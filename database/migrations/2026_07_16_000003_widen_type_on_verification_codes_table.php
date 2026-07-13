<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 'withdrawal_account_update' (25 chars) no longer fits in varchar(20).
        DB::statement('ALTER TABLE verification_codes MODIFY type VARCHAR(40) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE verification_codes MODIFY type VARCHAR(20) NOT NULL');
    }
};
