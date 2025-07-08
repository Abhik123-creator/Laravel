<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use App\Services\CaptchaService;
use Illuminate\Http\Request;

class EmbedController extends Controller
{
    /**
     * Display the embeddable form
     */
    public function show($slug)
    {
        $form = ContentType::where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->first();

        if (!$form) {
            abort(404, 'Form not found or inactive');
        }

        // Generate captcha if enabled
        $captcha = null;
        if ($form->captcha_enabled) {
            $captcha = CaptchaService::generate($form->captcha_difficulty);
        }

        return view('embed.form', compact('form', 'captcha'));
    }

    /**
     * Handle form submission for embedded forms
     */
    public function submit(Request $request, $slug)
    {
        // Use the same logic as PublicFormController
        $controller = new PublicFormController();
        $response = $controller->submit($request, $slug);
        
        // If successful, redirect to embedded success page
        if ($response->getStatusCode() === 302 && $response->getTargetUrl()) {
            return redirect()->route('embed.form.success', $slug)
                ->with('success', 'Your response has been submitted successfully!')
                ->with('entry_id', session('entry_id'));
        }
        
        return $response;
    }

    /**
     * Show embedded success page
     */
    public function success($slug)
    {
        $form = ContentType::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$form) {
            abort(404, 'Form not found or inactive');
        }

        return view('embed.form-success', compact('form'));
    }
}
