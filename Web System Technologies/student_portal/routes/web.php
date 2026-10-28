<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', [TaskController::class, 'index'])
    ->name('tasks.index');

Route::redirect('/tasks', '/');

Route::post('/tasks', [TaskController::class, 'store'])
    ->name('tasks.store');

Route::get('/dashboard', [MemberController::class, 'index'])->name('dashboard');
Route::patch('/members/{member}/reactivate', [MemberController::class, 'reactivate'])->name('members.reactivate');
Route::resource('members', MemberController::class)->except('destroy');
Route::delete('/members/{member}', [MemberController::class, 'destroy'])->name('members.destroy');

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
