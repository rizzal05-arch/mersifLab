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

        $userId = Auth::id();
        $sessionId = Session::getId();

        /**
         * =========================
         * LIMIT GUEST (MAX 3)
         * =========================
         */
        if (!$userId) {
            $guestChatCount = AiChat::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->count();

            if ($guestChatCount >= 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas 3 pertanyaan tercapai. Silakan login untuk melanjutkan.',
                    'require_login' => true
                ], 403);
            }
        }

        try {
            /**
             * =========================
             * CALL GEMINI API
             * =========================
             */
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])->post(
                'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . config('services.gemini.key'),
                [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $this->buildPrompt($request->message)]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.5,
                        'maxOutputTokens' => 1500,
                        'topP' => 0.9,
                        'topK' => 40
                    ]
                ]
            );

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghubungi AI',
                    'error' => $response->json()
                ], 500);
            }

            /**
             * =========================
             * PARSE RESPONSE
             * =========================
             */
            $data = $response->json();

            $answer = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

            if (!$answer) {
                $answer = 'Maaf, saya belum dapat menjawab pertanyaan tersebut.';
            }

            // 🔥 CLEAN MARKDOWN TOTAL
            $answer = $this->cleanMarkdown($answer);

            /**
             * =========================
             * SAVE CHAT
             * =========================
             */
            AiChat::create([
                'user_id' => $userId,
                'session_id' => $userId ? null : $sessionId,
                'question' => $request->message,
                'answer' => $answer
            ]);

            /**
             * =========================
             * SISA LIMIT GUEST
             * =========================
             */
            $remaining = null;

            if (!$userId) {
                $used = AiChat::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->count();

                $remaining = max(0, 3 - $used);
            }

            return response()->json([
                'success' => true,
                'answer' => $answer,
                'remaining_questions' => $remaining
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * =========================
     * PROMPT BUILDER (FIXED)
     * =========================
     */
    private function buildPrompt(string $message): string
    {
        return <<<PROMPT
Anda adalah Mersi, AI Assistant untuk LMS Mersiflab.

Aturan jawaban:
- Jangan memperkenalkan diri kecuali diminta.
- Jangan menggunakan simbol markdown seperti **, *, atau bullet aneh.
- Jawaban harus jelas, runtut, dan langsung ke inti.
- Gunakan bahasa Indonesia yang natural dan edukatif.
- Jawaban harus selesai dan tidak terpotong.

Pertanyaan:
{$message}
PROMPT;
    }

    /**
     * =========================
     * CLEAN MARKDOWN (WAJIB)
     * =========================
     */
    private function cleanMarkdown(string $text): string
    {
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text);
        $text = preg_replace('/\*(.*?)\*/', '$1', $text);
        $text = preg_replace('/`(.*?)`/', '$1', $text);
        $text = preg_replace('/#{1,6}\s*/', '', $text);
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * =========================
     * CHAT HISTORY
     * =========================
     */
    public function getHistory()
    {
        $userId = Auth::id();
        $sessionId = Session::getId();

        $chats = AiChat::where(function ($query) use ($userId, $sessionId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } else {
                    $query->where('session_id', $sessionId)
                          ->whereNull('user_id');
                }
            })
            ->orderBy('created_at', 'asc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'chats' => $chats
        ]);
    }

    /**
     * =========================
     * CHECK LIMIT
     * =========================
     */
    public function checkLimit()
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

        $used = AiChat::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->count();

        return response()->json([
            'success' => true,
            'is_authenticated' => false,
            'remaining_questions' => max(0, 3 - $used)
        ]);
    }
}