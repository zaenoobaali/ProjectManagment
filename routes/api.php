<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProjectController;

// مسارات مفتوحة (لا تحتاج توكن للوصول إليها)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// مسارات محمية (تطلب التوكن الذي حصلنا عليه من الـ login أو الـ register)
Route::middleware('auth:sanctum')->group(function () {
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::post('/projects/add-member', [ProjectController::class, 'addMemberToProject']);
    Route::put('/projects/{project}', [ProjectController::class, 'update']);
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy']);
    
});


