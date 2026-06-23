<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('unit_id')->nullable()->after('branch_id')->constrained('units')->nullOnDelete();
            $table->decimal('stock', 12, 3)->default(0)->change();
            $table->decimal('min_stock', 12, 3)->default(0)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
            $table->integer('stock')->default(0)->change();
            $table->integer('min_stock')->default(0)->change();
        });
    }
};
