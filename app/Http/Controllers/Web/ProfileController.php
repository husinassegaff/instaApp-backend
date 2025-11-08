<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the specified user's profile
     */
    public function show(User $user): View
    {
        // Load user's posts with likes and comments count
        $posts = $user->posts()
            ->withCount(['likes', 'comments'])
            ->with('user')
            ->latest()
            ->paginate(12);

        return view('profile.show', compact('user', 'posts'));
    }

    /**
     * Show the form for editing the authenticated user's profile
     */
    public function edit(): View
    {
        $user = auth()->user();

        return view('profile.edit', compact('user'));
    }

    /**
     * Update the authenticated user's profile
     */
    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:500',
            'profile_image' => 'nullable|string', // Base64 image
        ]);

        try {
            $user->update($validated);

            return redirect()->route('profile.show', $user->id)
                ->with('success', 'Profile updated successfully!');
        } catch (\Exception $e) {
            Log::error('Profile update failed', [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
            ]);

            return back()->withErrors([
                'error' => 'Failed to update profile. Please try again.',
            ])->withInput();
        }
    }
}
