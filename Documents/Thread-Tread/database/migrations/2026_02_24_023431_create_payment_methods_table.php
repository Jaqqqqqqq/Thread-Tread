<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->tinyIncrements('method_id');
            $table->string('method_name', 50)->unique();
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
        });

        DB::table('payment_methods')->insert([
            ['method_name' => 'COD',            'is_active' => 1],
            ['method_name' => 'Online Payment', 'is_active' => 1],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
