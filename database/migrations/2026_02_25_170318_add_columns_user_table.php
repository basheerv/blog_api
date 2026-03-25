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
            $table->string('phone')->nullable()->after('email');
            $table->string('address')->nullable()->after('phone');
            $table->string('profile_picture')->nullable()->after('address');
            $table->text('bio')->nullable()->after('profile_picture');
            $table->string('social_links')->nullable()->after('bio');
            $table->string('role')->default('user')->after('social_links');
            $table->boolean('is_active')->default(true)->after('role');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'address',
                'profile_picture',
                'bio',
                'social_links',
                'role',
                'is_active',
                'last_login_at'
            ]);
        });
    }
};
