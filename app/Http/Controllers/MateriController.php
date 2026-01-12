<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Materi;
use Illuminate\Http\Request;

class MateriController extends Controller
{
    public function index()
    {
        return view('admin.materi.index', [
            'materi' => Materi::with('course')->get()
        ]);
    }

    public function create()
    {
        return view('admin.materi.create', [
            'courses' => Course::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'course_id' => 'required',
            'type' => 'required|in:pdf,video',
            'file' => 'required|file'
        ]);

        $path = $request->file('file')->store('materi');

        Materi::create([
            'title' => $request->title,
            'course_id' => $request->course_id,
            'type' => $request->type,
            'file_path' => $path
        ]);

        return redirect('/admin/materi');
    }

    public function show($id)
    {
        $materi = Materi::findOrFail($id);

        if (!auth()->check()) {
            abort(403, 'Anda harus login terlebih dahulu');
        }

        $user = auth()->user();
        if (!$user->isSubscriber() && !$user->isAdmin()) {
            abort(403, 'Anda harus berlangganan untuk mengakses materi ini');
        }

        $fileUrl = route('materi.download', $id);

        return view('materi.show', [
            'materi' => $materi,
            'fileUrl' => $fileUrl
        ]);
    }

    public function download($id)
    {
        $materi = Materi::findOrFail($id);

        if (!auth()->check()) {
            abort(403, 'Anda harus login terlebih dahulu');
        }

        $user = auth()->user();
        if (!$user->isSubscriber() && !$user->isAdmin()) {
            abort(403, 'Anda harus berlangganan untuk mengakses materi ini');
        }

        return response()->file(
            storage_path('app/private/' . $materi->file_path)
        );
    }

    public function destroy($id)
    {
        $materi = Materi::findOrFail($id);
        
        if (file_exists(storage_path('app/private/' . $materi->file_path))) {
            unlink(storage_path('app/private/' . $materi->file_path));
        }
        
        $materi->delete();

        return redirect('/admin/materi')->with('success', 'Materi berhasil dihapus');
    }
}
