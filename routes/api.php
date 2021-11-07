<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InvitationController;

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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/invite/register', [InvitationController::class, 'register'])->name('invite.register');
Route::post('/register/confirm', [InvitationController::class, 'confirmRegistration'])->name('register.confirm');


Route::group(['middleware' => 'auth:sanctum'], function () {

    Route::group(['middleware' => 'isAdmin'], function () {
        Route::post('/admin/invite', [InvitationController::class, 'createInvitationLink']);
    });

    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});
