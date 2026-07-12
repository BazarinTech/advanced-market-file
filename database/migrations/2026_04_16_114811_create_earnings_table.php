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
        Schema::create('earnings', function (Blueprint $table) {
            $table->id('ID');
            $table->string('email', 255);
            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('withdraw', 15, 2)->default(0);
            $table->decimal('referral', 15, 2)->default(0);
            $table->decimal('bonus', 15, 2)->default(0);
            $table->decimal('deposit', 15, 2)->default(0);
            $table->decimal('totals', 15, 2)->default(0);
            $table->tinyInteger('roll')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('earnings');
    }
};
