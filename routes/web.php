<?php

use App\Models\Event;
use App\Services\BrevoMailer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\VolunteerRegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\HomeRedirectController;
use App\Http\Controllers\Volunteer\ProfileController;
use App\Http\Controllers\Volunteer\ExploreEventController;
use App\Http\Controllers\Volunteer\MyEventController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\AttendanceCheckInController;
use App\Http\Controllers\Volunteer\ReportController;
use App\Http\Controllers\Admin\EventReportController;
use App\Http\Controllers\Admin\ReportAnalysisController;
use App\Http\Controllers\Admin\EventCheckInController;

/*
|--------------------------------------------------------------------------
| Public
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    $upcomingEvents = Event::whereDate('event_date', '>=', Carbon::today())
        ->orderBy('event_date')
        ->limit(3)
        ->get();

    return view('landing', compact('upcomingEvents'));
})->name('landing');

/*
|--------------------------------------------------------------------------
| (A) Signed email confirm (no login required)
|--------------------------------------------------------------------------
*/
Route::get('/attendance/confirm/{event}/{registration}', [AttendanceCheckInController::class, 'confirmFromEmail'])
    ->name('attendance.email.confirm')
    ->middleware('signed');

/*
|--------------------------------------------------------------------------
| Guest (not logged in)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {

    // Auth
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login.show');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    // Register (multi-step)
    Route::get('/register', [VolunteerRegisterController::class, 'show'])->name('register.show');
    Route::post('/register/next', [VolunteerRegisterController::class, 'next'])->name('register.next');
    Route::post('/register/prev', [VolunteerRegisterController::class, 'prev'])->name('register.prev');
    Route::post('/register/finish', [VolunteerRegisterController::class, 'finish'])->name('register.finish');

    // Forgot / Reset password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'show'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'send'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'show'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'update'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Authenticated (all logged-in users)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Role-based redirect (you already have this controller)
    Route::get('/home', HomeRedirectController::class)->name('home');
});

/*
|--------------------------------------------------------------------------
| (B) Volunteer QR check-in (requires login + volunteer role + signed)
|--------------------------------------------------------------------------
| These are NOT inside /volunteer prefix because QR link should be short & public-ish.
| But they still require auth + volunteer role + signed URL.
*/
Route::get('/checkin/{event}', [AttendanceCheckInController::class, 'showQrCheckIn'])
    ->name('checkin.qr.show')
    ->middleware(['auth', 'role:volunteer', 'signed']);

Route::post('/checkin/{event}', [AttendanceCheckInController::class, 'confirmQrCheckIn'])
    ->name('checkin.qr.confirm')
    ->middleware(['auth', 'role:volunteer']);

/*
|--------------------------------------------------------------------------
| Admin (auth + admin role)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {

        Route::resource('events', EventController::class);

        Route::get('events/{event}/role-task', [EventController::class, 'roleTask'])
            ->name('events.role-task');

        Route::put('events/{event}/role-task', [EventController::class, 'updateRoleTask'])
            ->name('events.role-task.update');

        Route::get('events/{event}/attendance', [AttendanceController::class, 'show'])
            ->name('events.attendance');

        Route::post('events/{event}/attendance', [AttendanceController::class, 'update'])
            ->name('events.attendance.save');

        Route::get('/events/{event}/report', [EventReportController::class, 'show'])
            ->name('events.report');

        Route::get('/report-analysis', [ReportAnalysisController::class, 'index'])
            ->name('report_analysis.index');

        /*
        |--------------------------------------------------------------------------
        | (C) Admin QR display page
        |--------------------------------------------------------------------------
        */
        Route::get('events/{event}/check-in', [EventCheckInController::class, 'show'])
            ->name('events.checkin');
    });

/*
|--------------------------------------------------------------------------
| Volunteer (auth + volunteer role)
|--------------------------------------------------------------------------
*/
Route::prefix('volunteer')
    ->name('volunteer.')
    ->middleware(['auth', 'role:volunteer'])
    ->group(function () {

        Route::get('/explore', [ExploreEventController::class, 'index'])->name('explore.index');
        Route::get('/explore/{event}', [ExploreEventController::class, 'show'])->name('explore.show');
        Route::post('/explore/{event}/join', [ExploreEventController::class, 'join'])->name('explore.join');

        Route::get('/my-events', [MyEventController::class, 'index'])->name('myevents.index');
        Route::get('/my-events/{event}', [MyEventController::class, 'show'])->name('myevents.show');

        Route::get('/my-report', [ReportController::class, 'index'])->name('report');

        Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
        Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

Route::get('/_brevo-test', function () {
    $result = BrevoMailer::send(
        'YOUR_EMAIL@gmail.com',
        'Test User',
        'Brevo Test - SmartVolunteer',
        '<p>If you got this, Brevo API works ✅</p>'
    );

    return response()->json($result);
});

