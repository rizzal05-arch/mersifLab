<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categories = Category::ordered()->get();
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $validated['is_active'] ?? true;

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'required|string',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        // Check if category is used by any classes
        $classesCount = $category->classes()->count();
        
        if ($classesCount > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', "Kategori tidak dapat dihapus karena digunakan oleh {$classesCount} course(s).");
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}
