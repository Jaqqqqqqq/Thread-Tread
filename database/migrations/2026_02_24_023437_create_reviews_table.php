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
        Schema::create('reviews', function (Blueprint $table) {
            $table->increments('review_id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('product_id');
            $table->tinyInteger('rating');
            $table->string('comment', 1000)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'product_id'], 'uq_reviews_user_product');

            $table->foreign('product_id', 'fk_reviews_products')
                  ->references('product_id')->on('products')
                  ->onDelete('cascade')
                  ->onUpdate('no action');

            $table->foreign('user_id', 'fk_reviews_users')
                  ->references('user_id')->on('users')
                  ->onDelete('restrict')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
