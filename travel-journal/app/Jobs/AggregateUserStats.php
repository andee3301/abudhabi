<?php

namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AggregateUserStats implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [10, 30, 60];

    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $userId
    ) {
        $this->onQueue('stats');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $user = User::findOrFail($this->userId);

            // Aggregate stats
            $stats = [
                'total_trips' => $user->trips()->count(),
                'completed_trips' => $user->trips()->where('status', 'completed')->count(),
                'ongoing_trips' => $user->trips()->where('status', 'ongoing')->count(),
                'planned_trips' => $user->trips()->where('status', 'planned')->count(),
                'total_journal_entries' => $user->journalEntries()->count(),
                'countries_visited' => $user->trips()->distinct('country_code')->count('country_code'),
                'total_notes' => \App\Models\TripNote::where('user_id', $this->userId)->count(),
                'total_timeline_entries' => \App\Models\TripTimeline::where('user_id', $this->userId)->count(),
                'last_trip_date' => $user->trips()->max('end_date'),
                'next_trip_date' => $user->trips()
                    ->where('start_date', '>=', now())
                    ->min('start_date'),
            ];

            // Cache for 1 hour
            Cache::put("user:{$this->userId}:stats", $stats, 3600);

            Log::debug('User stats aggregated', [
                'user_id' => $this->userId,
                'stats' => $stats,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to aggregate user stats', [
                'user_id' => $this->userId,
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
        Log::error('AggregateUserStats job failed', [
            'user_id' => $this->userId,
            'error' => $exception->getMessage(),
        ]);
    }
}
