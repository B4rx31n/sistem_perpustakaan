<?php

namespace App\Http\Requests\Book;

use App\Models\Book;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isPetugas() ?? false;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'isbn' => ['required', 'string', 'max:20', 'regex:/^[0-9\-Xx]+$/', Rule::unique(Book::class)],
            'judul' => ['required', 'string', 'max:200'],
            'penulis' => ['required', 'string', 'max:150'],
            'penerbit' => ['nullable', 'string', 'max:150'],
            'tahun_terbit' => ['nullable', 'integer', 'between:1900,'.date('Y')],
            'category_id' => ['nullable', Rule::exists('categories', 'id')],
            'stok' => ['required', 'integer', 'min:0', 'max:9999'],
            'deskripsi' => ['nullable', 'string', 'max:2000'],
            'cover' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'isbn' => 'ISBN',
            'judul' => 'judul',
            'penulis' => 'penulis',
            'penerbit' => 'penerbit',
            'tahun_terbit' => 'tahun terbit',
            'category_id' => 'kategori',
            'stok' => 'jumlah stok',
            'deskripsi' => 'deskripsi',
            'cover' => 'sampul',
        ];
    }

    public function messages(): array
    {
        return [
            'isbn.regex' => 'ISBN hanya boleh berisi angka dan tanda hubung.',
            'isbn.unique' => 'Buku dengan ISBN ini sudah ada di katalog.',
            'cover.max' => 'Ukuran sampul maksimal 2 MB.',
            'stok.min' => 'Stok tidak boleh negatif. Hapus buku lewat tombol hapus bila koleksi dicabangkan.',
        ];
    }
}
