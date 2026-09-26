<?php

namespace App\Http\Requests\Profile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        $id = $this->user()->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'program_studi' => ['nullable', 'string', 'max:120'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama lengkap',
            'email' => 'surel',
            'no_hp' => 'nomor telepon',
            'program_studi' => 'program studi',
            'alamat' => 'alamat',
            'password' => 'kata sandi baru',
        ];
    }

    public function messages(): array
    {
        return [
            'no_hp.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, dan tanda hubung.',
            'password.confirmed' => 'Ulangi kata sandi baru dengan isi yang sama.',
        ];
    }
}
