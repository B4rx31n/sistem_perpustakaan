<?php

namespace App\Http\Requests\Member;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
        $member = $this->route('member');
        $id = $member?->id;

        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'nomor_anggota' => ['nullable', 'string', 'max:20', Rule::unique('users', 'nomor_anggota')->ignore($id)],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'program_studi' => ['nullable', 'string', 'max:120'],
            'alamat' => ['nullable', 'string', 'max:500'],
            'role' => ['required', Rule::in(array_column(UserRole::cases(), 'value'))],
            'status' => ['required', Rule::in(['aktif', 'nonaktif'])],
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
            'alamat' => 'alamat',
            'role' => 'peran',
            'status' => 'status akun',
        ];
    }

    public function messages(): array
    {
        return [
            'no_hp.regex' => 'Nomor telepon hanya boleh berisi angka, spasi, dan tanda hubung.',
        ];
    }
}
