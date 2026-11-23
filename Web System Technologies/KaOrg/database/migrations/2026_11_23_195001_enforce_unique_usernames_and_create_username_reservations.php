<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('CREATE UNIQUE INDEX users_username_unique ON users (LOWER(username))');
        Schema::create('username_reservations', function (Blueprint $table) {
            $table->string('username', 30)->primary();
            $table->string('email')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('username_reservations');
        DB::statement('DROP INDEX users_username_unique');
    }
};
