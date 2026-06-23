<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->nullable()->after('total');
            $table->decimal('change', 10, 2)->nullable()->after('amount_paid');
            $table->string('payment_reference')->nullable()->after('change');
            $table->string('client_name')->nullable()->after('payment_reference');
            $table->string('client_nit', 11)->nullable()->after('client_name');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['amount_paid', 'change', 'payment_reference', 'client_name', 'client_nit']);
        });
    }
};
