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
        Schema::table('username_reservations', function (Blueprint $table) {
            $table->string('owner_token_hash', 64)->nullable();
            $table->timestamp('pending_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('username_reservations', function (Blueprint $table) {
            $table->dropColumn(['owner_token_hash', 'pending_expires_at']);
        });
    }
};
