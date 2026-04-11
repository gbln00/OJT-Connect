<?php

namespace App\Http\Controllers\Student;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentSettingsController extends Controller
{
    public function index()
    {
        $profile = Auth::user()->studentProfile;
        return view('student.profile.settings', compact('profile'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone'   => ['nullable','string','max:20'],
            'address' => ['nullable','string','max:500'],
            'section' => ['nullable','string','max:100'],
        ]);
        Auth::user()->studentProfile?->update($request->only('phone','address', 'section'));
        return back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required','current_password'],
            'password'         => ['required','min:8','confirmed'],
        ]);
        Auth::user()->update(['password' => Hash::make($request->password)]);
        return back()->with('success', 'Password updated successfully.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'file', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars/' . $user->id, 'public');

        $user->update(['avatar' => $path]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'path'    => Storage::url($path),
                'stored'  => $path,
            ]);
        }

        return back()->with('avatar_success', 'Avatar updated.');
    }

    public function deleteAvatar(Request $request)
    {
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar' => null]);
        }

        return back()->with('avatar_success', 'Avatar removed.');
    }
}
