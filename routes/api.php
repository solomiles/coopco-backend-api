<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

/** COOPERATIVE ADMIN ROUTES **/

Route::prefix('admin')->group(function () {

    // Login
    Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'login']);

    /* PROTECTED */
    Route::group(['middleware' => 'auth:admin-web-api'], function () {

        // Member
        Route::prefix('members')->group(function () {

            // Create
            Route::post('/', [App\Http\Controllers\Admin\MemberController::class, 'create']);

            // Delete
            Route::delete('/{memberId}', [App\Http\Controllers\Admin\MemberController::class, 'delete']);

            // Activate/Deactivate
            Route::patch('status/{memberId}/{status}', [App\Http\Controllers\Admin\MemberController::class, 'updateStatus']);

            // Search member
            Route::get('search', [App\Http\Controllers\Admin\MemberController::class, 'search']);

            // Get all members
            Route::get('/', [App\Http\Controllers\Admin\MemberController::class, 'getAll']);

            // Get one member
            Route::get('/{memberId}', [App\Http\Controllers\Admin\MemberController::class, 'getOne']);

            // Update member
            Route::put('/{memberId}', [App\Http\Controllers\Admin\MemberController::class, 'update']);
        });

        // Update Admin
        Route::put('', [App\Http\Controllers\Admin\CRUDController::class, 'update']);

        // Send message
        Route::post('messages', [App\Http\Controllers\Shared\MessageController::class, 'send']);

    });
});

/******************************/

/** COOPCO SUPERADMIN ROUTES **/

Route::prefix('superadmin')->group(function () {

    // Login
    Route::post('login', [App\Http\Controllers\Superadmin\AuthController::class, 'login']);

});
/******************************/

/** COOPERATIVE MEMBER ROUTES **/

Route::prefix('member')->group(function () {

    // Login
    Route::post('login', [App\Http\Controllers\Member\AuthController::class, 'login']);

    // Password Reset Email
    Route::post('reset-password', [App\Http\Controllers\Member\AuthController::class, 'sendPasswordResetEmail']);

    /* PROTECTED */
    Route::group(['middleware' => 'auth:mobile-api'], function () {
        // Update member
        Route::put('', [App\Http\Controllers\Member\CRUDController::class, 'update']);

        // Send message
        Route::post('messages', [App\Http\Controllers\Shared\MessageController::class, 'send']);
    });
});

/******************************/
