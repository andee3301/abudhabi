<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreItineraryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:transport,housing,activity,note'],
            'title' => ['required', 'string', 'max:255'],
            'start_datetime' => ['nullable', 'date'],
            'end_datetime' => ['nullable', 'date', 'after_or_equal:start_datetime'],
            'location_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
            'price' => ['nullable', 'numeric'],
            'currency' => ['nullable', 'string', 'max:3'],
            'status' => ['nullable', 'string', 'max:50'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
