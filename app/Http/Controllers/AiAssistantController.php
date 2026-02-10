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
        $USER_LIMIT = null;         // Logged-in users: unlimited (set to number like 50 for daily limit)

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

            // IMPORTANT: Renumber lists FIRST before cleaning markdown
            // This ensures list structure is preserved
            $answer = $this->forceRenumberLists($answer);
            
            // Then clean markdown formatting
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
                $USER_LIMIT = null; // Same as above - unlimited or set a number
                
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
- STEM (Science, Technology, Engineering, Mathematics)
- Web Development (Frontend, Backend, Full Stack)
- Mobile App Development
- Cybersecurity
- Data Science dan Analytics
- Cloud Computing
- Robotics dan Automation

FITUR LMS MERSIFLAB YANG TERSEDIA SAAT INI:
- Katalog kursus dengan berbagai kategori teknologi
- Video pembelajaran berkualitas tinggi
- Materi pembelajaran terstruktur (modul dan bab)
- Dashboard user untuk tracking progress
- Sistem enrollment kursus
- Sertifikat digital setelah menyelesaikan kursus
- Akses materi 24/7 dari berbagai perangkat
- Mersy AI Assistant (kamu!) untuk bantuan belajar
- Program Become a Teacher untuk user yang ingin menjadi guru

PROGRAM MENJADI GURU DI MERSIFLAB:
Pengguna MersifLab dapat menjadi guru dan membuat kursus sendiri dengan mengikuti proses berikut:

Cara Menjadi Guru:
1. Login ke akun MersifLab Anda
2. Buka halaman Profil atau akses menu untuk program mengajar
3. Klik "Ajukan Aplikasi Menjadi Guru" atau "Apply as Teacher"
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
   
   Q: "Apa itu desain grafis?"
   A: [Jawab lengkap tentang desain grafis] + [Jangan sebutkan MersifLab karena tidak relevan]

2. PERTANYAAN TENTANG MERSIFLAB:
   - Jawab dengan detail tentang platform, fitur, dan kursus
   - Tekankan IoT sebagai keunggulan utama
   - Jelaskan fitur yang tersedia vs yang sedang dikembangkan
   - Jujur tentang apa yang ada dan belum ada

