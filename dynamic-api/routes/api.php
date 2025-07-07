<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContentEntryController;

Route::post('/content/{slug}', [ContentEntryController::class, 'store']);
