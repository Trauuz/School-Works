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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->cascadeOnDelete();
            $table->string('category')->index();
            $table->string('level')->index();
            $table->string('scheduled_level')->nullable();
            $table->string('renewal_status')->default('active')->index();
            $table->boolean('auto_renews')->default(true);
            $table->dateTime('starts_at');
            $table->dateTime('expires_at')->index();
            $table->dateTime('quota_period_started_at');
            $table->json('quota_usage')->nullable();
            $table->string('corporate_organization')->nullable();
            $table->unsignedInteger('seat_count')->default(1);
            $table->unsignedInteger('unit_price_snapshot');
            $table->unsignedInteger('total_price_snapshot');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
