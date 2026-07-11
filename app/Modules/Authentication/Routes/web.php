<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Authentication\Livewire\Login;
use App\Modules\Authentication\Livewire\Register;
use App\Modules\Authentication\Livewire\ForgotPassword;
use App\Modules\Authentication\Livewire\ResetPassword;
use App\Modules\Authentication\Livewire\OtpVerification;
use Illuminate\Support\Facades\Auth;

Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/admin/login', Login::class)->name('admin.login');
    Route::get('/register', Register::class)->name('auth.register');
    Route::get('/forgot-password', ForgotPassword::class)->name('auth.forgot-password');
    Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
    Route::get('/verify-otp', OtpVerification::class)->name('auth.verify-otp');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('corporate.index');
    })->name('auth.logout');
});
