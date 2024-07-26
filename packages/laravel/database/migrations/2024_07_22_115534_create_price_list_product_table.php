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
        Schema::create('price_list_product', function (Blueprint $table) {
            $table->id();
            // $table->timestamps();
            $table->foreignId('price_list_id')->constrained();
            $table->string('sku', 255);
            $table->unsignedInteger('price');
        });

        Schema::table('price_list_product', function (Blueprint $table) {
            $table->index('price_list_id');
            $table->index('sku');
            $table->unique(['price_list_id', 'sku']);
            $table->foreign('sku')->references('sku')->on('products')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_list_product');
    }
};
