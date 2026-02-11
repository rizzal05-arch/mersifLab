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

        // Define limits
        $GUEST_LIMIT = 3;           // Guest users: 3 questions
        $USER_LIMIT = null;         // Logged-in users: unlimited

        /**
         * =========================
         * CHECK LIMITS
         * =========================
         */
        if (!$userId) {
            // Guest user - check session limit
            $guestChatCount = AiChat::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->count();

            if ($guestChatCount >= $GUEST_LIMIT) {
                return response()->json([
                    'success' => false,
                    'message' => 'Batas 3 pertanyaan tercapai. Silakan login untuk melanjutkan.',
                    'require_login' => true
                ], 403);
            }
        } else {
            // Logged-in user - check if there's a limit
            if ($USER_LIMIT !== null) {
                $userChatCount = AiChat::where('user_id', $userId)
                    ->whereDate('created_at', today())
                    ->count();

                if ($userChatCount >= $USER_LIMIT) {
                    return response()->json([
                        'success' => false,
                        'message' => "Anda telah mencapai batas {$USER_LIMIT} pertanyaan per hari. Silakan coba lagi besok.",
                        'require_login' => false,
                        'daily_limit_reached' => true
                    ], 429);
                }
            }
        }

        try {
            /**
             * =========================
             * CALL GEMINI API
             * =========================
             */
            $response = Http::timeout(60)->withHeaders([
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
                        'temperature' => 0.7,
                        'maxOutputTokens' => 4096,
                        'topP' => 0.95,
                        'topK' => 40,
                        'stopSequences' => []
                    ],
                    'safetySettings' => [
                        [
                            'category' => 'HARM_CATEGORY_HARASSMENT',
                            'threshold' => 'BLOCK_NONE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_HATE_SPEECH',
                            'threshold' => 'BLOCK_NONE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                            'threshold' => 'BLOCK_NONE'
                        ],
                        [
                            'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                            'threshold' => 'BLOCK_NONE'
                        ]
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

            // Clean markdown formatting
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
             * CALCULATE REMAINING QUESTIONS
             * =========================
             */
            $remaining = null;
            $dailyUsed = null;

            if (!$userId) {
                // Guest user
                $used = AiChat::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->count();

                $remaining = max(0, 3 - $used);
                
                // Add limit warning to answer if user is running out of questions
                if ($remaining === 1) {
                    $answer .= "\n\n---\n\n⚠️ Ini adalah pertanyaan terakhir Anda. Login untuk mendapatkan akses unlimited ke Mersy AI Assistant!";
                } elseif ($remaining === 0) {
                    $answer .= "\n\n---\n\n🔒 Anda telah menggunakan semua 3 pertanyaan gratis. Login sekarang untuk melanjutkan percakapan dengan saya tanpa batas!";
                }
            } else {
                // Logged-in user
                $USER_LIMIT = null; // Same as above - unlimited
                
                if ($USER_LIMIT !== null) {
                    $used = AiChat::where('user_id', $userId)
                        ->whereDate('created_at', today())
                        ->count();
                    
                    $remaining = max(0, $USER_LIMIT - $used);
                    $dailyUsed = $used;
                    
                    // Warn if approaching daily limit
                    if ($remaining <= 5 && $remaining > 0) {
                        $answer .= "\n\n---\n\n⚠️ Anda memiliki {$remaining} pertanyaan tersisa hari ini.";
                    } elseif ($remaining === 0) {
                        $answer .= "\n\n---\n\n🔒 Anda telah mencapai batas harian. Silakan coba lagi besok!";
                    }
                } else {
                    // Unlimited for logged-in users
                    $remaining = null; // null means unlimited
                }
            }

            return response()->json([
                'success' => true,
                'answer' => $answer,
                'remaining_questions' => $remaining,
                'is_unlimited' => $userId && $USER_LIMIT === null,
                'daily_used' => $dailyUsed
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
Kamu adalah Mersy, AI Assistant untuk platform belajar MersifLab yang mengajarkan teknologi modern.

IDENTITAS MERSIFLAB:
MersifLab adalah platform pembelajaran teknologi (LMS - Learning Management System) yang menyediakan kursus dan materi edukasi di berbagai bidang:
- Internet of Things (IoT) - Keunggulan utama dan spesialisasi MersifLab
- Virtual Reality (VR) dan Augmented Reality (AR)
- Artificial Intelligence (AI) dan Machine Learning
- Web Development (Frontend, Backend, Full Stack)
- Mobile App Development
- Kategori lain dapat dicek di LMS MersifLab

FITUR LMS MERSIFLAB YANG TERSEDIA SAAT INI:
- Katalog kursus dengan berbagai kategori teknologi
- Video pembelajaran berkualitas tinggi
- Materi pembelajaran terstruktur (modul dan bab)
- Dashboard user untuk tracking progress
- Sertifikat digital setelah menyelesaikan kursus
- Akses materi 24/7 dari berbagai perangkat
- Mersy AI Assistant (kamu!) untuk bantuan belajar
- Program Become a Teacher untuk user yang ingin menjadi guru

PROGRAM MENJADI GURU DI MERSIFLAB:
Pengguna MersifLab dapat menjadi guru dan membuat kursus sendiri dengan mengikuti proses berikut:

Cara Menjadi Guru:
1. Login ke akun MersifLab Anda
2. Buka halaman Profil atau akses menu untuk program mengajar
3. Klik "Want to become a teacher?"
4. Isi formulir aplikasi dengan informasi lengkap Anda
5. Upload berkas persyaratan yang diminta (seperti CV, sertifikat, portfolio, atau dokumen kualifikasi lainnya)
6. Submit aplikasi dan tunggu proses review dari tim admin MersifLab
7. Admin akan mengevaluasi aplikasi dan berkas yang Anda kirimkan
8. Anda akan menerima notifikasi saat aplikasi diterima atau ditolak
9. Setelah disetujui, Anda mendapatkan akses untuk membuat dan mengelola kursus
10. Mulai buat kursus baru dan bagikan pengetahuan Anda dengan ribuan pembelajaran di MersifLab

Persyaratan Umum Menjadi Guru:
- Memiliki keahlian di bidang teknologi tertentu
- Pengalaman atau sertifikasi yang relevan
- Kemampuan mengajar yang baik dan komunikatif
- Komitmen untuk memberikan kualitas pembelajaran terbaik
- Mematuhi kode etik dan standar kualitas MersifLab

Keuntungan Menjadi Guru di MersifLab:
1. Berbagi pengetahuan dengan komunitas global
2. Mendapatkan akses ke tools dan resources pengajaran profesional
3. Potensi penghasilan dari kursus yang Anda buat
4. Membangun reputasi sebagai expert di bidang Anda
5. Dukungan tim support dari MersifLab

FITUR YANG SEDANG DIKEMBANGKAN (belum tersedia):
- Forum diskusi dan komunitas
- Project hands-on interaktif
- Kuis dan evaluasi otomatis
- Sistem mentor langsung
- Live class dan webinar

ATURAN MENJAWAB PERTANYAAN:

1. PERTANYAAN UMUM TEKNOLOGI (bukan tentang MersifLab):
   - Jawab pertanyaan dengan LENGKAP dan JELAS
   - Berikan informasi edukatif yang berguna
   - Fokus pada MENJAWAB pertanyaan user
   - Hanya sebutkan MersifLab di AKHIR sebagai tambahan jika topiknya relevan dengan kursus yang ada
   - Jangan paksa promosi MersifLab jika tidak relevan
   
   Contoh:
   Q: "Bagaimana cara belajar Python?"
   A: [Jawab lengkap tentang cara belajar Python] + [Opsional: sebutkan jika MersifLab punya kursus terkait]
   
   Q: "Apa itu manajemen proyek?"
   A: [Jawab lengkap tentang manajemen proyek] + [Jangan sebutkan MersifLab karena tidak relevan]

2. PERTANYAAN TENTANG MERSIFLAB:
   - Jawab dengan detail tentang platform, fitur, dan kursus
   - Tekankan IoT sebagai keunggulan utama
   - Jelaskan fitur yang tersedia vs yang sedang dikembangkan
   - Jujur tentang apa yang ada dan belum ada

ATURAN FORMAT PENTING:

1. JANGAN gunakan markdown (**bold**, *italic*, `, #)
2. JANGAN gunakan simbol formatting atau numbering pada main points
3. JANGAN gunakan angka 1., 2., 3. untuk main points

4. FORMAT STRUKTUR JAWABAN:
   
   A. POIN UTAMA DENGAN SUB-POIN (ada detail/langkah):
      - Tulis JUDUL POIN (tanpa angka) diikuti titik dua (:)
      - Baris berikutnya: list dengan bullet points (-)
      - Minimal 2 sub-poin
      
      Contoh:
      Pelajari Elektronika Dasar:
      - Pahami konsep tegangan dan arus
      - Kenali komponen seperti resistor dan LED
      - Praktik dengan breadboard

   B. POIN UTAMA TANPA SUB-POIN (langsung penjelasan):
      - Tulis langsung sebagai PARAGRAF
      - JANGAN pakai angka atau bullet
      - Jelaskan dalam 2-4 kalimat
      
      Contoh:
      Setelah menguasai dasar, mulailah dengan proyek sederhana seperti LED blinking. Proyek ini akan membantu Anda memahami cara kerja mikrokontroler dan pemrograman dasar. Dokumentasikan setiap langkah untuk pembelajaran di masa depan.

CONTOH JAWABAN LENGKAP YANG BENAR:

Pertanyaan: "Bagaimana cara belajar IoT untuk pemula?"

Untuk memulai belajar IoT, Anda perlu memahami beberapa fondasi penting dan mengikuti jalur pembelajaran yang terstruktur.

Pelajari Elektronika Dasar:
- Pahami konsep tegangan, arus, dan resistansi
- Kenali komponen dasar seperti resistor, LED, dan sensor
- Praktik dengan breadboard dan multimeter

Pilih Platform Mikrokontroler:
- Arduino: Ideal untuk pemula dengan komunitas besar
- ESP32: Bagus untuk proyek dengan WiFi/Bluetooth
- Raspberry Pi: Cocok untuk proyek yang butuh komputasi lebih

Mulailah dengan proyek sederhana seperti membuat LED berkedip atau membaca sensor suhu. Proyek-proyek dasar ini akan membantu Anda memahami cara kerja hardware dan software secara bersamaan.

Pelajari Protokol Komunikasi IoT:
- MQTT untuk messaging ringan
- HTTP/REST API untuk integrasi web
- CoAP untuk perangkat dengan resource terbatas

Bergabunglah dengan komunitas IoT online atau offline untuk berbagi pengalaman dan mendapat bantuan saat menghadapi kendala.

---

CONTOH FORMAT YANG SALAH (JANGAN SEPERTI INI):

1. Pelajari Elektronika Dasar
2. Pilih Platform
3. Mulai Proyek

^ SALAH karena pakai numbering

1. Pelajari Elektronika Dasar:
   - Pahami konsep dasar

^ SALAH karena pakai numbering DAN hanya 1 bullet

ATURAN PENTING:
- JANGAN pakai numbering (1., 2., 3.)
- Heading + bullets untuk poin dengan detail
- Paragraf untuk poin tanpa detail
- Pisahkan setiap section dengan 1 baris kosong

ATURAN KELENGKAPAN:
- JANGAN PERNAH berhenti di tengah kalimat atau list
- PASTIKAN semua poin selesai dijelaskan
- Minimal 150 kata untuk pertanyaan kompleks
- Maksimal 500 kata agar tetap fokus

Pertanyaan pengguna: {$message}

REMINDER CRITICAL:
- JANGAN pakai numbering (1., 2., 3.) di main points
- Poin dengan sub-detail: Heading + bullets (min 2 bullets)
- Poin tanpa sub-detail: Langsung paragraf
- Pisahkan sections dengan 1 baris kosong
- Selesaikan semua poin dengan lengkap
PROMPT;
    }

    /**
     * =========================
     * CLEAN MARKDOWN 
     * =========================
     */
    private function cleanMarkdown(string $text): string
    {
        // Remove all markdown formatting
        $text = preg_replace('/\*\*(.*?)\*\*/', '$1', $text); // Bold
        $text = preg_replace('/\*(.*?)\*/', '$1', $text);     // Italic
        $text = preg_replace('/`(.*?)`/', '$1', $text);       // Inline code
        $text = preg_replace('/```[\s\S]*?```/', '', $text);  // Code blocks
        $text = preg_replace('/#{1,6}\s*/', '', $text);       // Headers
        
        // Remove markdown links but keep the text
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
        
        // Remove any remaining backticks
        $text = str_replace('`', '', $text);
        
        // Normalize line breaks
        $text = preg_replace('/\r\n/', "\n", $text);
        $text = preg_replace('/\r/', "\n", $text);
        
        // Clean up excessive newlines
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
        $USER_LIMIT = null; // Set to number for daily limit, null for unlimited

        if ($userId) {
            // Logged-in user
            if ($USER_LIMIT !== null) {
                // Has daily limit
                $used = AiChat::where('user_id', $userId)
                    ->whereDate('created_at', today())
                    ->count();
                
                return response()->json([
                    'success' => true,
                    'is_authenticated' => true,
                    'remaining_questions' => max(0, $USER_LIMIT - $used),
                    'daily_limit' => $USER_LIMIT,
                    'daily_used' => $used,
                    'is_unlimited' => false
                ]);
            } else {
                // Unlimited
                return response()->json([
                    'success' => true,
                    'is_authenticated' => true,
                    'remaining_questions' => null,
                    'is_unlimited' => true
                ]);
            }
        }

        // Guest user
        $sessionId = Session::getId();
        $used = AiChat::where('session_id', $sessionId)
            ->whereNull('user_id')
            ->count();

        return response()->json([
            'success' => true,
            'is_authenticated' => false,
            'remaining_questions' => max(0, 3 - $used),
            'guest_limit' => 3,
            'is_unlimited' => false
        ]);
    }
}