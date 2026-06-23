<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('store_name');
            $table->string('logo_path')->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('receipt_settings', function (Blueprint $table) {
            $table->dropColumn(['company_name', 'logo_path']);
        });
    }
};
