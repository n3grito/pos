<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('type', 50)->change();
            $table->foreignId('warehouse_id')->nullable()->constrained()->nullOnDelete()->after('product_id');
            $table->foreignId('to_branch_id')->nullable()->constrained('branches')->nullOnDelete()->after('warehouse_id');
        });
    }

    public function down(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
            $table->dropForeign(['to_branch_id']);
            $table->dropColumn(['warehouse_id', 'to_branch_id']);
        });
    }
};
