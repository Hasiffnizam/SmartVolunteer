<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Skill;
use App\Models\Cause;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Services\BrevoMailer;

class VolunteerRegisterController extends Controller
{
    private const SESSION_KEY = 'vol_reg';
    private const STEP_KEY = 'vol_reg_step';

    public function show(Request $request)
    {
        $step = (int) $request->session()->get(self::STEP_KEY, 1);
        $data = (array) $request->session()->get(self::SESSION_KEY, []);

        $skills = Skill::orderBy('name')->get();
        $causes = Cause::orderBy('name')->get();

        return view('auth.register', compact('step', 'data', 'skills', 'causes'));
    }

    public function next(Request $request)
    {
        $step = (int) $request->input('step', 1);

        $validated = $this->validateStep($request, $step);

        $data = (array) $request->session()->get(self::SESSION_KEY, []);
        $request->session()->put(self::SESSION_KEY, array_merge($data, $validated));

        $request->session()->put(self::STEP_KEY, min($step + 1, 3));

        return redirect()->route('register.show');
    }

    public function prev(Request $request)
    {
        $step = (int) $request->input('step', 1);
        $request->session()->put(self::STEP_KEY, max($step - 1, 1));

        return redirect()->route('register.show');
    }

    public function finish(Request $request)
    {
        $validated = $this->validateStep($request, 3);

        $data = (array) $request->session()->get(self::SESSION_KEY, []);
        $all  = array_merge($data, $validated);

        // Create user
        $user = User::create([
            'name'       => $all['name'],
            'dob'        => $all['dob'] ?? null,
            'phone'      => $all['phone'] ?? null,
            'gender'     => $all['gender'] ?? null,
            'occupation' => $all['occupation'] ?? null,
            'address'    => $all['address'] ?? null,
            'email'      => $all['email'],
            'password'   => Hash::make($all['password']),
            'role'       => 'volunteer',
        ]);

        // Save pivot
        $user->skills()->sync($all['skills'] ?? []);
        $user->causes()->sync($all['causes'] ?? []);

        // Send welcome email via Brevo API
        try {
            $html = view('emails.volunteer-welcome', ['user' => $user])->render();

            BrevoMailer::send(
                $user->email,
                $user->name ?? 'Volunteer',
                'Welcome to SmartVolunteer',
                $html
            );

            Log::info('Welcome email sent (Brevo)', ['email' => $user->email]);
        } catch (\Throwable $e) {
            Log::error('Welcome email failed (Brevo)', [
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);
        }

        // Clear session
        $request->session()->forget([self::SESSION_KEY, self::STEP_KEY]);

        return redirect('/')
            ->with('success', 'Registration successful! Please check your email for confirmation.');
    }

    private function validateStep(Request $request, int $step): array
    {
        if ($step === 1) {
            return $request->validate([
                'name' => ['required','string','max:255'],
                'dob' => ['nullable','date'],
                'phone' => ['nullable','string','max:30'],
                'gender' => ['nullable','in:Male,Female,Other'],
                'occupation' => ['nullable','string','max:80'],
                'address' => ['nullable','string','max:2000'],
            ]);
        }

        if ($step === 2) {
            return $request->validate([
                'email' => ['required','email','max:255','unique:users,email'],
                'password' => ['required','string','min:8','confirmed'],
            ], [
                'password.confirmed' => 'Password confirmation tidak sama.',
            ]);
        }

        return $request->validate([
            'skills' => ['nullable','array'],
            'skills.*' => ['integer','exists:skills,id'],

            'causes' => ['nullable','array'],
            'causes.*' => ['integer','exists:causes,id'],
        ]);
    }
}
