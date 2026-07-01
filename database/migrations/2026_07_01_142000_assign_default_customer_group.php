<?php

use App\Models\Client;
use App\Models\CustomerGroup;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $default = CustomerGroup::where('is_default', true)->first();
        if ($default) {
            Client::whereNull('customer_group_id')->update(['customer_group_id' => $default->id]);
        }
    }

    public function down(): void
    {
    }
};
