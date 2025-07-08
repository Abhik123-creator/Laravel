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
            
            // Add required rule if field is marked as required
            if ($field->required ?? true) {
                $fieldRules[] = 'required';
            }
            
            // Add type-specific rules
            switch ($field->type) {
                case 'string':
                    $fieldRules[] = 'string';
                    $fieldRules[] = 'max:255';
                    break;
                case 'text':
                    $fieldRules[] = 'string';
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
                case 'radio':
                case 'select':
                    // Validate that the selected value is one of the allowed options
                    if ($field->options && is_array($field->options)) {
                        $allowedValues = array_column($field->options, 'value');
                        $fieldRules[] = 'in:' . implode(',', $allowedValues);
                    }
                    break;
                case 'checkbox':
                    // For checkboxes, validate that it's an array and all values are allowed
                    $fieldRules[] = 'array';
                    if ($field->options && is_array($field->options)) {
                        $allowedValues = array_column($field->options, 'value');
                        $fieldRules[] = 'min:1'; // At least one selection required if field is required
                        $rules[$field->name . '.*'] = 'in:' . implode(',', $allowedValues);
                    }
                    break;
            }
            
            if (!empty($fieldRules)) {
                $rules[$field->name] = implode('|', $fieldRules);
            }
            
            // Custom error messages
            if ($field->required ?? true) {
                $messages[$field->name . '.required'] = ($field->label ?: ucfirst($field->name)) . ' is required.';
            }
            
            if ($field->type === 'checkbox') {
                $messages[$field->name . '.min'] = 'Please select at least one ' . strtolower($field->label ?: $field->name) . '.';
            }
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
