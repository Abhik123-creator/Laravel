<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\PublicFormController;
use App\Http\Controllers\EmbedController;
use App\Services\CaptchaService;

Route::get('/', function () {
    return view('welcome');
});

// Public form routes
Route::prefix('forms')->group(function () {
    Route::get('{slug}', [PublicFormController::class, 'show'])->name('public.form.show');
    Route::post('{slug}', [PublicFormController::class, 'submit'])->name('public.form.submit');
    Route::get('{slug}/success', [PublicFormController::class, 'success'])->name('public.form.success');
});

// Embeddable form routes
Route::prefix('embed')->group(function () {
    Route::get('forms/{slug}', [EmbedController::class, 'show'])->name('embed.form.show');
    Route::post('forms/{slug}', [EmbedController::class, 'submit'])->name('embed.form.submit');
    Route::get('forms/{slug}/success', [EmbedController::class, 'success'])->name('embed.form.success');
});

// Captcha routes
Route::get('/captcha/refresh/{slug?}', function ($slug = null) {
    try {
        // If slug is provided, get the form's captcha difficulty
        $difficulty = 'medium'; // default
        
        if ($slug) {
            $form = \App\Models\ContentType::where('slug', $slug)->where('is_active', true)->first();
            if ($form && $form->captcha_enabled) {
                $difficulty = $form->captcha_difficulty;
            }
        }
        
        $captcha = CaptchaService::generate($difficulty);
        
        return response()->json($captcha);
        
    } catch (\Exception $e) {
        Log::error('Captcha refresh error: ' . $e->getMessage());
        return response()->json(['error' => 'Failed to generate captcha'], 500);
    }
})->name('captcha.refresh');
