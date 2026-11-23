<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_categories', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });
        Schema::create('membership_levels', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedBigInteger('fee');
            $table->unsignedInteger('validity_days');
            $table->text('benefits')->nullable();
            $table->timestamps();
        });
        $categories = [];
        foreach ([
            'student' => 'Student',
            'regular' => 'Regular',
            'senior_citizen' => 'Senior Citizen',
            'corporate' => 'Corporate',
        ] as $key => $name) {
            $categories[$key] = DB::table('membership_categories')->insertGetId(['name' => $name, 'created_at' => now(), 'updated_at' => now()]);
        }
        $levels = [];
        foreach (['basic' => 299, 'silver' => 599, 'gold' => 999, 'platinum' => 1499] as $key => $fee) {
            $benefits = [];
            foreach (config("memberships.entitlements.$key", []) as $name => $value) {
                $benefits[] = ucfirst(str_replace('_', ' ', $name)).': '.(is_bool($value) ? ($value ? 'Yes' : 'No') : $value);
            }

            $levels[$key] = DB::table('membership_levels')->insertGetId([
                'name' => ucfirst($key),
                'fee' => $fee * 100,
                'validity_days' => 30,
                'benefits' => implode("\n", $benefits),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        Schema::table('members', function (Blueprint $table): void {
            $table->renameColumn('category', 'legacy_category');
            $table->renameColumn('level', 'legacy_level');
        });
        Schema::withoutForeignKeyConstraints(function (): void {
            Schema::table('members', function (Blueprint $table): void {
                $table->string('legacy_category')->nullable()->change();
                $table->string('legacy_level')->nullable()->change();
                $table->foreignId('membership_category_id')->nullable()->constrained()->restrictOnDelete();
                $table->foreignId('membership_level_id')->nullable()->constrained()->restrictOnDelete();
                $table->timestamp('membership_starts_at')->nullable();
                $table->timestamp('membership_expires_at')->nullable()->index();
                $table->boolean('legacy_validity')->default(false);
            });
        });
        foreach (DB::table('members')->get() as $member) {
            $subscription = DB::table('subscriptions')->where('member_id', $member->id)->orderByDesc('expires_at')->first();
            DB::table('members')->where('id', $member->id)->update([
                'membership_category_id' => $categories[$member->legacy_category] ?? $categories['regular'],
                'membership_level_id' => $levels[$member->legacy_level] ?? $levels['basic'],
                'membership_starts_at' => $subscription?->starts_at,
                'membership_expires_at' => $subscription?->expires_at,
                'legacy_validity' => $subscription !== null,
            ]);
        }
        Schema::create('membership_transactions', function (Blueprint $table): void {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('member_id')->constrained()->restrictOnDelete();
            $table->foreignId('membership_category_id')->constrained()->restrictOnDelete();
            $table->foreignId('membership_level_id')->constrained()->restrictOnDelete();
            $table->string('category_name');
            $table->string('level_name');
            $table->string('type');
            $table->string('status')->default('Pending')->index();
            $table->unsignedBigInteger('fee');
            $table->unsignedInteger('validity_days');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        Schema::create('payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('membership_transaction_id')->unique()->constrained()->restrictOnDelete();
            $table->unsignedBigInteger('amount');
            $table->timestamp('paid_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('membership_transactions');
        Schema::withoutForeignKeyConstraints(function (): void {
            Schema::table('members', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('membership_category_id');
                $table->dropConstrainedForeignId('membership_level_id');
                $table->dropIndex(['membership_expires_at']);
                $table->renameColumn('legacy_category', 'category');
                $table->renameColumn('legacy_level', 'level');
                $table->dropColumn(['membership_starts_at', 'membership_expires_at', 'legacy_validity']);
            });
        });
        Schema::dropIfExists('membership_levels');
        Schema::dropIfExists('membership_categories');
    }
};
