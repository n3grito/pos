<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipt_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('footer_text')->nullable()->default('¡Gracias por su compra!');
            $table->boolean('show_seller')->default(true);
            $table->boolean('show_nit')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipt_settings');
    }
};
