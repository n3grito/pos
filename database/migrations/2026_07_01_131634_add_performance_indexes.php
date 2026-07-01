<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->index('date');
            $table->index('status');
            $table->index(['date', 'status']);
            $table->index(['branch_id', 'date']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('available_for_sale');
            $table->index('stock');
            $table->index('min_stock');
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->index(['sale_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropIndex(['date']);
            $table->dropIndex(['status']);
            $table->dropIndex(['date', 'status']);
            $table->dropIndex(['branch_id', 'date']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['available_for_sale']);
            $table->dropIndex(['stock']);
            $table->dropIndex(['min_stock']);
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->dropIndex(['sale_id', 'product_id']);
        });
    }
};
