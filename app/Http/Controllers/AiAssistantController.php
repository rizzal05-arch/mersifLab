<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class AiAssistantController extends Controller
{
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        // Cek apakah user login atau guest
        $userId = Auth::id();
        $sessionId = Session::getId();

        // Batasi guest hanya 3 pertanyaan
        if (!$userId) {
            $guestChatCount = AiChat::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->count();

            if ($guestChatCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda telah mencapai batas 3 pertanyaan. Silakan login untuk melanjutkan.',
                    'require_login' => true
                ], 403);
            }
        }

        // Panggil Gemini API
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key=' . config('services.gemini.key'), [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $this->buildPrompt($request->message)
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Maaf, saya tidak dapat memproses pertanyaan Anda.';

                // Simpan ke database
                AiChat::create([
                    'user_id' => $userId,
                    'session_id' => $userId ? null : $sessionId,
                    'question' => $request->message,
                    'answer' => $answer
                ]);

                // Hitung sisa pertanyaan untuk guest
                $remainingQuestions = null;
                if (!$userId) {
                    $usedQuestions = AiChat::where('session_id', $sessionId)
                        ->whereNull('user_id')
                        ->count();
                    $remainingQuestions = 3 - $usedQuestions;
                }

                return response()->json([
                    'success' => true,
                    'answer' => $answer,
                    'remaining_questions' => $remainingQuestions
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghubungi AI.'
            ], 500);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    private function buildPrompt($message)
    {
        return "Anda adalah Mersi AI Assistant, asisten virtual untuk LMS Mersiflab yang membantu siswa dan guru dalam pembelajaran AI, IoT, VR, dan STEM. " .
               "Jawab pertanyaan berikut dengan ramah, informatif, dan dalam Bahasa Indonesia:\n\n" .
               $message;
    }

    public function getHistory(Request $request)
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $chats = AiChat::where(function($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId)
                      ->whereNull('user_id');
            }
        })
        ->orderBy('created_at', 'desc')
        ->take(20)
        ->get();

        return response()->json([
            'success' => true,
            'chats' => $chats
        ]);
    }

    public function checkLimit(Request $request)
    {
        $userId = Auth::id();
        
        if ($userId) {
            return response()->json([
                'success' => true,
                'is_authenticated' => true,
                'remaining_questions' => null
            ]);
        }

        $sessionId = Session::getId();
        $usedQuestions = AiChat::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->count();

        return response()->json([
            'success' => true,
            'is_authenticated' => false,
            'remaining_questions' => 3 - $usedQuestions
        ]);
    }
}