<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentType;
use App\Models\ContentEntry;
use App\Services\CaptchaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContentEntryController extends Controller
{
    public function store(Request $request, $slug)
    {
        // Find the Content Type
        $contentType = ContentType::where('slug', $slug)->with('fields')->first();

        if (! $contentType) {
            return response()->json(['error' => 'Content type not found.'], 404);
        }

        // Check if form is active
        if (!$contentType->is_active) {
            return response()->json(['error' => 'This form is currently inactive.'], 422);
        }

        // Build dynamic validation rules
        $rules = [];
        
        // Add captcha validation if enabled
        if ($contentType->captcha_enabled) {
            $rules['captcha_answer'] = 'required|numeric';
            $rules['captcha_id'] = 'required|string';
        }
        
        foreach ($contentType->fields as $field) {
            $fieldRules = [];
            
            // Add required rule if field is marked as required
            if ($field->required ?? true) {
                $fieldRules[] = 'required';
            }
            
            switch ($field->type) {
                case 'string':
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
                case 'text':
                    $fieldRules[] = 'string';
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
                        if ($field->required ?? true) {
                            $fieldRules[] = 'min:1'; // At least one selection required if field is required
                        }
                        $rules[$field->name . '.*'] = 'in:' . implode(',', $allowedValues);
                    }
                    break;
                default:
                    if ($field->required ?? true) {
                        $fieldRules[] = 'required';
                    }
            }
            
            if (!empty($fieldRules)) {
                $rules[$field->name] = implode('|', $fieldRules);
            }
        }

        // Validate request
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Verify captcha if enabled
        if ($contentType->captcha_enabled) {
            if (!CaptchaService::verify($request->input('captcha_answer'), $request->input('captcha_id'))) {
                return response()->json(['errors' => ['captcha_answer' => ['Incorrect captcha answer.']]], 422);
            }
        }

        // Save content entry (exclude captcha fields from data)
        $dataFields = array_keys($rules);
        if ($contentType->captcha_enabled) {
            $dataFields = array_diff($dataFields, ['captcha_answer', 'captcha_id']);
        }
        
        $entry = ContentEntry::create([
            'content_type_id' => $contentType->id,
            'data' => $request->only($dataFields),
        ]);

        return response()->json(['message' => 'Entry saved successfully.', 'id' => $entry->id], 201);
    }
}
