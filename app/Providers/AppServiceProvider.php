<?php

namespace App\Providers;

use App\Models\Book;
use App\Models\User;
use App\Policies\BookPolicy;
use App\Policies\MemberPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Book::class, BookPolicy::class);
        Gate::policy(User::class, MemberPolicy::class);

        // Kunci throttle digabung dari surel dan alamat IP, supaya percobaan masuk
        // untuk satu akun tidak terkunci hanya karena orang lain di jaringan yang
        // sama salah mengetik kata sandinya.
        RateLimiter::for('login', function (Request $request) {
            $kunci = Str::transliterate(Str::lower($request->string('email')).'|'.$request->ip());

            return Limit::perMinute(5)->by($kunci);
        });

        RateLimiter::for('peminjaman', fn (Request $request) => Limit::perMinute(30)->by((string) $request->user()?->id));
    }
}
