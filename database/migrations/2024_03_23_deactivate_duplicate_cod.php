<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Find and deactivate the "COD" entry if it exists (keep "Cash on Delivery")
        DB::table('payment_methods')
            ->where('method_name', 'COD')
            ->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('payment_methods')
            ->where('method_name', 'COD')
            ->update(['is_active' => true]);
    }
};
