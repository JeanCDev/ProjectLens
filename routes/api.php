<?php

use App\Http\Controllers\Api\ApiEndpointController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EnvironmentController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\ReleaseController;
use App\Http\Controllers\Api\TeamMemberController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    Route::apiResource('projects', ProjectController::class);

    Route::apiResource('projects.releases', ReleaseController::class);
    Route::apiResource('projects.environments', EnvironmentController::class);
    Route::apiResource('projects.endpoints', ApiEndpointController::class);
    Route::apiResource('projects.team-members', TeamMemberController::class);
});
