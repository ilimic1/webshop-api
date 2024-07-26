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
        Schema::create('category_product', function (Blueprint $table) {
            $table->id();
            // $table->timestamps();
            $table->string('sku', 255);
            $table->foreignId('category_id')->constrained();
        });

        Schema::table('category_product', function (Blueprint $table) {
            $table->foreign('sku')->references('sku')->on('products')->constrained();
            $table->unique(['sku', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_category');
    }
};
