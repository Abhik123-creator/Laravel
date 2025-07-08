<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ContentEntryController;
use App\Services\CaptchaService;

Route::post('/forms/{slug}/entries', [ContentEntryController::class, 'store']);
Route::post('/content/{slug}', [ContentEntryController::class, 'store']); // Legacy route for backward compatibility

// Captcha API routes
Route::get('/captcha/{slug}', function ($slug) {
    $contentType = \App\Models\ContentType::where('slug', $slug)->first();
    
    if (!$contentType || !$contentType->captcha_enabled) {
        return response()->json(['error' => 'Captcha not enabled for this form.'], 422);
    }
    
    return response()->json(CaptchaService::generate($contentType->captcha_difficulty));
})->name('api.captcha.get');
