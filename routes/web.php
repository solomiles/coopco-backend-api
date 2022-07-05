<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

/** PASSWORD RESET ROUTE FOR MEMBERS **/

Route::name('member.')->prefix('member')->group(function () {

    // Render password reset form
    Route::get('reset-password/{token}', [App\Http\Controllers\Member\AuthController::class, 'passwordResetForm'])->name('reset-password-form');
    
    // Update password
    Route::post('update-password', [App\Http\Controllers\Member\AuthController::class, 'updatePassword'])->name('update-password');

    // Render password success page
    Route::get('password-reset-success', function () {
        return view('password-reset.password-success');
    })->name('password-success');
});

/******************************/
