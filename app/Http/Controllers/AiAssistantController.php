<?php

namespace App\Http\Controllers;

use App\Models\AiChat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;

class AiAssistantController extends Controller
{
    public function chat(Request $request)
    {
        // Basic validation for message first; files validated conditionally below
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $userId = Auth::id();
        $sessionId = Session::getId();

        // Determine user state and applicable limits
        $user = Auth::user();

        // Default settings
        $guestLimit = 3;
        $dailyLimit = null; // null = unlimited for this user
        $allowFiles = false;

        if (!$user) {
            // Guest user
            $dailyLimit = $guestLimit;
            $allowFiles = false;
        } else {
            // Logged-in user: check subscription and owned/enrolled courses
            $hasSubscription = $user->hasActiveSubscription();
            $plan = strtolower($user->subscription_plan ?? '');

            // Does user have any enrolled classes/courses?
            $hasCourse = $user->enrolledClasses()->exists() || $user->classes()->exists();

            if ($hasSubscription) {
                // Subscribers: unlimited
                $dailyLimit = null;
                $allowFiles = ($plan === 'premium');
            } else {
                // Not subscribed: limits depend on whether user has courses
                if ($hasCourse) {
                    $dailyLimit = 15;
                } else {
                    $dailyLimit = 5;
                }
                $allowFiles = false;
            }
        }

        // If files are present but user is not allowed to upload, reject
        if ($request->hasFile('files') && !$allowFiles) {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pengguna subscription premium yang dapat mengirim file.',
            ], 403);
        }

        // If files are allowed, validate them (accept either 'files' array or single 'file')
        if ($allowFiles) {
            $rules = [
                'files.*' => 'file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt',
                'file' => 'file|max:5120|mimes:jpg,jpeg,png,pdf,doc,docx,txt'
            ];

            $request->validate($rules);
        }

        // Enforce daily/session limits
        if (!$user) {
            $guestChatCount = AiChat::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->count();

            if ($guestChatCount >= $dailyLimit) {
                return response()->json([
                    'success' => false,
                    'message' => "Batas {$dailyLimit} pertanyaan tercapai. Silakan login untuk melanjutkan.",
                    'require_login' => true
                ], 403);
            }
        } else {
            if ($dailyLimit !== null) {
                $userChatCount = AiChat::where('user_id', $userId)
                    ->whereDate('created_at', today())
                    ->count();

                if ($userChatCount >= $dailyLimit) {
                    return response()->json([
                        'success' => false,
                        'message' => "Anda telah mencapai batas {$dailyLimit} pertanyaan per hari. Silakan coba lagi besok.",
                        'daily_limit_reached' => true
                    ], 429);
                }
            }
        }

        try {
            // If files uploaded (premium), store them first and attempt to extract text
            $savedFiles = [];
            $extractedFiles = []; // ['filename' => 'extracted text']

            if ($allowFiles) {
                $uploaded = $request->file('files') ?? $request->file('file');

                if ($uploaded) {
                    if (!is_array($uploaded)) {
                        $uploaded = [$uploaded];
                    }

                    foreach ($uploaded as $file) {
                        if (!$file) continue;
                        $path = $file->store('ai_attachments', 'public');
                        $savedFiles[] = $path;

                        // Try to extract text from the saved file when possible
                        $fullPath = storage_path('app/public/' . $path);
                        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                        $extracted = $this->extractTextFromFile($fullPath, $ext);
                        if ($extracted) {
                            $extractedFiles[$path] = $extracted;
                        }
                    }
                }
            }

            // Build prompt including file contents (if any)
            $basePrompt = $this->buildPrompt($request->message);
            $fullPrompt = $basePrompt;
            if (!empty($extractedFiles)) {
                $fullPrompt .= "\n\nAttached files contents:\n";
                foreach ($extractedFiles as $fname => $content) {
                    // limit each file content to prevent huge prompts
                    $snippet = mb_substr($content, 0, 3500);
                    $fullPrompt .= "File: {$fname}\n" . $snippet . "\n\n";
                }
            }

            // If files were uploaded but we couldn't extract any text, inform user and skip calling external AI.
            if (!empty($savedFiles) && empty($extractedFiles)) {
                $answer = "Maaf, saya tidak dapat memproses atau membaca file yang Anda lampirkan secara otomatis dari server saat ini.";
                $answer .= "\n\nKemungkinan server belum memiliki tools OCR (tesseract/pdftotext) atau file tidak dapat diekstrak. \nSilakan ketik pertanyaan atau transkrip teks dari file tersebut agar saya dapat membantu menjawabnya.";
                $answer .= "\n\nAttachments: " . implode(', ', $savedFiles);

                // Save chat and return with special flag
                AiChat::create([
                    'user_id' => $userId,
                    'session_id' => $userId ? null : $sessionId,
                    'question' => $request->message,
                    'answer' => $answer
                ]);

                return response()->json([
                    'success' => true,
                    'answer' => $answer,
                    'remaining_questions' => null,
                    'is_unlimited' => $userId && $dailyLimit === null,
                    'allow_file_upload' => $allowFiles,
                    'attachments_processed' => false
                ]);
            }

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
                                ['text' => $fullPrompt]
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
            // If files were saved earlier, append filenames to the answer metadata
            if (!empty($savedFiles)) {
                $answer .= "\n\n---\n\nAttachments: " . implode(', ', $savedFiles);
            }

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
                $used = AiChat::where('session_id', $sessionId)
                    ->whereNull('user_id')
                    ->count();

                $remaining = max(0, $dailyLimit - $used);

                if ($remaining === 1) {
                    $answer .= "\n\n---\n\n⚠️ Ini adalah pertanyaan terakhir Anda. Login untuk mendapatkan akses lebih ke Mersy AI Assistant!";
                } elseif ($remaining === 0) {
                    $answer .= "\n\n---\n\n🔒 Anda telah menggunakan semua pertanyaan gratis. Login sekarang untuk melanjutkan percakapan dengan saya!";
                }
            } else {
                if ($dailyLimit !== null) {
                    $used = AiChat::where('user_id', $userId)
                        ->whereDate('created_at', today())
                        ->count();

                    $remaining = max(0, $dailyLimit - $used);
                    $dailyUsed = $used;

                    if ($remaining <= 5 && $remaining > 0) {
                        $answer .= "\n\n---\n\n⚠️ Anda memiliki {$remaining} pertanyaan tersisa hari ini.";
                    } elseif ($remaining === 0) {
                        $answer .= "\n\n---\n\n🔒 Anda telah mencapai batas harian. Silakan coba lagi besok!";
                    }
                } else {
                    // Unlimited
                    $remaining = null;
                }
            }

            return response()->json([
                'success' => true,
                'answer' => $answer,
                'remaining_questions' => $remaining,
                'is_unlimited' => $userId && $dailyLimit === null,
                'allow_file_upload' => $allowFiles,
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
     * Attempt to extract text content from uploaded file when possible.
     * Supports: txt, docx, pdf (if pdftotext installed), images (jpg/png via tesseract if installed).
     */
    private function extractTextFromFile(string $fullPath, string $ext): ?string
    {
        try {
            if (!file_exists($fullPath)) return null;

            $ext = strtolower($ext);

            if (in_array($ext, ['txt'])) {
                $content = file_get_contents($fullPath);
                return $content ?: null;
            }

            if ($ext === 'docx') {
                $zip = new \ZipArchive();
                if ($zip->open($fullPath) === true) {
                    $index = $zip->locateName('word/document.xml');
                    if ($index !== false) {
                        $data = $zip->getFromIndex($index);
                        $zip->close();
                        // Strip XML tags
                        $text = strip_tags($data);
                        return $text ?: null;
                    }
                    $zip->close();
                }
                return null;
            }

            if ($ext === 'pdf') {
                // Try to use pdftotext if available
                if (function_exists('shell_exec')) {
                    $cmd = 'pdftotext ' . escapeshellarg($fullPath) . ' -';
                    $out = @shell_exec($cmd . ' 2>&1');
                    if ($out && !str_contains(strtolower($out), 'not found')) {
                        return $out;
                    }
                }
                return null;
            }

            if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                // Try tesseract OCR if available
                if (function_exists('shell_exec')) {
                    $cmd = 'tesseract ' . escapeshellarg($fullPath) . ' stdout';
                    $out = @shell_exec($cmd . ' 2>&1');
                    if ($out && !str_contains(strtolower($out), 'not found')) {
                        return $out;
                    }
                }
                return null;
            }

            return null;
        } catch (\Throwable $e) {
            return null;
        }
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
        $user = Auth::user();

        $guestLimit = 3;
        $dailyLimit = null;
        $allowFiles = false;

        if (!$user) {
            $sessionId = Session::getId();
            $used = AiChat::where('session_id', $sessionId)
                ->whereNull('user_id')
                ->count();

            return response()->json([
                'success' => true,
                'is_authenticated' => false,
                'remaining_questions' => max(0, $guestLimit - $used),
                'daily_limit' => $guestLimit,
                'daily_used' => $used,
                'is_unlimited' => false,
                'allow_file_upload' => false,
            ]);
        }

        // Logged-in user
        $hasSubscription = $user->hasActiveSubscription();
        $plan = strtolower($user->subscription_plan ?? '');
        $hasCourse = $user->enrolledClasses()->exists() || $user->classes()->exists();

        if ($hasSubscription) {
            $dailyLimit = null;
            $allowFiles = ($plan === 'premium');
        } else {
            $dailyLimit = $hasCourse ? 15 : 5;
            $allowFiles = false;
        }

        $used = AiChat::where('user_id', $user->id)
            ->whereDate('created_at', today())
            ->count();

        return response()->json([
            'success' => true,
            'is_authenticated' => true,
            'remaining_questions' => $dailyLimit === null ? null : max(0, $dailyLimit - $used),
            'daily_limit' => $dailyLimit,
            'daily_used' => $used,
            'is_unlimited' => $dailyLimit === null,
            'allow_file_upload' => $allowFiles,
            'subscription_plan' => $user->subscription_plan
        ]);
    }
}