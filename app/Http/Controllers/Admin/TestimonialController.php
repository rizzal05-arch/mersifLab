<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialController extends Controller
{
    public function index()
    {
        // Order by newest first (order column was removed)
        $testimonials = Testimonial::orderBy('created_at', 'desc')->paginate(25);
        return view('admin.testimonials.index', compact('testimonials'));
    }

    public function create()
    {
        return view('admin.testimonials.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'content' => 'required|string|max:2000',
            'avatar' => 'nullable|image|max:2048',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $data['admin_id'] = auth()->id();
        $data['is_published'] = $request->has('is_published');

        Testimonial::create($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial added successfully.');
    }

    public function edit(Testimonial $testimonial)
    {
        return view('admin.testimonials.edit', compact('testimonial'));
    }

    public function show(Testimonial $testimonial)
    {
        // Redirect to edit page for now
        return redirect()->route('admin.testimonials.edit', $testimonial->id);
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'content' => 'required|string|max:2000',
            'avatar' => 'nullable|image|max:2048',
            'is_published' => 'sometimes|boolean',
        ]);

        if ($request->hasFile('avatar')) {
            // remove old avatar file if exists
            if ($testimonial->avatar && Storage::disk('public')->exists($testimonial->avatar)) {
                Storage::disk('public')->delete($testimonial->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('testimonials', 'public');
        }

        $data['is_published'] = $request->has('is_published');

        $testimonial->update($data);

        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial updated.');
    }

    public function destroy(Testimonial $testimonial)
    {
        // delete avatar file if exists
        if ($testimonial->avatar && Storage::disk('public')->exists($testimonial->avatar)) {
            Storage::disk('public')->delete($testimonial->avatar);
        }

        $testimonial->delete();
        return redirect()->route('admin.testimonials.index')->with('success', 'Testimonial deleted.');
    }

    public function togglePublish(Testimonial $testimonial)
    {
        $testimonial->update(['is_published' => !$testimonial->is_published]);
        return redirect()->back()->with('success', 'Publication status updated.');
    }
}
