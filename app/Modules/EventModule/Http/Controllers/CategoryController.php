<?php

namespace App\Modules\EventModule\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\EventModule\Http\Requests\StoreCategoryRequest;
use App\Modules\EventModule\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('events')->orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $name = $request->validated()['name'];

        Category::create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);

        return back()->with('success', 'Kategori baru berhasil ditambahkan!');
    }

    public function destroy(int $id): RedirectResponse
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return back()->with('success', 'Kategori berhasil dihapus.');
    }
}