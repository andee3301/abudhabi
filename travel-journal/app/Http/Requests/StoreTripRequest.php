<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'primary_location_name' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'cover_image_url' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'in:planned,ongoing,completed'],
            'companion_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
