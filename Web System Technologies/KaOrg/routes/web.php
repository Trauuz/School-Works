<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\RegistrationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LevelController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipStatusController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\WeatherController;
use App\Http\Middleware\EnsureProfileComplete;
use App\Http\Middleware\EnsureSupabaseSession;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');
Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:6,1')->name('login.store');
    Route::get('/register', [RegistrationController::class, 'create'])->name('register');
    Route::post('/register', [RegistrationController::class, 'store'])->middleware('throttle:6,1')->name('register.store');
});
Route::get('/verify-email', [RegistrationController::class, 'notice'])->name('verification.notice');
Route::post('/verify-email', [RegistrationController::class, 'resend'])->middleware('throttle:2,1')->name('verification.resend');
Route::match(['get', 'post'], '/auth/confirm', [RegistrationController::class, 'confirm'])->middleware('throttle:10,1')->block()->name('auth.confirm');
Route::get('/registration-confirmed', [RegistrationController::class, 'confirmed'])->name('verification.confirmed');
Route::get('/forgot-password', [PasswordController::class, 'forgot'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'email'])->middleware('throttle:2,1')->name('password.email');
Route::get('/reset-password', [PasswordController::class, 'reset'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'update'])->middleware('throttle:6,1')->block()->name('password.update');
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->block()->name('logout');
Route::middleware(['auth', EnsureSupabaseSession::class])->group(function (): void {
    Route::get('/account/setup', [AccountController::class, 'setup'])->block()->name('account.setup');
    Route::post('/account/setup', [AccountController::class, 'complete'])->block()->name('account.setup.store');
    Route::get('/locations', LocationController::class)->middleware('throttle:30,1')->block()->name('locations.search');
    Route::get('/locations/reverse', [LocationController::class, 'reverse'])->middleware('throttle:30,1')->block()->name('locations.reverse');
    Route::get('/weather', WeatherController::class)->middleware('throttle:30,1')->block()->name('weather.show');
    Route::middleware(EnsureProfileComplete::class)->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->block()->name('dashboard');
        Route::get('/account/profile', [AccountController::class, 'edit'])->block()->name('account.profile');
        Route::put('/account/profile', [AccountController::class, 'update'])->block()->name('account.profile.update');
        Route::post('/account/renewals', [AccountController::class, 'renew'])->block()->name('account.renew');
        Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->whereNumber('transaction')->block()->name('transactions.show');
        Route::get('/payments/{payment}/receipt', [PaymentController::class, 'show'])->block()->name('payments.show');
        Route::middleware('can:manage-membership')->group(function (): void {
            Route::get('/membership-status', [MembershipStatusController::class, 'index'])->name('membership-status.index');
            Route::patch('/membership-status/{member}', [MembershipStatusController::class, 'update'])->block()->name('membership-status.update');
            Route::patch('/members/{member}/deactivate', [MemberController::class, 'deactivate'])->block()->name('members.deactivate');
            Route::resource('members', MemberController::class);
            Route::resource('categories', CategoryController::class);
            Route::resource('levels', LevelController::class);
            Route::resource('transactions', TransactionController::class)->only(['index', 'create', 'store']);
            Route::patch('/transactions/{transaction}/approve', [TransactionController::class, 'approve'])->block()->name('transactions.approve');
            Route::patch('/transactions/{transaction}/cancel', [TransactionController::class, 'cancel'])->block()->name('transactions.cancel');
            Route::get('/transactions/{transaction}/payment', [PaymentController::class, 'create'])->block()->name('payments.create');
            Route::post('/payments', [PaymentController::class, 'store'])->block()->name('payments.store');
        });
    });
});

Route::get('/verify-email/status', [RegistrationController::class, 'status'])->middleware('throttle:20,1')->name('verification.status');
Route::get('/verify-email/correct', [RegistrationController::class, 'correction'])->name('verification.correction');
Route::post('/verify-email/correct', [RegistrationController::class, 'correct'])->middleware('throttle:3,1')->block()->name('verification.correct');
