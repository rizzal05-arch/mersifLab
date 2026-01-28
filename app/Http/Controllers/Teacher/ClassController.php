<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
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

        return view('teacher.classes.create');
    }

    /**
     * Store class baru
     */
    public function store(Request $request)
    {
        if (!auth()->user()->isTeacher()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:ai,development,marketing,design,photography',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'what_youll_learn' => 'nullable|string',
            'requirement' => 'nullable|string',
        ], [
            'price.max' => 'Price cannot exceed Rp 99,999,999.99',
            'price.min' => 'Price cannot be negative',
            'price.numeric' => 'Price must be a number',
        ]);

        // Convert checkbox value to boolean
        $validated['is_published'] = $request->has('is_published') ? true : false;

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
                $image = $request->file('image');
                
                // Debug: Log file info
                \Log::info('Uploading image', [
                    'original_name' => $image->getClientOriginalName(),
                    'size' => $image->getSize(),
                    'mime' => $image->getMimeType(),
                    'is_valid' => $image->isValid()
                ]);
                
                if (!$image->isValid()) {
                    return back()->withErrors(['image' => 'Invalid image file.'])->withInput();
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
                    return back()->withErrors(['image' => 'Failed to save image.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Image upload error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return back()->withErrors(['image' => 'Error uploading image: ' . $e->getMessage()])->withInput();
            }
        }

        $class = auth()->user()->classes()->create($validated);

        // Notify all students when new course is published
        if ($class->is_published) {
            $this->notifyStudentsForNewCourse($class);
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Class created successfully');
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

        return view('teacher.classes.edit', compact('class', 'chapters'));
    }

    /**
     * Update class
     */
    public function update(Request $request, ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|in:ai,development,marketing,design,photography',
            'order' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'what_youll_learn' => 'nullable|string',
            'requirement' => 'nullable|string',
        ], [
            'price.max' => 'Price cannot exceed Rp 99,999,999.99',
            'price.min' => 'Price cannot be negative',
            'price.numeric' => 'Price must be a number',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            try {
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
                    return back()->withErrors(['image' => 'Invalid image file.'])->withInput();
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
                    return back()->withErrors(['image' => 'Failed to save image.'])->withInput();
                }
            } catch (\Exception $e) {
                \Log::error('Image upload error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
                return back()->withErrors(['image' => 'Error uploading image: ' . $e->getMessage()])->withInput();
            }
        }

        $wasPublished = $class->is_published;
        $class->update($validated);
        $class->refresh();

        // Notify all students when course is published (from draft to published)
        if (!$wasPublished && $class->is_published) {
            $this->notifyStudentsForNewCourse($class);
        }

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Class updated successfully');
    }

    /**
     * Delete class
     */
    public function destroy(ClassModel $class)
    {
        if ($class->teacher_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $class->delete();

        return redirect()
            ->route('teacher.manage.content')
            ->with('success', 'Class deleted successfully');
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
     * Notify all students about new course (only those who enabled notifications)
     */
    private function notifyStudentsForNewCourse(ClassModel $class)
    {
        // Notify all students who enabled new_course notifications
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
                    'title' => 'New Course Available',
                    'message' => "New course '{$class->name}' by {$class->teacher->name} is now available. Enroll now!",
                    'notifiable_type' => ClassModel::class,
                    'notifiable_id' => $class->id,
                ]);
            }
        }
    }
}
