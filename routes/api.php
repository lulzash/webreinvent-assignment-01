<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

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

Route::apiResource('tasks', TaskController::class);
Route::patch('tasks/{task}/complete', [TaskController::class, 'complete']);
Route::patch('tasks/{task}/incomplete', [TaskController::class, 'incomplete']);

