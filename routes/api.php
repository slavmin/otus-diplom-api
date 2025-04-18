<?php

declare(strict_types=1);

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\DocumentTemplate\DocumentTemplateController;
use App\Http\Controllers\Api\Import\ImportExcelController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return ['data' => $request->user()];
})->middleware('auth:sanctum');

// Auth routes
Route::group(['middleware' => ['guest:api', 'throttle:10,1']], static function (): void {
    Route::group(['prefix' => 'auth'], function (): void {
        Route::post('/login', LoginController::class)->name('login');
        Route::post('/register', RegisterController::class)->name('register');

        // Password Reset Routes...
        // Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail']);
        // Route::post('/password/reset', [ForgotPasswordController::class, 'reset']);
    });
});

// Authorized users routes
Route::group(['middleware' => 'auth:sanctum', 'throttle:30,1'], static function (): void {
    Route::apiResource('/templates', DocumentTemplateController::class);

    Route::post('/imports/excel', ImportExcelController::class)->name('imports.excel');
    Route::post('/exports/pdf', App\Http\Controllers\Api\Export\ExportPdfController::class)->name('exports.pdf');

    // Auth
    Route::group(['prefix' => 'auth'], function (): void {
        // Route::get('/user', ProfileController::class);
        Route::post('/logout', LogoutController::class)->name('logout');
    });
});
