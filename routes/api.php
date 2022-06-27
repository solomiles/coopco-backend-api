<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\MemberController;
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
    Route::post('login', [AuthController::class, 'login']);

    /* PROTECTED */
    Route::group(['middleware' => 'auth'], function () {

        // Member
        Route::prefix('members')->group(function () {

            // Create
            Route::post('create', [MemberController::class, 'create']);

            // Delete
            Route::delete('delete/{memberId}', [MemberController::class, 'delete']);

            // Activate/Deactivate
            Route::patch('activate/{memberId}/{status?}', [MemberController::class, 'activate']);

            // Get all members
            Route::get('/', [MemberController::class, 'getAll']);

            // Get one member
            Route::get('/{memberId}', [MemberController::class, 'getOne']);
       });
    });
});

/******************************/
