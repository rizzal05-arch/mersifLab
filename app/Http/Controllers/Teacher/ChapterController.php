<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\ClassModel;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\DB;

/**
 * ChapterController - Manage chapters (bab) dalam class
 * 
 * Teacher hanya bisa manage chapter di class miliknya sendiri
 */
class ChapterController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show list of chapters untuk class tertentu
     */
    public function index(ClassModel $class)
    {
        // Pastikan class milik teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This class does not belong to you.');
        }

        $chapters = $class->chapters()->get();

        return view('teacher.chapters.index', compact('class', 'chapters'));
    }

    /**
     * Show form create chapter
     */
    public function create(ClassModel $class)
    {
        // Pastikan class milik teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This class does not belong to you.');
        }

        return view('teacher.chapters.create', compact('class'));
    }

    /**
     * Show chapter dengan modules
     */
    public function show(ClassModel $class, Chapter $chapter)
    {
        // Pastikan class milik teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This class does not belong to you.');
        }

        // Pastikan chapter milik class yang dimaksud
        if ($chapter->class_id !== $class->id) {
            abort(404, 'Chapter not found in this class.');
        }

        // Load chapters dengan modules
        $chapter->load(['modules' => function($query) {
            $query->orderBy('order', 'asc');
        }]);

        return view('teacher.chapters.show', compact('class', 'chapter'));
    }

    /**
     * Store chapter baru
     */
    public function store(Request $request, ClassModel $class)
    {
        // Pastikan class milik teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && $class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized. This class does not belong to you.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ], [
            'description.required' => 'Chapter Description tidak boleh kosong',
        ]);

        $chapter = $class->chapters()->create($validated);

        // Pastikan class duration ter-update setelah create chapter
        $chapter->refresh();
        if ($chapter->class) {
            $chapter->class->recalculateTotalDuration();
        }

        // Notifikasi ke semua student yang sudah enroll di course ini (jika chapter dipublish dan mereka mengaktifkan notifikasi)
        if ($chapter->is_published) {
            $enrolledStudents = DB::table('class_student')
                ->where('class_id', $class->id)
                ->join('users', 'class_student.user_id', '=', 'users.id')
                ->where('users.role', 'student')
                ->select('users.id')
                ->get();

            foreach ($enrolledStudents as $student) {
                $user = \App\Models\User::find($student->id);
                if ($user && $user->wantsNotification('new_chapter')) {
                    Notification::create([
                        'user_id' => $student->id,
                        'type' => 'new_chapter',
                        'title' => 'Chapter Baru Tersedia',
                        'message' => "Chapter baru '{$chapter->title}' telah ditambahkan ke course '{$class->name}' yang Anda ikuti.",
                        'notifiable_type' => Chapter::class,
                        'notifiable_id' => $chapter->id,
                    ]);
                }
            }
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil menambahkan chapter');
    }

    /**
     * Show form edit chapter
     */
    public function edit(ClassModel $class, Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && ($chapter->class_id !== $class->id || $class->teacher_id !== auth()->id())) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        return view('teacher.chapters.edit', compact('class', 'chapter'));
    }

    /**
     * Update chapter
     */
    public function update(Request $request, ClassModel $class, Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && ($chapter->class_id !== $class->id || $class->teacher_id !== auth()->id())) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ], [
            'description.required' => 'Chapter Description tidak boleh kosong',
        ]);

        $wasPublished = $chapter->is_published;
        $chapter->update($validated);
        $chapter->refresh();

        // Pastikan class duration ter-update setelah update chapter
        if ($chapter->class) {
            $chapter->class->recalculateTotalDuration();
        }

        // Notifikasi ke semua student yang sudah enroll di course ini (jika chapter baru dipublish)
        if (!$wasPublished && $chapter->is_published) {
            $enrolledStudents = DB::table('class_student')
                ->where('class_id', $class->id)
                ->join('users', 'class_student.user_id', '=', 'users.id')
                ->where('users.role', 'student')
                ->select('users.id')
                ->get();

            foreach ($enrolledStudents as $student) {
                $user = \App\Models\User::find($student->id);
                if ($user && $user->wantsNotification('new_chapter')) {
                    Notification::create([
                        'user_id' => $student->id,
                        'type' => 'new_chapter',
                        'title' => 'Chapter Baru Tersedia',
                        'message' => "Chapter baru '{$chapter->title}' telah ditambahkan ke course '{$class->name}' yang Anda ikuti.",
                        'notifiable_type' => Chapter::class,
                        'notifiable_id' => $chapter->id,
                    ]);
                }
            }
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil memperbarui chapter');
    }

    /**
     * Delete chapter
     */
    public function destroy(ClassModel $class, Chapter $chapter)
    {
        // Pastikan chapter milik class yang dimiliki teacher yang sedang login atau admin
        if (!auth()->user()->isAdmin() && ($chapter->class_id !== $class->id || $class->teacher_id !== auth()->id())) {
            abort(403, 'Unauthorized. This chapter does not belong to you.');
        }

        // Simpan class_id sebelum delete
        $classId = $chapter->class_id;

        $chapter->delete();

        // Pastikan class duration ter-update setelah delete chapter
        if ($classId) {
            $class = ClassModel::find($classId);
            if ($class) {
                $class->recalculateTotalDuration();
            }
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil menghapus chapter');
    }

    /**
     * Reorder chapters
     */
    public function reorder(Request $request, ClassModel $class)
    {
        $this->authorize('updateClass', $class);

        $validated = $request->validate([
            'chapters' => 'required|array',
            'chapters.*.id' => 'required|exists:chapters,id',
            'chapters.*.order' => 'required|integer',
        ]);

        foreach ($validated['chapters'] as $chapter) {
            Chapter::find($chapter['id'])->update(['order' => $chapter['order']]);
        }

        return response()->json(['message' => 'Chapters reordered successfully']);
    }
}
