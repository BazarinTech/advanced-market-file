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
        // Note: legacy table name is 'transaction' (no 's')
        Schema::create('transaction', function (Blueprint $table) {
            $table->id('ID');
            $table->string('type', 20);
            $table->string('email', 255);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status', 20)->default('Pending');
            $table->string('phone', 13)->nullable();
            $table->string('details', 255)->nullable();
            $table->decimal('RecAmount', 15, 2)->default(0);
            $table->timestamp('date')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};
