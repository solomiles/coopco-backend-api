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
    Route::group(['middleware' => ['auth:admin-web-api', 'scopes:admins']], function () {

        // Update Admin
        Route::put('', [App\Http\Controllers\Admin\CRUDController::class, 'update']);

        // Members
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

            // Create bulk
            Route::post('/bulk', [App\Http\Controllers\Admin\MemberController::class, 'createBulk']);
        });

        // Messages
        Route::prefix('messages')->group(function () {
            // Send message
            Route::post('', [App\Http\Controllers\Shared\MessageController::class, 'send']);

            // Get received messages
            Route::get('', [App\Http\Controllers\Shared\MessageController::class, 'getReceived']);

            // Get sent messages
            Route::get('sent', [App\Http\Controllers\Shared\MessageController::class, 'getSent']);

            // Delete sent messages
            Route::delete('/{messageId}', [App\Http\Controllers\Shared\MessageController::class, 'delete']);

            // Mark message as seen
            Route::patch('seen/{messageId}', [App\Http\Controllers\Shared\MessageController::class, 'markAsSeen']);

            // Mark message as read
            Route::patch('read/{messageId}', [App\Http\Controllers\Shared\MessageController::class, 'markAsRead']);
        });

        // News
        Route::prefix('news')->group(function () {
            // Create news post
            Route::post('', [App\Http\Controllers\Shared\NewsController::class, 'create']);

            // Get news
            Route::get('', [App\Http\Controllers\Shared\NewsController::class, 'get']);

            // Update news post
            Route::put('/{newsId}', [App\Http\Controllers\Shared\NewsController::class, 'update']);

            // Delete news post
            Route::delete('/{newsId}', [App\Http\Controllers\Shared\NewsController::class, 'delete']);
        });

        // Loans
        Route::prefix('loans')->group(function () {

            // Get a single loan
            Route::get('/{loanId}', [App\Http\Controllers\Shared\LoanController::class, 'getOne']);
        });
    });
});

/******************************/

/** COOPCO SUPERADMIN ROUTES **/

Route::prefix('superadmin')->group(function () {
    Route::group(['middleware' => ['switchschema']], function () {
        // Login
        Route::post('login', [App\Http\Controllers\Superadmin\AuthController::class, 'login']);

        // Create cooperative
        Route::post('/cooperative',  [App\Http\Controllers\Superadmin\CooperativeController::class, 'create']);
    });
});
/******************************/

/** COOPERATIVE MEMBER ROUTES **/

Route::prefix('member')->group(function () {

    // Login
    Route::post('login', [App\Http\Controllers\Member\AuthController::class, 'login']);

    // Password Reset Email
    Route::post('reset-password', [App\Http\Controllers\Member\AuthController::class, 'sendPasswordResetEmail']);

    /* PROTECTED */
    Route::group(['middleware' => ['auth:mobile-api', 'scopes:members']], function () {
        // Update member
        Route::put('', [App\Http\Controllers\Member\CRUDController::class, 'update']);

        Route::prefix('messages')->group(function () {
            // Send message
            Route::post('/', [App\Http\Controllers\Shared\MessageController::class, 'send']);

            // Get received messages
            Route::get('/', [App\Http\Controllers\Shared\MessageController::class, 'getReceived']);

            // Get sent messages
            Route::get('sent', [App\Http\Controllers\Shared\MessageController::class, 'getSent']);

            // Delete sent messages
            Route::delete('/{messageId}', [App\Http\Controllers\Shared\MessageController::class, 'delete']);

            // Mark message as seen
            Route::patch('seen/{messageId}', [App\Http\Controllers\Shared\MessageController::class, 'markAsSeen']);

            // Mark message as read
            Route::patch('read/{messageId}', [App\Http\Controllers\Shared\MessageController::class, 'markAsRead']);
        });

        Route::prefix('loans')->group(function () {

            // Apply for a loan
            Route::post('apply/{loanId}', [App\Http\Controllers\Shared\LoanApplicationController::class, 'apply']);
        });

        Route::prefix('news')->group(function () {

            // Get news
            Route::get('', [App\Http\Controllers\Shared\NewsController::class, 'get']);
        });
    });
});

/******************************/
