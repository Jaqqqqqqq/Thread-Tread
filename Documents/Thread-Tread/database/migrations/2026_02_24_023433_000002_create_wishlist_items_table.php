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
        Schema::create('wishlist_items', function (Blueprint $table) {
            $table->increments('wishlist_item_id');
            $table->unsignedInteger('wishlist_id');
            $table->unsignedInteger('product_id');
            $table->timestamps();

            $table->unique(['wishlist_id', 'product_id'], 'uq_wishlist_product');

            $table->foreign('wishlist_id', 'fk_wishlist_items_wishlists')
                  ->references('wishlist_id')->on('wishlists')
                  ->onDelete('cascade')
                  ->onUpdate('no action');

            $table->foreign('product_id', 'fk_wishlist_items_products')
                  ->references('product_id')->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wishlist_items');
    }
};
