<?php

namespace App\Http\Requests\Payment;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentRequest extends FormRequest
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
            'user_id' => ['required', Rule::exists('users', 'id')->where('role', 'anggota')],
            'jumlah' => ['required', 'integer', 'min:1', 'max:10000000'],
            'metode' => ['required', Rule::in(array_column(PaymentMethod::cases(), 'value'))],
            'loan_id' => ['nullable', Rule::exists('loans', 'id')],
            'bukti' => ['nullable', 'string', 'max:120'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_id' => 'anggota',
            'jumlah' => 'jumlah pembayaran',
            'metode' => 'metode pembayaran',
            'loan_id' => 'peminjaman',
            'bukti' => 'nomor bukti transfer',
            'keterangan' => 'keterangan',
        ];
    }

    public function messages(): array
    {
        return [
            'jumlah.min' => 'Nominal pembayaran minimal Rp1.',
        ];
    }
}
