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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('kind')->default('activation');
            $table->string('category');
            $table->string('level');
            $table->string('currency', 3)->default('PHP');
            $table->unsignedInteger('unit_price_snapshot');
            $table->unsignedInteger('seat_count')->default(1);
            $table->unsignedInteger('total_price_snapshot');
            $table->dateTime('period_starts_at');
            $table->dateTime('period_expires_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
