<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Category;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * ClassController - Manage classes (kursus/pembelajaran)
 * 
 * Teacher hanya bisa manage class miliknya sendiri
 */
class ClassController extends Controller
{
    use AuthorizesRequests;
    /**
     * Show list of classes milik teacher
     */
    public function index()
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        $classes = ClassModel::byTeacher(auth()->id())
            ->orderBy('order')
            ->get();

        return view('teacher.classes.index', compact('classes'));
    }

    /**
     * Show form create class
     */
    public function create()
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        $categories = Category::active()->ordered()->get();
        return view('teacher.classes.create', compact('categories'));
    }

    /**
     * Store class baru
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        // Get valid category slugs from database
        $validCategories = Category::active()->pluck('slug')->toArray();
        // Fallback to constant categories if database is empty
        if (empty($validCategories)) {
            $validCategories = array_keys(ClassModel::CATEGORIES);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => ['required', 'string', 'in:' . implode(',', $validCategories)],
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'has_discount' => 'nullable|boolean',
            'discount' => 'required_if:has_discount,1|nullable|numeric|min:0|max:99999999.99|lte:price',
            'discount_starts_at' => 'nullable|date',
            'discount_ends_at' => 'nullable|date|after_or_equal:discount_starts_at',
            'discount_starts_at' => 'nullable|date',
            'discount_ends_at' => 'nullable|date|after_or_equal:discount_starts_at',
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'what_youll_learn' => 'required|string',
            'requirement' => 'required|string',
            'includes' => 'required|array|min:1',
            'includes.*' => 'required|string|in:video,lifetime,certificate,ai,mobile',
        ], [
            'price.max' => 'Harga tidak boleh melebihi Rp 99.999.999,99',
            'price.min' => 'Harga tidak boleh negatif',
            'price.numeric' => 'Harga harus berupa angka',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'image.required' => 'Gambar class tidak boleh kosong',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif, webp',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB',
            'what_youll_learn.required' => 'What You\'ll Learn tidak boleh kosong',
            'requirement.required' => 'Requirements tidak boleh kosong',
            'includes.required' => 'Course Includes harus dipilih minimal 1',
            'includes.min' => 'Course Includes harus dipilih minimal 1',
            'includes.*.in' => 'Pilihan includes tidak valid',
        ]);

        // Convert checkbox value to boolean
        $validated['is_published'] = $request->has('is_published') ? true : false;
        // Discount handling: convert checkbox and ensure discount value and period
        $validated['has_discount'] = $request->has('has_discount') ? true : false;
        if ($validated['has_discount']) {
            // Ensure discount exists and is numeric (already validated). Keep as decimal.
            $validated['discount'] = isset($validated['discount']) ? $validated['discount'] : 0;
            $validated['discount_starts_at'] = $request->input('discount_starts_at') ?: null;
            $validated['discount_ends_at'] = $request->input('discount_ends_at') ?: null;
        } else {
            $validated['discount'] = null;
            $validated['discount_starts_at'] = null;
            $validated['discount_ends_at'] = null;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Additional file size check (in bytes: 5MB = 5 * 1024 * 1024)
                $image = $request->file('image');
                if ($image->getSize() > 5 * 1024 * 1024) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Ukuran gambar tidak boleh lebih dari 5MB']);
                }
                
                $image = $request->file('image');
                
                // Debug: Log file info
                \Log::info('Uploading image', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType(),
                    'is_valid' => $image->isValid()
                ]);
                
                if (!$image->isValid()) {
                    return back()->withErrors(['image' => 'File gambar tidak valid.'])->withInput();
                }
                
                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $image->getClientOriginalName());
                
                // Ensure directory exists
                $directory = storage_path('app/public/classes');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Store the file using Storage facade
                $stored = Storage::disk('public')->putFileAs('classes', $image, $imageName);
                
                // Verify file was actually saved
                $fullPath = storage_path('app/public/' . $stored);
                $fileExists = Storage::disk('public')->exists('classes/' . $imageName);
                
                \Log::info('File storage check', [
                    'stored' => $stored,
                    'full_path' => $fullPath,
                    'exists' => $fileExists,
                    'size' => $fileExists ? Storage::disk('public')->size('classes/' . $imageName) : 0
                ]);
                
                if ($stored && $fileExists) {
                    $validated['image'] = $stored; // Already includes 'classes/' prefix
                    \Log::info('Image stored successfully', ['stored' => $stored, 'image' => $validated['image'], 'full_path' => $fullPath]);
                } else {
                    \Log::error('Failed to store image', ['stored' => $stored, 'full_path' => $fullPath, 'exists' => $fileExists]);
                    return back()->withErrors(['image' => 'Gagal menyimpan gambar.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Image upload error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return back()->withErrors(['image' => 'Error saat upload gambar: ' . $e->getMessage()])->withInput();
            }
        }

        $class = auth()->user()->classes()->create($validated);

        auth()->user()->logActivity('class_created', "Menambahkan kelas: {$class->name}");

        // Notifikasi ke semua student ketika course baru dipublish
        if ($class->is_published) {
            $this->notifyStudentsForNewCourse($class);
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil membuat kelas');
    }

    /**
     * Show form edit class dengan chapters
     */
    public function edit(ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $chapters = $class->chapters()->with('modules')->get();
        $categories = Category::active()->ordered()->get();

        return view('teacher.classes.edit', compact('class', 'chapters', 'categories'));
    }

    /**
     * Update class
     */
    public function update(Request $request, ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Get valid category slugs from database
        $validCategories = Category::active()->pluck('slug')->toArray();
        // Fallback to constant categories if database is empty
        if (empty($validCategories)) {
            $validCategories = array_keys(ClassModel::CATEGORIES);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => ['required', 'string', 'in:' . implode(',', $validCategories)],
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'has_discount' => 'nullable|boolean',
            'discount' => 'required_if:has_discount,1|nullable|numeric|min:0|max:99999999.99|lte:price',
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'what_youll_learn' => 'required|string',
            'requirement' => 'required|string',
            'includes' => 'required|array|min:1',
            'includes.*' => 'required|string|in:video,lifetime,certificate,ai,mobile',
        ], [
            'price.max' => 'Harga tidak boleh melebihi Rp 99.999.999,99',
            'price.min' => 'Harga tidak boleh negatif',
            'price.numeric' => 'Harga harus berupa angka',
            'category.in' => 'Kategori yang dipilih tidak valid.',
            'description.required' => 'Deskripsi tidak boleh kosong',
            'image.image' => 'File harus berupa gambar',
            'image.mimes' => 'Format gambar yang diperbolehkan: jpeg, png, jpg, gif, webp',
            'image.max' => 'Ukuran gambar tidak boleh lebih dari 5MB',
            'what_youll_learn.required' => 'What You\'ll Learn tidak boleh kosong',
            'requirement.required' => 'Requirements tidak boleh kosong',
            'includes.required' => 'Course Includes harus dipilih minimal 1',
            'includes.min' => 'Course Includes harus dipilih minimal 1',
            'includes.*.in' => 'Pilihan includes tidak valid',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                // Additional file size check (in bytes: 5MB = 5 * 1024 * 1024)
                $image = $request->file('image');
                if ($image->getSize() > 5 * 1024 * 1024) {
                    return redirect()->back()
                        ->withInput()
                        ->withErrors(['image' => 'Ukuran gambar tidak boleh lebih dari 5MB']);
                }
                
                // Delete old image if exists
                if ($class->image && Storage::exists('public/' . $class->image)) {
                    Storage::delete('public/' . $class->image);
                }
                
                $image = $request->file('image');
                
                // Debug: Log file info
                \Log::info('Updating image', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType(),
                    'is_valid' => $image->isValid()
                ]);
                
                if (!$image->isValid()) {
                    return back()->withErrors(['image' => 'File gambar tidak valid.'])->withInput();
                }
                
                $imageName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $image->getClientOriginalName());
                
                // Ensure directory exists
                $directory = storage_path('app/public/classes');
                if (!file_exists($directory)) {
                    mkdir($directory, 0755, true);
                }
                
                // Store the file using Storage facade
                $stored = Storage::disk('public')->putFileAs('classes', $image, $imageName);
                
                // Verify file was actually saved
                $fullPath = storage_path('app/public/' . $stored);
                $fileExists = Storage::disk('public')->exists('classes/' . $imageName);
                
                \Log::info('File storage check (update)', [
                    'stored' => $stored,
                    'full_path' => $fullPath,
                    'exists' => $fileExists,
                    'size' => $fileExists ? Storage::disk('public')->size('classes/' . $imageName) : 0
                ]);
                
                if ($stored && $fileExists) {
                    $validated['image'] = $stored; // Already includes 'classes/' prefix
                    \Log::info('Image updated successfully', ['stored' => $stored, 'image' => $validated['image'], 'full_path' => $fullPath]);
                } else {
                    \Log::error('Failed to store image (update)', ['stored' => $stored, 'full_path' => $fullPath, 'exists' => $fileExists]);
                    return back()->withErrors(['image' => 'Gagal menyimpan gambar.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Image upload error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return back()->withErrors(['image' => 'Error saat upload gambar: ' . $e->getMessage()])->withInput();
            }
        }

        $wasPublished = $class->is_published;
        // Discount handling: convert checkbox and ensure discount value and period
        $validated['has_discount'] = $request->has('has_discount') ? true : false;
        if ($validated['has_discount']) {
            $validated['discount'] = isset($validated['discount']) ? $validated['discount'] : 0;
            $validated['discount_starts_at'] = $request->input('discount_starts_at') ?: null;
            $validated['discount_ends_at'] = $request->input('discount_ends_at') ?: null;
        } else {
            $validated['discount'] = null;
            $validated['discount_starts_at'] = null;
            $validated['discount_ends_at'] = null;
        }

        $class->update($validated);
        $class->refresh();

        auth()->user()->logActivity('class_updated', "Mengubah kelas: {$class->name}");

        // Notifikasi ke semua student ketika course baru dipublish (dari draft ke published)
        if (!$wasPublished && $class->is_published) {
            $this->notifyStudentsForNewCourse($class);
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil memperbarui kelas');
    }

    /**
     * Delete class
     */
    public function destroy(ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $className = $class->name;
        $class->delete();

        auth()->user()->logActivity('class_deleted', "Menghapus kelas: {$className}");

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Berhasil menghapus kelas');
    }

    /**
     * Show content management interface
     */
    public function manageContent()
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        $classes = ClassModel::byTeacher(auth()->id())
            ->with('chapters.modules')
            ->orderBy('order')
            ->get();

        $totalClasses = $classes->count();
        $totalChapters = $classes->sum(fn($c) => $c->chapters->count());
        $totalModules = $classes->sum(fn($c) => $c->chapters->sum(fn($ch) => $ch->modules->count() ?? 0));

        return view('teacher.manage-content', compact('classes', 'totalClasses', 'totalChapters', 'totalModules'));
    }

    /**
     * Notify all students about new course (hanya yang mengaktifkan notifikasi)
     */
    private function notifyStudentsForNewCourse(ClassModel $class)
    {
        // Notifikasi ke semua student yang mengaktifkan notifikasi new_course
        $students = DB::table('users')
            ->where('role', 'student')
            ->where('is_banned', false)
            ->select('id')
            ->get();

        foreach ($students as $student) {
            $user = \App\Models\User::find($student->id);
            if ($user && $user->wantsNotification('new_course')) {
                Notification::create([
                    'user_id' => $student->id,
                    'type' => 'new_course',
                    'title' => 'Course Baru Tersedia',
                    'message' => "Course baru '{$class->name}' oleh {$class->teacher->name} telah tersedia. Segera daftar sekarang!",
                    'notifiable_type' => ClassModel::class,
                    'notifiable_id' => $class->id,
                ]);
            }
        }
    }
}
