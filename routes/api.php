<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

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

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);
Route::post('user/details', [ApiController::class, 'get_user']);
Route::post('user/tickets', [ApiController::class, 'getAllAssignedTickets']);

Route::post('user/login', [ApiController::class, 'authenticateByOrderPhone']);
Route::post('user/ticket', [ApiController::class, 'getAllAssignedTicketsByOrderIdAndPhone']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
