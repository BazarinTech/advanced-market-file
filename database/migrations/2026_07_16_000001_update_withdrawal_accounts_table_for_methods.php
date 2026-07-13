<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawal_accounts', function (Blueprint $table) {
            $table->dropUnique(['email']);
            $table->string('method', 10)->default('mpesa')->after('email');
            $table->string('crypto_address', 64)->nullable()->after('phone');
        });

        // Raw SQL to avoid a doctrine/dbal dependency for column modification.
        DB::statement('ALTER TABLE withdrawal_accounts MODIFY phone VARCHAR(20) NULL');
        DB::statement('ALTER TABLE withdrawal_accounts MODIFY name VARCHAR(100) NULL');

        Schema::table('withdrawal_accounts', function (Blueprint $table) {
            $table->unique(['email', 'method']);
        });
    }

    public function down(): void
    {
        Schema::table('withdrawal_accounts', function (Blueprint $table) {
            $table->dropUnique(['email', 'method']);
            $table->dropColumn(['method', 'crypto_address']);
            $table->unique('email');
        });

        DB::statement('ALTER TABLE withdrawal_accounts MODIFY phone VARCHAR(20) NOT NULL');
        DB::statement('ALTER TABLE withdrawal_accounts MODIFY name VARCHAR(100) NOT NULL');
    }
};
