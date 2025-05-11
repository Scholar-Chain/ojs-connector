<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('me', \App\Http\Controllers\MeController::class)->middleware('auth:api');

Route::prefix('ojs')->group(function () {
    Route::post('register', [\App\Http\Controllers\OJS\AuthController::class, 'register']);

    Route::get('journals', [\App\Http\Controllers\OJS\JournalController::class, 'index']);
    Route::get('journals/{journal}', [\App\Http\Controllers\OJS\JournalController::class, 'show']);

    Route::middleware('auth:api')->group(function () {
        Route::get('submissions', [\App\Http\Controllers\OJS\SubmissionController::class, 'index']);
        Route::get('submissions/{submission}', [\App\Http\Controllers\OJS\SubmissionController::class, 'show']);
        Route::post('submissions/{journal}', [\App\Http\Controllers\OJS\SubmissionController::class, 'store']);
    });
});
