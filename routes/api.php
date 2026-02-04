<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AiController;

Route::get('/ai/health', [AiController::class, 'health']);
