<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('clients', 'client_type')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->string('client_type', 50)->default('persona_natural')->after('notes');
                $table->string('photo')->nullable()->after('client_type');
            });
        }

        if (!Schema::hasColumn('suppliers', 'tax_id')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->string('tax_id', 50)->nullable()->unique()->after('address');
                $table->string('client_type', 50)->default('empresa')->after('tax_id');
                $table->string('photo')->nullable()->after('client_type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('clients', 'client_type')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropColumn(['client_type', 'photo']);
            });
        }

        if (Schema::hasColumn('suppliers', 'tax_id')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->dropColumn(['tax_id', 'client_type', 'photo']);
            });
        }
    }
};
