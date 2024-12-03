<?php

use App\Http\Controllers\Api\AuthController;
use App\Modules\General\Controllers\GeneralController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/general')->middleware('auth:general')->group(function () {
    Route::get('test', [GeneralController::class, 'index']);

});


