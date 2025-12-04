<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (! Schema::hasColumn('journal_entries', 'entry_date')) {
                $table->date('entry_date')->nullable()->after('user_id');
                $table->index(['trip_id', 'entry_date']);
            }
        });

        // Backfill entry_date from logged_at if it exists
        if (Schema::hasColumn('journal_entries', 'entry_date') && Schema::hasColumn('journal_entries', 'logged_at')) {
            DB::table('journal_entries')
                ->whereNull('entry_date')
                ->update(['entry_date' => DB::raw('DATE(logged_at)')]);
        }
    }

    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            if (Schema::hasColumn('journal_entries', 'entry_date')) {
                $table->dropIndex(['trip_id', 'entry_date']);
                $table->dropColumn('entry_date');
            }
        });
    }
};
