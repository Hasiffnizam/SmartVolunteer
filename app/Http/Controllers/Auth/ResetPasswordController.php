<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function show(string $token)
    {
         return view('auth.reset-password', [
        'token' => $token,
        'email' => request('email'),
    ]);
    }

    public function update(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = bcrypt($password);
            $user->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return redirect()
            ->route('login.show')
            ->with('password_reset_success', 'Your password has been reset successfully.');
    }

    return back()->withErrors(['email' => __($status)]);
}
}
