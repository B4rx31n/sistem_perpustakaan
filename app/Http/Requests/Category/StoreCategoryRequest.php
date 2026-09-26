<?php

namespace App\Http\Requests\Category;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $category = $this->route('category');

        return [
            'nama' => [
                'required',
                'string',
                'max:80',
                Rule::unique(Category::class)->ignore($category instanceof Category ? $category->id : null),
            ],
            'kode' => [
                'required',
                'string',
                'max:10',
                'alpha_dash',
                Rule::unique(Category::class)->ignore($category instanceof Category ? $category->id : null),
            ],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama' => 'nama kategori',
            'kode' => 'kode kategori',
            'keterangan' => 'keterangan',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.alpha_dash' => 'Kode kategori hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'kode.unique' => 'Kode kategori ini sudah dipakai.',
            'nama.unique' => 'Kategori dengan nama ini sudah ada.',
        ];
    }
}
