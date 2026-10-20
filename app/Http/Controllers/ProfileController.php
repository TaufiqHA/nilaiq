<?php

namespace App\Http\Controllers;

use App\Models\TelegramContext;
use App\Models\TelegramLink;
use App\Models\TelegramLinkToken;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(): View
    {
        $user = auth()->user();
        $telegramLink = $user->role === 'mapel'
            ? TelegramLink::where('user_id', $user->id)->first()
            : null;

        return view('auth.profile', compact('user', 'telegramLink'));
    }

    /**
     * Generate a one-time Telegram linking token for the current user.
     */
    public function linkTelegram(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        if ($user->role !== 'mapel') {
            return back()->with('error', 'Bot Telegram saat ini hanya tersedia untuk akun Guru Mapel.');
        }

        $botUsername = trim((string) config('services.telegram.username'), '@');

        if ($botUsername === '') {
            return back()->with('error', 'Username bot Telegram belum dikonfigurasi. Hubungi admin.');
        }

        TelegramLinkToken::where('user_id', $user->id)
            ->where(fn ($query) => $query->whereNotNull('used_at')->orWhere('expires_at', '<=', now()))
            ->delete();

        $token = Str::random(48);

        TelegramLinkToken::create([
            'user_id' => $user->id,
            'token_hash' => hash('sha256', $token),
            'expires_at' => now()->addMinutes(15),
        ]);

        return back()
            ->with('success', 'Kode penghubung Telegram berhasil dibuat. Kode berlaku 15 menit.')
            ->with('telegram_link_url', "https://t.me/{$botUsername}?start={$token}")
            ->with('telegram_link_code', $token);
    }

    /**
     * Disconnect the current user's Telegram chat.
     */
    public function unlinkTelegram(): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $link = TelegramLink::where('user_id', $user->id)->first();

        if ($link !== null) {
            TelegramContext::where('chat_id', $link->chat_id)->delete();
            $link->delete();
        }

        return back()->with('success', 'Koneksi Telegram berhasil diputuskan.');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ]);

        $user->update($validated);

        return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile')->with('success', 'Password berhasil diperbarui.');
    }
}
