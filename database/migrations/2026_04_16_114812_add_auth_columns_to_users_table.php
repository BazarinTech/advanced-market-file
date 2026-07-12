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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'password')) {
                $table->string('password', 255)->nullable()->after('passwrd');
            }
            if (!Schema::hasColumn('users', 'role')) {
                $table->string('role', 10)->default('user')->after('country');
            }
            if (!Schema::hasColumn('users', 'remember_token')) {
                $table->rememberToken()->after('role');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['password', 'role', 'remember_token']);
        });
    }
};
