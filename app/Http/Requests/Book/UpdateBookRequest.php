<?php

namespace App\Http\Requests\Book;

use App\Models\Book;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends StoreBookRequest
{
    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $book = $this->route('book');

        return array_merge(parent::rules(), [
            'isbn' => [
                'required',
                'string',
                'max:20',
                'regex:/^[0-9\-Xx]+$/',
                Rule::unique(Book::class)->ignore($book instanceof Book ? $book->id : null),
            ],
        ]);
    }

    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'isbn.unique' => 'ISBN ini sudah dipakai buku lain di katalog.',
        ]);
    }
}
