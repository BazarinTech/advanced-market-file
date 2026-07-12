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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('ID');
            $table->string('email', 255);
            $table->string('package', 20);
            $table->decimal('daily', 15, 2)->default(0);
            $table->decimal('totals', 15, 2)->default(0);
            $table->decimal('price', 15, 2)->default(0);
            $table->string('status', 13)->default('Active');
            $table->string('cycle', 10)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->decimal('earnings', 15, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
