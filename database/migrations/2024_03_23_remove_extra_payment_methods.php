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
        // Deactivate extra payment methods, keeping only COD and Online Payment active on website
        DB::table('payment_methods')->whereIn('method_name', [
            'GCash',
            'Credit Card',
            'Debit Card',
            'PayMaya'
        ])->update(['is_active' => false]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reactivate payment methods if migration is rolled back
        DB::table('payment_methods')->whereIn('method_name', [
            'GCash',
            'Credit Card',
            'Debit Card'
        ])->update(['is_active' => true]);
    }
};
