<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
            'profile_photo'    => ['nullable', 'image', 'max:2048'], // Max 2MB image
            'report_frequency' => ['nullable', 'string', Rule::in(['daily', 'weekly', 'monthly'])],
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        // Handle report frequency and calculate next_send_at if it changed
        if ($request->has('report_frequency')) {
            $data['report_frequency'] = $request->report_frequency;
            
            // Only update next_send_at if frequency changed
            if ($user->report_frequency !== $request->report_frequency) {
                if ($request->report_frequency) {
                    $now = Carbon::now();
                    $data['next_send_at'] = match ($request->report_frequency) {
                        'daily'   => $now->copy()->endOfDay()->subMinutes(5),
                        'weekly'  => $now->copy()->endOfWeek()->subMinutes(5),
                        'monthly' => $now->copy()->endOfMonth()->subMinutes(5),
                    };
                } else {
                    $data['next_send_at'] = null; // User disabled reports
                }
            }
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('profile_photo')) {
            // Delete old photo if it exists
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $path = $request->file('profile_photo')->store('profile_photos', 'public');
            $data['profile_photo'] = $path;
        }

        $user->update($data);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully.');
    }
}
