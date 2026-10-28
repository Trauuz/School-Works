<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('eligibility_status')->default('exempt')->after('level')->index();
            $table->timestamp('eligibility_verified_at')->nullable()->after('eligibility_status');
            $table->string('corporate_organization')->nullable()->after('eligibility_verified_at');
            $table->unsignedInteger('seat_count')->default(1)->after('corporate_organization');
        });

        DB::table('members')->whereIn('category', ['faculty', 'staff'])->update(['category' => 'regular']);
        DB::table('members')->where('category', 'student')->update(['eligibility_status' => 'pending']);
        DB::table('members')->where('level', '1')->update(['level' => 'basic']);
        DB::table('members')->where('level', '2')->update(['level' => 'silver']);
        DB::table('members')->where('level', '3')->update(['level' => 'gold']);
        DB::table('members')->where('level', '4')->update(['level' => 'platinum']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('members')->where('category', 'regular')->update(['category' => 'faculty']);
        DB::table('members')->whereIn('category', ['senior_citizen', 'corporate'])->update(['category' => 'staff']);
        DB::table('members')->where('level', 'basic')->update(['level' => '1']);
        DB::table('members')->where('level', 'silver')->update(['level' => '2']);
        DB::table('members')->where('level', 'gold')->update(['level' => '3']);
        DB::table('members')->where('level', 'platinum')->update(['level' => '4']);

        Schema::table('members', function (Blueprint $table) {
            $table->dropColumn(['eligibility_status', 'eligibility_verified_at', 'corporate_organization', 'seat_count']);
        });
    }
};
