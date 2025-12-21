<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripTimelineRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'nullable'],
            'occurred_at' => ['sometimes', 'date', 'nullable'],
            'type' => ['sometimes', 'string', 'max:100', 'nullable'],
            'location_name' => ['sometimes', 'string', 'max:255', 'nullable'],
            'tags' => ['sometimes', 'array', 'nullable'],
            'metadata' => ['sometimes', 'array', 'nullable'],
        ];
    }
}
