<?php

namespace App\Jobs;

use App\Models\JournalEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class ProcessJournalMedia implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = [10, 30, 60];
    public $timeout = 120;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public JournalEntry $journalEntry,
        public array $photoUrls
    ) {
        $this->onQueue('media');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $optimizedUrls = [];

        foreach ($this->photoUrls as $url) {
            try {
                // Download and optimize image
                $contents = file_get_contents($url);

                if ($contents === false) {
                    Log::warning("Failed to download image: {$url}");
                    continue;
                }

                $filename = basename(parse_url($url, PHP_URL_PATH));
                $path = "journal/{$this->journalEntry->id}/{$filename}";

                // Store original
                Storage::put($path, $contents);

                // Create WebP version (if image manipulation library available)
                // $webpPath = str_replace(['.jpg', '.jpeg', '.png'], '.webp', $path);
                // Image::make($contents)->encode('webp', 80)->save(storage_path("app/{$webpPath}"));

                $optimizedUrls[] = Storage::url($path);
            } catch (\Exception $e) {
                Log::error("Failed to process journal media: {$url}", [
                    'error' => $e->getMessage(),
                    'journal_entry_id' => $this->journalEntry->id,
                ]);
            }
        }

        // Update journal entry with optimized URLs
        if (! empty($optimizedUrls)) {
            $this->journalEntry->update([
                'photo_urls' => array_merge($this->journalEntry->photo_urls ?? [], $optimizedUrls),
            ]);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('ProcessJournalMedia job failed', [
            'journal_entry_id' => $this->journalEntry->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
