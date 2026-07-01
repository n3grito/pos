<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('general_settings', 'timezone')) {
            Schema::table('general_settings', function (Blueprint $table) {
                $table->string('timezone', 64)->nullable()->after('value');
            });
        }

        DB::table('general_settings')->updateOrInsert(
            ['key' => 'timezone'],
            ['value' => 'America/Havana']
        );
    }

    public function down(): void
    {
        Schema::table('general_settings', function (Blueprint $table) {
            $table->dropColumn('timezone');
        });
    }
};