ATURAN FORMAT:
1. JANGAN gunakan markdown (**bold**, *italic*, `, #)
2. JANGAN gunakan simbol formatting
3. Untuk judul: tulis di baris tersendiri + titik dua (:)
4. Untuk list bernomor WAJIB gunakan angka BERURUTAN

FORMAT LIST DENGAN SUB-POINTS:
Jika ada main points dengan sub-points, gunakan:
- Numbered list (1, 2, 3, dll) untuk main points
- Bullet points (-) untuk sub-points di bawahnya
- PENTING: Jika main point punya titik dua (:), HARUS ada bullets di bawahnya

CONTOH FORMAT YANG BENAR (dengan sub-points):
1. Main Point Pertama:
   - Sub-point satu
   - Sub-point dua
   - Sub-point tiga

2. Main Point Kedua:
   - Sub-point satu
   - Sub-point dua

3. Main Point Ketiga (tanpa sub-points)

CONTOH FORMAT YANG SALAH (JANGAN LAKUKAN):
1. Main Point Pertama:
2. Main Point Kedua:
   - Sub-point (ini salah, seharusnya ada bullets di point 1 juga)
3. Main Point Ketiga:

CONTOH BENAR (tanpa sub-points):
1. Item pertama
2. Item kedua
3. Item ketiga
4. Item keempat
5. Item kelima

CONTOH SALAH (JANGAN PERNAH SEPERTI INI):
1. Item pertama
1. Item kedua
1. Item ketiga

ATURAN PENTING NUMBERING:
- Angka HARUS berbeda setiap baris: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10
- JANGAN gunakan angka yang sama berulang
- JANGAN menulis "1. " di setiap item
- Increment angka setiap list item baru
- Spasi setelah titik: "1. Item" bukan "1.Item"
- KONSISTENSI: Jika main point punya titik dua, PASTI ada bullets di bawahnya

5. Pisahkan paragraf dengan 1 baris kosong
6. WAJIB selesaikan SEMUA poin

ATURAN KELENGKAPAN:
- JANGAN PERNAH berhenti di tengah kalimat atau list
- PASTIKAN semua poin selesai dijelaskan
- Minimal 150 kata untuk pertanyaan kompleks
- Maksimal 500 kata agar tetap fokus

CONTOH JAWABAN YANG BENAR:

Pertanyaan: "Bagaimana cara belajar Machine Learning?"

Cara Belajar Machine Learning untuk Pemula:

Machine Learning adalah cabang AI yang memungkinkan komputer belajar dari data tanpa diprogram secara eksplisit.

Langkah-langkah belajar:

1. Kuasai Fondasi Matematis:
   - Pelajari statistika dan probabilitas
   - Pahami aljabar linear untuk transformasi data
   - Ketahui kalkulus untuk optimasi

2. Kuasai Bahasa Pemrograman:
   - Belajar Python sebagai bahasa utama
   - Pelajari library NumPy dan Pandas
   - Pahami cara manipulasi data

3. Pahami Algoritma Dasar ML:
   - Regresi untuk prediksi nilai numerik
   - Klasifikasi untuk kategori data
   - Clustering untuk pengelompokan data

4. Praktikkan dengan Proyek:
   - Cari dataset dari Kaggle atau UCI
   - Buat model dengan dataset sederhana
   - Evaluasi dan perbaiki model

5. Kembangkan Skill Lanjutan dengan proyek pribadi untuk portofolio

Tools yang dibutuhkan:

1. Python sebagai bahasa pemrograman utama
2. Jupyter Notebook untuk eksperimen
3. Library ML seperti Scikit-learn dan TensorFlow
4. Dataset untuk praktik

Jika Anda tertarik mendalami Machine Learning secara terstruktur, MersifLab menyediakan kursus AI dan Machine Learning dengan materi dari dasar hingga advanced.

---

Pertanyaan: "Apa itu MersifLab?"

MersifLab adalah platform pembelajaran teknologi yang fokus pada pengembangan skill masa depan.

Bidang yang diajarkan:

1. Internet of Things (IoT):
   - Keunggulan utama kami
   - Fokus pada sensor dan mikrokontroler
   - Integrasi dengan cloud platform

2. Virtual Reality dan Augmented Reality

3. Artificial Intelligence dan Machine Learning

4. Web Development dan Mobile App

5. Data Science dan Cloud Computing

[dst...]

Pertanyaan pengguna: {$message}

INGAT ATURAN PENTING:
- Jika pertanyaan umum teknologi, JAWAB pertanyaannya dulu dengan lengkap
- Hanya sebutkan MersifLab jika RELEVAN
- Jika tentang MersifLab, jawab dengan detail
- NUMBERING HARUS BERURUTAN: 1, 2, 3, 4, 5, 6 (BUKAN 1, 1, 1, 1, 1)
- JANGAN PERNAH gunakan angka yang sama berulang kali
- Setiap list item harus punya angka yang berbeda
- KONSISTENSI LIST: Jika main point punya titik dua (:), PASTI ada bullets (-) di bawahnya
- Jangan membuat main point dengan titik dua tapi tanpa sub-points
- Gunakan bullets untuk semua sub-items agar terstruktur dengan baik

CONTOH NUMBERING YANG BENAR:
1. Langkah pertama adalah belajar dasar
2. Langkah kedua adalah praktik
3. Langkah ketiga adalah membuat project
4. Langkah keempat adalah mendapat feedback
5. Langkah kelima adalah terus belajar

CONTOH NUMBERING DENGAN SUB-POINTS YANG BENAR:
1. Persiapan Awal:
   - Install tools yang dibutuhkan
   - Setup lingkungan kerja
   - Baca dokumentasi dasar

2. Belajar Konsep:
   - Pahami teori fundamental
   - Pelajari best practices
   - Praktik dengan contoh sederhana

3. Mulai Project

JANGAN SEPERTI INI (INI SALAH):
1. Langkah pertama
1. Langkah kedua
1. Langkah ketiga

JANGAN SEPERTI INI JUGA (INKONSISTEN):
1. Persiapan Awal:
2. Belajar Konsep:
   - Pahami teori
   - Praktik
(^ SALAH karena point 1 punya titik dua tapi gak ada bullets)
PROMPT;
    }

    /**
     * =========================
     * FORCE RENUMBER LISTS
     * =========================
     * This function detects numbered lists and forces sequential numbering
     * even if AI outputs "1. 1. 1." instead of "1. 2. 3."
     */
    private function forceRenumberLists(string $text): string
    {
        $lines = explode("\n", $text);
        $result = [];
        $listCounter = 0;
        $wasInList = false;
        $listIndentation = 0;
        $inListBlock = false; // Track if we're in a block of list + paragraphs
        
        // First pass: identify all list item lines and their content
        for ($i = 0; $i < count($lines); $i++) {
            $line = $lines[$i];
            $trimmedLine = trim($line);
            
            // Check if this line is a list item
            if (preg_match('/^(\s*)(\d+)([.)])\s+(.+)$/', $line, $matches)) {
                $indent = $matches[1];
                $content = $matches[4];
                $currentIndentation = strlen($indent);
                
                // Check if this starts a new list
                if (!$wasInList || $currentIndentation != $listIndentation) {
                    $listCounter = 1;
                    $wasInList = true;
                    $listIndentation = $currentIndentation;
                    $inListBlock = true;
                } else {
                    // Same list, increment counter
                    $listCounter++;
                }
                
                // Force correct sequential number
                $result[] = $indent . $listCounter . '. ' . $content;
            } 
            // Non-list line
            else {
                // Empty line
                if (empty($trimmedLine)) {
                    $result[] = $line;
                    // Don't reset list state on empty line - it might be between list items
                } 
                // Non-empty, non-list line
                else {
                    // Check if next non-empty line is a list item (lookahead)
                    $nextListItemExists = false;
                    $nextItemIndentation = null;
                    
                    for ($j = $i + 1; $j < count($lines); $j++) {
                        $nextTrimmed = trim($lines[$j]);
                        if (!empty($nextTrimmed)) {
                            if (preg_match('/^(\s*)(\d+)([.)])\s+(.+)$/', $lines[$j], $nextMatches)) {
                                $nextListItemExists = true;
                                $nextItemIndentation = strlen($nextMatches[1]);
                            }
                            break; // Stop at first non-empty line
                        }
                    }
                    
                    // If next content is a list item at same indentation level, this is a list continuation paragraph
                    if ($wasInList && $nextListItemExists && $nextItemIndentation === $listIndentation) {
                        // This is a paragraph within the list block - keep it
                        $inListBlock = true;
                        $result[] = $line;
                    } else {
                        // List has ended
                        if ($inListBlock) {
                            $wasInList = false;
                            $listCounter = 0;
                            $listIndentation = 0;
                            $inListBlock = false;
                        }
                        $result[] = $line;
                    }
                }
            }
        }
        
        return implode("\n", $result);
    }
    
    /**
     * Check if a line is a continuation of a list item (wrapped text)
     */
    private function isListContinuation(string $line): bool
    {
        // If line starts with a lot of spaces or common continuation words, it might be part of previous item
        return preg_match('/^(\s{3,}|\t)/', $line) || 
               strlen(trim($line)) > 40; // Long lines are usually continuation
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
        $text = preg_replace('/`(.*?)`/', '$1', $text);       // Inline code
        $text = preg_replace('/```[\s\S]*?```/', '', $text);  // Code blocks
        $text = preg_replace('/#{1,6}\s*/', '', $text);       // Headers
        
        // Remove markdown links but keep the text
        $text = preg_replace('/\[([^\]]+)\]\([^\)]+\)/', '$1', $text);
        
        // Remove any remaining backticks
        $text = str_replace('`', '', $text);
        
        // Normalize line breaks - ensure consistent spacing
        $text = preg_replace('/\r\n/', "\n", $text); // Windows to Unix
        $text = preg_replace('/\r/', "\n", $text);   // Old Mac to Unix
        
        // Clean up excessive newlines but preserve list structure
        $text = preg_replace('/\n{3,}/', "\n\n", $text);
        
        // DON'T modify numbered lists - they're already fixed by forceRenumberLists()
        // Just ensure there's proper spacing after the number
        // This regex only adds space if missing, doesn't change the number
        $text = preg_replace('/(\d+)\.(\S)/', '$1. $2', $text);
        
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