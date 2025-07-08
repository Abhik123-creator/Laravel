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
            switch ($field->type) {
                case 'string':
                    $rules[$field->name] = 'required|string';
                    break;
                case 'integer':
                    $rules[$field->name] = 'required|integer';
                    break;
                case 'boolean':
                    $rules[$field->name] = 'required|boolean';
                    break;
                case 'date':
                    $rules[$field->name] = 'required|date';
                    break;
                case 'text':
                    $rules[$field->name] = 'required|string';
                    break;
                case 'email':
                    $rules[$field->name] = 'required|email';
                    break;
                default:
                    $rules[$field->name] = 'required';
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
