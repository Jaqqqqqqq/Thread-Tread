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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->increments('variant_id');
            $table->unsignedInteger('product_id');
            $table->string('size', 20);
            $table->string('color', 50);
            $table->integer('stock')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'size', 'color'], 'uq_product_size_color');

            $table->foreign('product_id', 'fk_product_variants_products')
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
        Schema::dropIfExists('product_variants');
    }
};
