<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->string('severity', 20)->default('info')->after('action');
            $table->boolean('notable')->default(false)->after('severity');
            $table->index('severity');
            $table->index('notable');
        });
    }

    public function down(): void
    {
        Schema::table('activity_logs', function (Blueprint $table) {
            $table->dropIndex(['severity']);
            $table->dropIndex(['notable']);
            $table->dropColumn(['severity', 'notable']);
        });
    }
};
