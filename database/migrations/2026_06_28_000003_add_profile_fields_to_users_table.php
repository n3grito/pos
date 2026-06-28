<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nit', 20)->nullable()->after('email');
            $table->text('address')->nullable()->after('nit');
            $table->string('phone_personal', 20)->nullable()->after('phone');
            $table->string('profile_photo_path', 255)->nullable()->after('phone_personal');
            $table->boolean('must_change_password')->default(false)->after('two_factor_expires_at');
            $table->timestamp('password_changed_at')->nullable()->after('must_change_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nit', 'address', 'phone_personal', 'profile_photo_path', 'must_change_password', 'password_changed_at']);
        });
    }
};
