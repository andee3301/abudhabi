<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip trips.end_date - index already exists

        // For SQLite in tests, just try to add indexes without checking
        // For MySQL/production, check if index exists first
        $isSqlite = Schema::getConnection()->getDriverName() === 'sqlite';

        Schema::table('journal_entries', function (Blueprint $table) use ($isSqlite) {
            if ($isSqlite || !$this->indexExists('journal_entries', 'journal_entries_user_id_entry_date_index')) {
                $table->index(['user_id', 'entry_date']);
            }
        });

        Schema::table('trip_notes', function (Blueprint $table) use ($isSqlite) {
            if ($isSqlite || !$this->indexExists('trip_notes', 'trip_notes_user_id_note_date_index')) {
                $table->index(['user_id', 'note_date']);
            }
            if ($isSqlite || !$this->indexExists('trip_notes', 'trip_notes_is_pinned_index')) {
                $table->index('is_pinned');
            }
        });

        Schema::table('trip_timelines', function (Blueprint $table) use ($isSqlite) {
            if ($isSqlite || !$this->indexExists('trip_timelines', 'trip_timelines_user_id_occurred_at_index')) {
                $table->index(['user_id', 'occurred_at']);
            }
        });

        // Skip itinerary_items as indexes already exist in base migration
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $conn = Schema::getConnection();

        // SQLite doesn't have INFORMATION_SCHEMA, skip check
        if ($conn->getDriverName() === 'sqlite') {
            return false;
        }

        $dbName = $conn->getDatabaseName();

        $result = $conn->selectOne(
            "SELECT COUNT(1) as count
             FROM INFORMATION_SCHEMA.STATISTICS
             WHERE table_schema = ?
             AND table_name = ?
             AND index_name = ?",
            [$dbName, $table, $indexName]
        );

        return $result->count > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropIndex(['end_date']);
        });

        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'entry_date']);
        });

        Schema::table('trip_notes', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'note_date']);
            $table->dropIndex(['is_pinned']);
        });

        Schema::table('trip_timelines', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'occurred_at']);
        });

        // Skip itinerary_items - indexes managed in base migration
    }
};
