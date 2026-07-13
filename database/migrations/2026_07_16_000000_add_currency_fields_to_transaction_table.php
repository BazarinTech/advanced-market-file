<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->string('method', 10)->default('mpesa')->after('type');
            $table->string('payout_address', 64)->nullable()->after('phone');
            $table->decimal('fx_rate', 10, 4)->nullable()->after('RecAmount');
        });
    }

    public function down(): void
    {
        Schema::table('transaction', function (Blueprint $table) {
            $table->dropColumn(['method', 'payout_address', 'fx_rate']);
        });
    }
};
