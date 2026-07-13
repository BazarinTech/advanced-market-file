<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Snapshotted from the Package at purchase time, same as daily/totals/amount,
            // so changing a package's settings later doesn't affect orders already in progress.
            $table->unsignedTinyInteger('tasks_per_day')->default(1)->after('last_claimed_at');
            $table->string('task_category', 100)->nullable()->after('tasks_per_day');
            $table->unsignedTinyInteger('tasks_claimed_today')->default(0)->after('task_category');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['tasks_per_day', 'task_category', 'tasks_claimed_today']);
        });
    }
};
