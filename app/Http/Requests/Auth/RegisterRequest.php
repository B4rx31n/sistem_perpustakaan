<?php

namespace App\Http\Requests\Auth;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<mixed>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
            'nomor_anggota' => ['nullable', 'string', 'max:20', Rule::unique(User::class)],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'program_studi' => ['nullable', 'string', 'max:120'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
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
            'nomor_anggota' => 'nomor anggota',
            'no_hp' => 'nomor telepon',
            'program_studi' => 'program studi',
            'password' => 'kata sandi',
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'Surel ini sudah terdaftar. Masuk saja memakai akun yang ada.',
            'nomor_anggota.unique' => 'Nomor anggota ini sudah dipakai anggota lain.',
            'no_hp.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, dan tanda hubung.',
        ];
    }

    public function peranDefault(): array
    {
        return [UserRole::Anggota->value];
    }
}
