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
        Schema::create('order_items', function (Blueprint $table) {
            $table->increments('order_item_id');
            $table->unsignedInteger('order_id');
            $table->unsignedInteger('variant_id');
            $table->integer('oi_quantity');
            $table->decimal('oi_price', 10, 2);
            $table->timestamps();

            $table->foreign('order_id', 'fk_order_items_orders')
                  ->references('order_id')->on('orders')
                  ->onDelete('cascade')
                  ->onUpdate('no action');

            $table->foreign('variant_id', 'fk_order_items_variants')
                  ->references('variant_id')->on('product_variants')
                  ->onDelete('restrict')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
