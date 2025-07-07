<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContentType;
use App\Models\ContentEntry;
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

        // Build dynamic validation rules
        $rules = [];
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

        // Save content entry
        $entry = ContentEntry::create([
            'content_type_id' => $contentType->id,
            'data' => $request->only(array_keys($rules)),
        ]);

        return response()->json(['message' => 'Entry saved successfully.', 'id' => $entry->id], 201);
    }
}
