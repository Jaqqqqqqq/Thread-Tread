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
        Schema::create('product_images', function (Blueprint $table) {
            $table->increments('image_id');          // Primary key
            $table->unsignedInteger('product_id');    // FK to products table
            $table->string('image_path', 255);        // Path to the stored image
            $table->integer('sort_order')->default(0); // Controls display order (0 = first)
            $table->timestamps();

            // Foreign key: if a product is deleted, remove its images too
            $table->foreign('product_id', 'fk_product_images_products')
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
        Schema::dropIfExists('product_images');
    }
};