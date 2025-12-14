<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTripRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'primary_location_name' => ['sometimes', 'string', 'max:255'],
            'city_id' => ['sometimes', 'nullable', 'integer', 'exists:cities,id'],
            'city' => ['sometimes', 'string', 'max:255'],
            'state_region' => ['sometimes', 'string', 'max:255'],
            'country_code' => ['sometimes', 'string', 'size:2'],
            'timezone' => ['sometimes', 'string', 'max:64'],
            'region_id' => ['sometimes', 'integer', 'exists:regions,id'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'cover_image_url' => ['nullable', 'string', 'max:1000'],
            'status' => ['sometimes', 'in:planned,ongoing,completed'],
            'companion_name' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:64'],
        ];
    }
}
