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
     * PROMPT BUILDER (IMPROVED)
     * =========================
     */
    private function buildPrompt(string $message): string
    {
        return <<<PROMPT
Anda adalah Mersi, AI Assistant untuk LMS Mersiflab yang fokus pada edukasi teknologi AI, IoT, VR, dan STEM.

ATURAN PENTING FORMAT JAWABAN:
1. JANGAN gunakan markdown (**bold**, *italic*, `, #heading)
2. JANGAN gunakan simbol formatting apapun
3. Gunakan format plain text dengan struktur yang jelas:
   - Untuk judul/topik: tulis di baris tersendiri diakhiri dengan tanda titik dua (:)
   - Untuk list bernomor: gunakan format "1. ", "2. ", "3. "
   - Untuk bullet point: gunakan tanda "- " di awal
   - Untuk paragraf: tulis dalam kalimat lengkap dengan baris kosong sebagai pemisah

4. Berikan jawaban yang terstruktur dan mudah dipahami
5. Jangan memperkenalkan diri kecuali diminta
6. Jawaban harus lengkap dan tidak terpotong
7. Gunakan bahasa Indonesia yang natural dan edukatif

CONTOH FORMAT YANG BENAR:

Machine Learning:
Machine learning adalah cabang dari AI yang memungkinkan komputer belajar dari data.

Langkah-langkah belajar Machine Learning:
1. Pelajari dasar Python dan matematika
2. Pahami konsep algoritma ML
3. Praktik dengan dataset sederhana

Keuntungan utama:
- Otomasi pengambilan keputusan
- Prediksi yang akurat
- Hemat waktu dan biaya

Pertanyaan pengguna:
{$message}
PROMPT;
    }

    /**
     * =========================
     * CLEAN MARKDOWN (IMPROVED)
     * =========================
     */
    private function cleanMarkdown(string $text): string
    {
        // Remove all markdown formatting
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text); // Bold
        $text = preg_replace('/\*(.*?)\*/', '$1', $text);     // Italic
        $text = preg_replace('/`(.*?)`/', '$1', $text);       // Code
        $text = preg_replace('/#{1,6}\s*/', '', $text);       // Headers
        
        // Remove markdown links but keep the text
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
        
        // Clean up excessive newlines but keep paragraph structure
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // Remove any remaining backticks
        $text = str_replace('`', '', $text);
        
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