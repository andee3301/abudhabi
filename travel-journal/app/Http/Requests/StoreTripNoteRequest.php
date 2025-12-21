<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'note_date' => ['nullable', 'date'],
            'is_pinned' => ['sometimes', 'boolean'],
            'tags' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
