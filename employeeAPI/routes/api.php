<?php

use App\Http\Controllers\APIController;
use Illuminate\Support\Facades\Route;

Route::get('/employees/{page?}', [APIController::class, 'getEmployees']);
Route::get('/import/employees', [APIController::class, 'importEmployees']);
Route::get('/value/{matricula}/{mes}', [APIController::class, 'getValueByMonth']);

Route::post('/hours/{matricula}', [APIController::class, 'storeHours']);
Route::post('/value/{matricula}', [APIController::class, 'updateValue']);
