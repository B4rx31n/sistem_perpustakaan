<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureRole
{
    /**
     * Membatasi rute ke satu atau beberapa peran. Peran yang tidak diizinkan
     * diarahkan ke beranda miliknya sendiri, bukan ke halaman 403 kosong, supaya
     * anggota yang tersesat tidak terjebak di layar error.
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if ($user === null || ! in_array($user->role->value, $roles, true)) {
            if ($request->expectsJson()) {
                abort(403, 'Peran akun tidak diizinkan mengakses sumber daya ini.');
            }

            return redirect()
                ->route('dashboard')
                ->with('error', 'Halaman itu hanya untuk '.implode(' atau ', $this->labelPeran($roles)).'.');
        }

        return $next($request);
    }

    /**
     * @param  array<int, string>  $roles
     * @return array<int, string>
     */
    private function labelPeran(array $roles): array
    {
        return array_map(
            fn (string $role) => UserRole::tryFrom($role)?->label() ?? $role,
            $roles,
        );
    }
}
