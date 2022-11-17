<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\ResponsibilityController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TeamController;
use Illuminate\Http\Request;
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

// Auth API
Route::name('auth.')->group(function () {
    Route::post('login', [UserController::class, 'login'])->name(
        'login'
    );
    Route::post('register', [UserController::class, 'register'])->name(
        'register'
    );

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('logout', [UserController::class, 'logout'])->name(
            'logout'
        );
        Route::get('user', [UserController::class, 'fetch'])->name(
            'fetch'
        );
    });
});

// Company API
Route::prefix('company')->middleware('auth:sanctum')->name('company.')->group(function () {
    Route::get('/', [CompanyController::class, 'fetch'])->name(
        'fetch'
    );
    Route::post('/', [CompanyController::class, 'create'])->name(
        'create'
    );
    Route::post('/update/{id}', [CompanyController::class, 'update'])->name(
        'update'
    );
});

// Team API
Route::prefix('team')->middleware('auth:sanctum')->name('team.')->group(function () {
    Route::get('/', [TeamController::class, 'fetch'])->name(
        'fetch'
    );
    Route::post('/', [TeamController::class, 'create'])->name(
        'create'
    );
    Route::post('/update/{id}', [TeamController::class, 'update'])->name(
        'update'
    );
    Route::delete('/{id}', [TeamController::class, 'destroy'])->name(
        'destroy'
    );
});

// Role API
Route::prefix('role')->middleware('auth:sanctum')->name('role.')->group(function () {
    Route::get('/', [RoleController::class, 'fetch'])->name(
        'fetch'
    );
    Route::post('/', [RoleController::class, 'create'])->name(
        'create'
    );
    Route::post('/update/{id}', [RoleController::class, 'update'])->name(
        'update'
    );
    Route::delete('/{id}', [RoleController::class, 'destroy'])->name(
        'destroy'
    );
});

// Responsibility API
Route::prefix('responsibility')->middleware('auth:sanctum')->name('responsibility.')->group(function () {
    Route::get('/', [ResponsibilityController::class, 'fetch'])->name(
        'fetch'
    );
    Route::post('/', [ResponsibilityController::class, 'create'])->name(
        'create'
    );
    Route::delete('/{id}', [ResponsibilityController::class, 'destroy'])->name(
        'destroy'
    );
});

// Employee API
Route::prefix('employee')->middleware('auth:sanctum')->name('employee.')->group(function () {
    Route::get('/', [EmployeeController::class, 'fetch'])->name(
        'fetch'
    );
    Route::post('/', [EmployeeController::class, 'create'])->name(
        'create'
    );
    Route::post('/update/{id}', [EmployeeController::class, 'update'])->name(
        'update'
    );
    Route::delete('/{id}', [EmployeeController::class, 'destroy'])->name(
        'destroy'
    );
});