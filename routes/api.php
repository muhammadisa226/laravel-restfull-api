<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);
Route::get('/contacts', [ContactController::class, 'getAll']);
Route::middleware(ApiAuthMiddleware::class)->group(function () {
    Route::get('/users/current', [UserController::class, 'get']);
    Route::patch('/users/current', [UserController::class, 'update']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    // contacts
    Route::post('/contacts', [ContactController::class, 'create']);
    Route::get('/contacts/search', [ContactController::class, 'search']);
    Route::get('/contacts/{id}', [ContactController::class, 'get'])->where('id', '[0-9]+');
    Route::put('/contacts/{id}', [ContactController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/contacts/{id}', [ContactController::class, 'remove'])->where('id', '[0-9]+');

    // Address
    Route::get('/contacts/{idContact}/addresses', [AddressController::class, 'list'])->where('idContact', '[0-9]+');
    Route::post('/contacts/{idContact}/addresses', [AddressController::class, 'create'])->where('idContact', '[0-9]+');
    Route::get('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'get'])->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
    Route::put('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'update'])->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
    Route::delete('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class, 'remove'])->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
});
