<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('password_confirmation');

        $user = DB::transaction(function () use ($data, $request) {
            $user = User::create($data + [
                'password' => $request->string('password')->value(),
                'role' => $request->peranDefault()[0],
                'nomor_anggota' => $data['nomor_anggota'] ?: $this->nomorAnggotaBerikutnya(),
            ]);

            ActivityLog::catat($user, 'daftar', $user->nomor_anggota, 'Pendaftaran akun anggota baru');

            return $user;
        });

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', "Selamat datang, {$user->name}. Nomor anggota Anda {$user->nomor_anggota}.");
    }

    /**
     * Nomor anggota disusun dari urutan user supaya unik dan mudah dibaca petugas
     * saat mengetikkan nomor di loket.
     */
    private function nomorAnggotaBerikutnya(): string
    {
        $terakhir = User::query()
            ->whereNotNull('nomor_anggota')
            ->orderByDesc('nomor_anggota')
            ->value('nomor_anggota');

        $urutan = $terakhir ? ((int) substr($terakhir, -5)) + 1 : 1;

        return 'ANG-'.str_pad((string) $urutan, 5, '0', STR_PAD_LEFT);
    }
}
