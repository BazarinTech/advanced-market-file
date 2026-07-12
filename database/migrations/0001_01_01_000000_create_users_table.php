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
        Schema::create('users', function (Blueprint $table) {
            $table->id('ID');
            $table->string('email', 255)->unique();
            $table->string('phone', 20);
            $table->string('passwrd', 255);
            $table->string('password', 255)->nullable();
            $table->string('status', 10)->default('Inactive');
            $table->string('refer', 255)->nullable();
            $table->string('country', 10)->nullable();
            $table->string('role', 10)->default('user');
            $table->rememberToken();
            $table->timestamp('date')->useCurrent();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
