<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

/** PASSWORD RESET ROUTE FOR MEMBERS **/

Route::name('member.')->prefix('member')->group(function () {
    Route::get('reset-password/{token}', [App\Http\Controllers\Member\AuthController::class, 'resetPassword'])->name('reset-password');

    Route::post('reset', [App\Http\Controllers\Member\AuthController::class, 'reset'])->name('reset');

    Route::get('password-success', function () {
        return view('password-reset.password-success');
    })->name('password-success');
});

/******************************/
