<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('
            UPDATE products p
            SET p.stock = COALESCE((
                SELECT SUM(ws.quantity)
                FROM warehouse_stock ws
                WHERE ws.product_id = p.id
            ), 0)
        ');
    }

    public function down(): void
    {
        // No reversible — data migration
    }
};
