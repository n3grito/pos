<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('general_settings')->insert([
            ['key' => 'default_tax_rate', 'value' => '0'],
            ['key' => 'default_currency_id', 'value' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('general_settings');
    }
};
