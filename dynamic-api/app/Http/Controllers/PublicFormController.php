<?php

namespace App\Http\Controllers;

use App\Models\ContentType;
use App\Models\ContentEntry;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicFormController extends Controller
{
    /**
     * Display the public form
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

        return view('public.form', compact('form', 'captcha'));
    }

    /**
     * Handle form submission
     */
    public function submit(Request $request, $slug)
    {
        $form = ContentType::where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->first();

        if (!$form) {
            return back()->withErrors(['form' => 'Form not found or inactive']);
        }

        // Build dynamic validation rules
        $rules = [];
        $messages = [];
        
        // Add captcha validation if enabled
        if ($form->captcha_enabled) {
            $rules['captcha_answer'] = 'required|numeric';
            $messages['captcha_answer.required'] = 'Please solve the captcha.';
            $messages['captcha_answer.numeric'] = 'Captcha answer must be a number.';
        }
        
        foreach ($form->fields as $field) {
            $fieldRules = [];
            
            // Add required rule if needed (assuming all fields are required for now)
            $fieldRules[] = 'required';
            
            // Add type-specific rules
            switch ($field->type) {
                case 'string':
                case 'text':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
                case 'integer':
                    $fieldRules[] = 'integer';
                    break;
                case 'boolean':
                    $fieldRules[] = 'boolean';
                    break;
                case 'date':
                    $fieldRules[] = 'date';
                    break;
                case 'email':
                    $fieldRules[] = 'email';
                    break;
            }
            
            $rules[$field->name] = implode('|', $fieldRules);
            $messages[$field->name . '.required'] = $field->label . ' is required.';
        }

        // Validate the request
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput();
        }

        // Verify captcha if enabled
        if ($form->captcha_enabled) {
            if (!CaptchaService::verify($request->input('captcha_answer'), $request->input('captcha_id'))) {
                return back()
                    ->withErrors(['captcha_answer' => 'Incorrect captcha answer. Please try again.'])
                    ->withInput();
            }
        }

        // Save the entry
        $entry = ContentEntry::create([
            'content_type_id' => $form->id,
            'data' => $request->only(array_keys($rules)),
        ]);

        return redirect()->route('public.form.success', $slug)
            ->with('success', 'Your response has been submitted successfully!')
            ->with('entry_id', $entry->id);
    }

    /**
     * Show success page
     */
    public function success($slug)
    {
        $form = ContentType::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$form) {
            abort(404, 'Form not found or inactive');
        }

        return view('public.form-success', compact('form'));
    }
}
