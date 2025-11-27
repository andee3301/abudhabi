<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class MediaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $disk = $this->disk ?? 'public';

        return [
            'id' => $this->id,
            'journal_entry_id' => $this->journal_entry_id,
            'disk' => $disk,
            'path' => $this->path,
            'url' => Storage::disk($disk)->url($this->path),
            'mime_type' => $this->mime_type,
            'size' => $this->size,
            'caption' => $this->caption,
        ];
    }
}
