<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules for a specific chapter.
     *
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function index($chapterId)
    {
        $modules = Module::where('chapter_id', $chapterId)
            ->orderBy('order', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $modules
        ]);
    }

    /**
     * Display the specified module.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $module = Module::with('chapter')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $module
        ]);
    }

    /**
     * Store a newly created module in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $chapterId
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, $chapterId)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,file,text,mixed',
            'content' => 'nullable|string',
            'video_url' => 'nullable|url',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'order' => 'nullable|integer',
            'is_published' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
        $validated['chapter_id'] = $chapterId;

        // Handle file upload if present
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('files/pdf', $fileName, 'private');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        // Set default values
        $validated['is_published'] = $validated['is_published'] ?? false;
        $validated['order'] = $validated['order'] ?? Module::where('chapter_id', $chapterId)->max('order') + 1;

        $module = Module::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Module created successfully',
            'data' => $module
        ], 201);
    }

    /**
     * Update the specified module in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $module = Module::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:video,file,text,mixed',
            'content' => 'sometimes|nullable|string',
            'video_url' => 'sometimes|nullable|url',
            'file' => 'sometimes|nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
            'order' => 'sometimes|nullable|integer',
            'is_published' => 'sometimes|nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();

        // Handle file upload if present
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($module->file_path && Storage::disk('private')->exists($module->file_path)) {
                Storage::disk('private')->delete($module->file_path);
            }

            $file = $request->file('file');
            $fileName = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $filePath = $file->storeAs('files/pdf', $fileName, 'private');

            $validated['file_path'] = $filePath;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
            $validated['mime_type'] = $file->getMimeType();
        }

        $module->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Module updated successfully',
            'data' => $module
        ]);
    }

    /**
     * Remove the specified module from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $module = Module::findOrFail($id);

        // Delete file if exists
        if ($module->file_path && Storage::disk('private')->exists($module->file_path)) {
            Storage::disk('private')->delete($module->file_path);
        }

        $module->delete();

        return response()->json([
            'success' => true,
            'message' => 'Module deleted successfully'
        ]);
    }

    /**
     * Download the module file.
     * Hanya user yang sudah login dan punya akses yang bisa download file
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function download($id)
    {
        // 1. Pastikan user sudah login
        $user = auth()->user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu untuk mengakses file ini.'
            ], 401);
        }

        // 2. Pastikan user memiliki role yang valid (student, teacher, atau admin)
        if (!in_array($user->role, ['student', 'teacher', 'admin'])) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke file ini.'
            ], 403);
        }

        // 3. Load module dengan relasi chapter dan class
        $module = Module::with(['chapter.class'])->findOrFail($id);

        if (!$module->file_path) {
            return response()->json([
                'success' => false,
                'message' => 'No file available for this module'
            ], 404);
        }

        // 4. Pastikan chapter dan class ada
        if (!$module->chapter || !$module->chapter->class) {
            return response()->json([
                'success' => false,
                'message' => 'Module tidak valid.'
            ], 404);
        }

        $class = $module->chapter->class;
        $isTeacherOrAdmin = $user->isTeacher() || $user->isAdmin();

        // 5. Check authorization: Teacher hanya bisa akses class mereka sendiri (kecuali admin)
        if ($isTeacherOrAdmin) {
            if (!$user->isAdmin() && $class->teacher_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. This class does not belong to you.'
                ], 403);
            }
        }

        // 6. Check enrollment untuk student
        $isEnrolled = false;
        if ($user->isStudent()) {
            $isEnrolled = DB::table('class_student')
                ->where('class_id', $class->id)
                ->where('user_id', $user->id)
                ->exists();
        }

        // 7. Student yang belum enrolled tidak bisa download (kecuali course sudah published untuk preview)
        if ($user->isStudent() && !$isEnrolled && !$class->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Anda harus enroll ke course ini terlebih dahulu untuk mengakses file.'
            ], 403);
        }

        // 8. Check if module is approved and accessible
        // Admin bisa akses semua modul, Teacher & Student hanya yang sudah approved
        if (!$user->isAdmin() && !$module->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => 'Modul ini belum disetujui admin sehingga tidak dapat diakses. Silakan tunggu persetujuan.'
            ], 403);
        }

        // 9. Check if user can view (untuk student yang belum enrolled, hanya bisa lihat yang published)
        if ($user->isStudent() && !$isEnrolled && !$module->is_published) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki akses ke file ini. Silakan enroll ke course ini terlebih dahulu.'
            ], 403);
        }

        // 10. Check if file exists
        if (!Storage::disk('private')->exists($module->file_path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found'
            ], 404);
        }

        // 11. Return file download
        return Storage::disk('private')->download($module->file_path, $module->file_name);
    }
}
