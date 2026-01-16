<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\Cause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $this->ensureVolunteer($request);

        // Load pivot relationships for display
        $user = $request->user()->load(['skills', 'causes']);

        // Profile completeness (include skills/causes)
        $fields = [
            'name' => (bool) $user->name,
            'email' => (bool) $user->email,
            'phone' => (bool) $user->phone,
            'address' => (bool) $user->address,
            'avatar' => (bool) $user->avatar_path,

            // IMPORTANT: use relationship query to avoid attribute shadowing
            'skills' => $user->skills()->count() > 0,
            'causes' => $user->causes()->count() > 0,
        ];

        $total = count($fields);
        $done = collect($fields)->filter()->count();
        $percent = (int) round(($done / max($total, 1)) * 100);

        $missing = collect($fields)
            ->filter(fn ($ok) => !$ok)
            ->keys()
            ->map(fn ($k) => match ($k) {
                'name' => 'Name',
                'email' => 'Email',
                'phone' => 'Phone',
                'address' => 'Address',
                'avatar' => 'Profile Photo',
                'skills' => 'Skills',
                'causes' => 'Causes',
                default => ucfirst($k),
            })
            ->values()
            ->all();

        return view('volunteer.profile.show', [
            'user' => $user,
            'profileCompletionPercent' => $percent,
            'profileMissingFields' => $missing,
        ]);
    }

    public function edit(Request $request)
    {
        $this->ensureVolunteer($request);

        $user = $request->user()->load(['skills', 'causes']);

        return view('volunteer.profile.edit', [
            'user' => $user,

            // all options
            'skills' => Skill::orderBy('name')->get(),
            'causes' => Cause::orderBy('name')->get(),

            // selected ids (explicit pluck avoids shadowing issues)
            'selectedSkillIds' => $user->skills()->pluck('skills.id')->toArray(),
            'selectedCauseIds' => $user->causes()->pluck('causes.id')->toArray(),
        ]);
    }

    public function update(Request $request)
    {
        $this->ensureVolunteer($request);

        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:2000'],
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            // ✅ NEW: pivot selections
            'skill_ids' => ['nullable', 'array'],
            'skill_ids.*' => ['integer', 'exists:skills,id'],
            'cause_ids' => ['nullable', 'array'],
            'cause_ids.*' => ['integer', 'exists:causes,id'],

            // password change (optional)
            'current_password' => ['nullable', 'string', 'min:6'],
            'new_password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            if ($user->avatar_path && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }
            $validated['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        // Password change (only if user filled new_password)
        if (!empty($validated['new_password'])) {
            if (!Hash::check($validated['current_password'] ?? '', $user->password)) {
                return back()
                    ->withErrors(['current_password' => 'Current password is incorrect.'])
                    ->withInput();
            }

            $user->password = Hash::make($validated['new_password']);
            $user->save();
        }

        // ✅ Update pivots first (or after — either is fine)
        $user->skills()->sync($request->input('skill_ids', []));
        $user->causes()->sync($request->input('cause_ids', []));

        // Remove non-user fields before updating user columns
        unset(
            $validated['avatar'],
            $validated['current_password'],
            $validated['new_password'],
            $validated['new_password_confirmation'],
            $validated['skill_ids'],
            $validated['cause_ids'],
        );

        // Update user fields
        $user->update($validated);
        $user->refresh();

        return redirect()
            ->route('volunteer.profile.show')
            ->with('success', 'Profile updated successfully.');
    }

    private function ensureVolunteer(Request $request): void
    {
        $user = $request->user();
        abort_if(!$user || ($user->role ?? null) !== 'volunteer', 403);
    }
}
