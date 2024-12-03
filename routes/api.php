<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Models\DocumentHeader;
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



Route::prefix('v1')->group(function () {
    Route::post('user-register', [AuthController::class, 'register']);
    Route::post('user-login', [AuthController::class, 'login']);

    // This group is protected by the 'throttle' middleware and authentication check
    Route::middleware(['throttle:api', 'auth:api'])->group(function () {
     
        Route::post('/store-key-document', [DocumentController::class, 'storeDocumentKey']);
        Route::get('documents/versions/{id}', [DocumentController::class, 'getVersions']);
        Route::delete('documents/{id}', [DocumentController::class, 'destroy']);
        Route::post('/documents/search', [DocumentController::class, 'searchDocuments']);
        Route::get('/documents/{id}',[DocumentController::class,'fetchAndCombineDocument']);
        Route::get('search-documents',[DocumentController::class,'searchDocumentsWithFilters']);
        Route::post('store-update-document',[DocumentController::class,'storeOrUpdateDocumentKeyUsingQueue']);
    });
});




