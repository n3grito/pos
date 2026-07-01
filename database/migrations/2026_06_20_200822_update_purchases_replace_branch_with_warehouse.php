<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE purchases DROP FOREIGN KEY purchases_warehouse_id_foreign');
            DB::statement('ALTER TABLE purchases MODIFY branch_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE purchases MODIFY warehouse_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE purchases ADD CONSTRAINT purchases_warehouse_id_foreign FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT');
        } elseif ($driver === 'sqlite') {
            Schema::table('purchases', function ($table) {
                $table->unsignedBigInteger('branch_id')->nullable()->change();
                $table->unsignedBigInteger('warehouse_id')->nullable(false)->change();
            });
        }
    }

    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE purchases DROP FOREIGN KEY purchases_warehouse_id_foreign');
            DB::statement('ALTER TABLE purchases MODIFY branch_id BIGINT UNSIGNED NOT NULL');
            DB::statement('ALTER TABLE purchases MODIFY warehouse_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE purchases ADD CONSTRAINT purchases_warehouse_id_foreign FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL');
        } elseif ($driver === 'sqlite') {
            Schema::table('purchases', function ($table) {
                $table->unsignedBigInteger('branch_id')->nullable(false)->change();
                $table->unsignedBigInteger('warehouse_id')->nullable()->change();
            });
        }
    }
};
