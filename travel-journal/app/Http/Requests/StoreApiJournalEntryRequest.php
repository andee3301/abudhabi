<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApiJournalEntryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'entry_date' => ['required', 'date'],
            'body' => ['required', 'string'],
            'mood' => ['nullable', 'string', 'max:50'],
            'photo_urls' => ['nullable', 'array'],
            'photo_urls.*' => ['string'],
        ];
    }
}
