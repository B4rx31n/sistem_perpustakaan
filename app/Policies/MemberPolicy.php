<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\User;

class MemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isPetugas();
    }

    public function view(User $user, User $member): bool
    {
        return $user->isPetugas() || $user->is($member);
    }

    public function update(User $user, User $member): bool
    {
        return $user->isPetugas();
    }

    /**
     * Hanya administrator yang boleh mengubah peran atau memblokir akun.
     */
    public function ubahPeran(User $user, User $member): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function ubahStatus(User $user, User $member): bool
    {
        return $user->role === UserRole::Admin;
    }

    public function delete(User $user, User $member): bool
    {
        return $user->role === UserRole::Admin && ! $user->is($member);
    }
}
