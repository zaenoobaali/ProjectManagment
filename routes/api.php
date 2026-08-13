<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;

// مسارات مفتوحة (لا تحتاج توكن للوصول إليها)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// مسارات محمية (تطلب التوكن الذي حصلنا عليه من الـ login أو الـ register)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('projects', ProjectController::class)->only(['index','store']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::post('/projects/add-member', [ProjectController::class, 'addMemberToProject']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);

    Route::apiResource('tasks', TaskController::class)->only(['index','store']);
    Route::get('/tasks/{task}', [TaskController::class, 'show']);
    Route::post('/tasks/assign', [TaskController::class, 'assignTask']);
    Route::put('/tasks/{task}', [TaskController::class, 'update']);
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy']);

    
});


