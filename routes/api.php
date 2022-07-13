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
            Route::post('create', [App\Http\Controllers\Admin\MemberController::class, 'create']);

            // Delete
            Route::delete('delete/{memberId}', [App\Http\Controllers\Admin\MemberController::class, 'delete']);

            // Activate/Deactivate
            Route::patch('activate/{memberId}/{status?}', [App\Http\Controllers\Admin\MemberController::class, 'activate']);

            // Search member
            Route::get('search', [App\Http\Controllers\Admin\MemberController::class, 'search']);

            // Get all members
            Route::get('/', [App\Http\Controllers\Admin\MemberController::class, 'getAll']);

            // Get one member
            Route::get('/{memberId}', [App\Http\Controllers\Admin\MemberController::class, 'getOne']);

            // Update member
            Route::put('update/{memberId}', [App\Http\Controllers\Admin\MemberController::class, 'update']);
        });

        // Update Admin
        Route::put('update/{adminId}', [App\Http\Controllers\Admin\CRUDController::class, 'update']);

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

<<<<<<< HEAD
<<<<<<< HEAD
    /* PROTECTED */
    Route::group(['middleware' => 'auth:mobile-api'], function () {
        // Update member
        Route::put('update/{memberId}', [App\Http\Controllers\Member\CrudController::class, 'update']);
    });
=======
    // Update member
<<<<<<< HEAD
    Route::put('update/{memberId}', [App\Http\Controllers\Member\MemberController::class, 'update']);
>>>>>>> 30c9e6d (Add CrudController for member)
=======
    Route::put('update/{memberId}', [App\Http\Controllers\Member\CrudController::class, 'update']);
>>>>>>> d6e6cdb (Install and test crazybooot/base64-validation validator library)
=======
    /* PROTECTED */
    Route::group(['middleware' => 'auth:mobile-api'], function () {
        // Update member
        Route::put('update/{memberId}', [App\Http\Controllers\Member\CrudController::class, 'update']);
    });
>>>>>>> f8da978 (Add route for updating member)
});

/******************************/
