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
        Schema::create('cart_items', function (Blueprint $table) {
            $table->increments('cart_item_id');
            $table->unsignedInteger('cart_id');
            $table->unsignedInteger('variant_id');
            $table->integer('quantity')->default(1);
            $table->timestamps();

            $table->unique(['cart_id', 'variant_id'], 'uq_cart_variant');

            $table->foreign('cart_id', 'fk_cart_items_carts')
                  ->references('cart_id')->on('carts')
                  ->onDelete('cascade')
                  ->onUpdate('no action');

            $table->foreign('variant_id', 'fk_cart_items_variants')
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
        Schema::dropIfExists('cart_items');
    }
};
