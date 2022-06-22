<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Member\CRUDController;
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

Route::prefix('admin')->group(function() {

    // Login
    Route::post('login', [AuthController::class, 'login']);

    /* PROTECTED */
    Route::group(['middleware' => 'auth'], function () {


       // Member
       Route::prefix('members')->group(function() {

            // Create
            Route::post('create', [CRUDController::class, 'create']);
            // Delete
            Route::delete('delete/{$member_id}', [CRUDController::class, 'delete']);
            // Deactivate
            Route::patch('deactivate/{$member_id}', [CRUDController::class, 'deactivate']);
       });
    });
});

/******************************/
