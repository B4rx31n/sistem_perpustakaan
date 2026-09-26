<?php

namespace App\Http\Controllers;

use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\ActivityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->safe()->except('password');

        if ($request->filled('password')) {
            $data['password'] = $request->string('password')->value();
        }

        $user->update($data);

        ActivityLog::catat($user, 'profil-ubah', $user->nomor_anggota ?? $user->email, 'Data profil diperbarui');

        return back()->with('success', 'Data profil Anda diperbarui.');
    }
}
