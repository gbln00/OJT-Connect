<?php

namespace App\Http\Controllers\Coordinator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class CoordinatorSettingsController extends Controller
{
    /**
     * Show the profile settings page.
     */
    public function index()
    {
        return view('coordinator.profile.settings');
    }

    /**
     * Update name and email.
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $user->update([
            'name'  => $request->name,
            'email' => $request->email,
        ]);

        return back()->with('success', 'Profile updated successfully.');
    }

    /**
     * Update password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password updated successfully.');
    }

    /**
     * Upload or replace avatar.
     */
    public function updateAvatar(Request $request)
{
    \Log::info('Avatar upload attempt', [
        'has_file'   => $request->hasFile('avatar'),
        'all_files'  => array_keys($request->allFiles()),
        'all_input'  => array_keys($request->all()),
        'method'     => $request->method(),
        'content_type' => $request->header('Content-Type'),
    ]);

    if (!$request->hasFile('avatar')) {
        \Log::error('No avatar file in request');
        return back()->with('error', 'No file received by server.');
    }

    $file = $request->file('avatar');

    \Log::info('File details', [
        'valid'     => $file->isValid(),
        'error'     => $file->getError(),
        'size'      => $file->getSize(),
        'mime'      => $file->getMimeType(),
        'original'  => $file->getClientOriginalName(),
    ]);

    $request->validate([
        'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
    ]);

    $user = Auth::user();

    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
        Storage::disk('public')->delete($user->avatar);
    }

    $path = $request->file('avatar')->store('avatars', 'public');

    \Log::info('Stored avatar', ['path' => $path]);

    $user->update(['avatar' => $path]);

    return back()->with('success', 'Avatar updated successfully.');
}

    /**
     * Delete avatar (revert to initials).
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);

        return back()->with('success', 'Avatar removed.');
    }
}