<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->change();
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->change();
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->change();
        });

        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->default(0)->change();
        });

        Schema::table('service_product', function (Blueprint $table) {
            $table->decimal('quantity', 12, 3)->default(1)->change();
        });
    }

    public function down(): void
    {
        Schema::table('purchase_details', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('sale_details', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->integer('quantity')->change();
        });

        Schema::table('warehouse_stock', function (Blueprint $table) {
            $table->integer('quantity')->default(0)->change();
        });

        Schema::table('service_product', function (Blueprint $table) {
            $table->integer('quantity')->default(1)->change();
        });
    }
};
