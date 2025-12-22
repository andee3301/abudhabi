<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateTripPdfExport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $backoff = [15, 45, 90];

    public $timeout = 180;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Trip $trip,
        public User $user
    ) {
        $this->onQueue('exports');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Load all trip data
            $this->trip->load([
                'journalEntries',
                'itineraryItems',
                'tripNotes',
                'timelineEntries',
                'countryVisits',
            ]);

            // Generate PDF (placeholder - would use a PDF library like DomPDF or Snappy)
            // $pdf = PDF::loadView('trips.export-pdf', ['trip' => $this->trip]);
            // $filename = "trip-{$this->trip->id}-" . now()->format('Y-m-d') . '.pdf';
            // Storage::put("exports/{$this->user->id}/{$filename}", $pdf->output());

            // Notify user (placeholder - would send email with download link)
            // Mail::to($this->user)->send(new TripPdfReady($this->trip, $filename));

            Log::info('Trip PDF export generated', [
                'trip_id' => $this->trip->id,
                'user_id' => $this->user->id,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to generate trip PDF', [
                'trip_id' => $this->trip->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateTripPdfExport job failed', [
            'trip_id' => $this->trip->id,
            'user_id' => $this->user->id,
            'error' => $exception->getMessage(),
        ]);

        // Notify user of failure (placeholder)
        // Mail::to($this->user)->send(new TripPdfFailed($this->trip));
    }
}
