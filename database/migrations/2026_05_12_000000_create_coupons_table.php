<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->decimal('amount', 10, 2);
            $table->datetime('expires_at');
            $table->unsignedInteger('max_uses')->default(0);
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamps();
        });

        Schema::create('coupon_uses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->string('user_email');
            $table->datetime('used_at');
            $table->index(['coupon_id', 'user_email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupon_uses');
        Schema::dropIfExists('coupons');
    }
};
