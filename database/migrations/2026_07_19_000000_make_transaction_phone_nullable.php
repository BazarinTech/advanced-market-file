<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The original migration already declared `phone` nullable(), but the
     * live database's actual column was NOT NULL (schema drift from before
     * the migration history existed) — silently breaking every crypto
     * deposit, since that path never sets a phone. Confirmed live: crypto
     * deposits have a 100% failure rate (0 rows ever created, insert throws
     * before the row exists).
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE transaction MODIFY phone VARCHAR(13) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transaction MODIFY phone VARCHAR(13) NOT NULL DEFAULT ''");
    }
};
