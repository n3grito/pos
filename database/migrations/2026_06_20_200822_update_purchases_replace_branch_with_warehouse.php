<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE purchases DROP FOREIGN KEY purchases_warehouse_id_foreign');
        DB::statement('ALTER TABLE purchases MODIFY branch_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE purchases MODIFY warehouse_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE purchases ADD CONSTRAINT purchases_warehouse_id_foreign FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE RESTRICT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE purchases DROP FOREIGN KEY purchases_warehouse_id_foreign');
        DB::statement('ALTER TABLE purchases MODIFY branch_id BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE purchases MODIFY warehouse_id BIGINT UNSIGNED NULL');
        DB::statement('ALTER TABLE purchases ADD CONSTRAINT purchases_warehouse_id_foreign FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE SET NULL');
    }
};
