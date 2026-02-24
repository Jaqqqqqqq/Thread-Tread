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
        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id');
            $table->unsignedInteger('category_id');
            $table->unsignedInteger('brand_id')->nullable();
            $table->string('product_name', 150);
            $table->text('prod_desc');
            $table->decimal('price', 10, 2);
            $table->string('prod_image', 255);
            $table->timestamps();

            $table->foreign('category_id', 'fk_products_categories')
                  ->references('category_id')->on('categories')
                  ->onDelete('restrict')
                  ->onUpdate('no action');

            $table->foreign('brand_id', 'fk_products_brands')
                  ->references('brand_id')->on('brands')
                  ->onDelete('set null')
                  ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
