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
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('order_id');
            $table->unsignedInteger('user_id');
            $table->unsignedTinyInteger('method_id');
            $table->enum('order_status', ['pending', 'processing', 'shipped', 'completed', 'cancelled'])
                  ->default('pending');
            $table->string('shipping_address', 255);
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->timestamps();

            $table->index('order_status', 'idx_orders_status');

            $table->foreign('user_id', 'fk_orders_users')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('no action');

            $table->foreign('method_id', 'fk_orders_payment_methods')
                  ->references('method_id')->on('payment_methods')
                  ->onDelete('restrict')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
