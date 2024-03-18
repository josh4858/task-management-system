<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\TaskController;


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

Route::group(['middleware' => ['auth:api']], function() {
    // To use these routes you have to be authenticated 
    Route::get('/tasks',[TaskController::class,'index']);
    Route::post('/tasks',[TaskController::class,'store']);
    Route::get('/tasks/{id}',[TaskController::class,'show']);
    Route::put('/tasks/{id}',[TaskController::class,'update']);
    Route::delete('/tasks/{id}',[TaskController::class,'destroy']);

    // Role API routes
    Route::get('/roles',[RoleController::class,'index']);
    Route::post('/roles',[RoleController::class,'store']);
    Route::get('/roles/{id}',[RoleController::class,'show']);
    Route::put('/roles/{id}',[RoleController::class,'update']);
    Route::delete('/roles/{id}',[RoleController::class,'destroy']);

    // User Routes
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}',[UserController::class,'show']);
    Route::put('/users/{id}',[UserController::class,'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);

});

// Unprotected routes which use will have throttles to prevent abuse
Route::post('/register',[UserController::class,'register']);
Route::post('/login',[UserController::class,'authenticate']);
Route::post('/logout', [UserController::class,'logout']);

