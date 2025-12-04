<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'trip_id' => ['required', 'exists:trips,id'],
            'title' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'mood' => ['nullable', 'string', 'max:50'],
            'content' => ['required', 'string'],
            'photo_urls' => ['nullable', 'array'],
            'photo_urls.*' => ['string'],
        ];
    }
}
