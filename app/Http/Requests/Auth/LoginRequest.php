<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'string'],
            'ingat' => ['nullable', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'email' => 'surel',
            'password' => 'kata sandi',
            'ingat' => 'ingat saya',
        ];
    }

    /**
     * Pesan kesalahan sengaja tidak membedakan surel tidak terdaftar dengan kata
     * sandi salah, supaya halaman masuk tidak bisa dipakai menebak akun yang ada.
     */
    public function authenticate(): void
    {
        if (! auth()->attempt($this->only('email', 'password'), $this->boolean('ingat'))) {
            throw ValidationException::withMessages([
                'email' => 'Surel atau kata sandi tidak cocok dengan data kami.',
            ]);
        }
    }
}
