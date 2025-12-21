<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'body' => ['sometimes', 'string'],
            'note_date' => ['sometimes', 'date', 'nullable'],
            'is_pinned' => ['sometimes', 'boolean'],
            'tags' => ['sometimes', 'array', 'nullable'],
            'metadata' => ['sometimes', 'array', 'nullable'],
        ];
    }
}
