<?php

namespace App\Http\Controllers;

use App\Http\Requests\Category\StoreCategoryRequest;
use App\Models\ActivityLog;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $kategori = Category::query()
            ->withCount('books')
            ->orderBy('nama')
            ->get();

        return view('categories.index', [
            'kategori' => $kategori,
        ]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $category = Category::create($request->validated());

        ActivityLog::catat(
            $request->user(),
            'kategori-tambah',
            $category->kode,
            "Kategori \"{$category->nama}\" dibuat",
        );

        return back()->with('success', "Kategori \"{$category->nama}\" ditambahkan.");
    }

    public function update(StoreCategoryRequest $request, Category $category): RedirectResponse
    {
        $category->update($request->validated());

        ActivityLog::catat(
            $request->user(),
            'kategori-ubah',
            $category->kode,
            "Kategori \"{$category->nama}\" diperbarui",
        );

        return back()->with('success', "Kategori \"{$category->nama}\" diperbarui.");
    }

    public function destroy(Category $category): RedirectResponse
    {
        $jumlahBuku = $category->books()->count();

        if ($jumlahBuku > 0) {
            return back()->with('error', "Kategori \"{$category->nama}\" masih dipakai {$jumlahBuku} buku. Pindahkan bukunya dulu sebelum menghapus kategori.");
        }

        $nama = $category->nama;
        $category->delete();

        ActivityLog::catat(request()->user(), 'kategori-hapus', null, "Kategori \"{$nama}\" dihapus");

        return back()->with('success', "Kategori \"{$nama}\" dihapus.");
    }
}
