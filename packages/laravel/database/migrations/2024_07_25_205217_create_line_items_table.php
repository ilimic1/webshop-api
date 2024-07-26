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
        Schema::create('line_items', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('order_id')->constrained();
            $table->string('sku', 255);
            $table->string('name', 255);
            $table->unsignedInteger('price');
            $table->unsignedInteger('quantity');
        });

        Schema::table('line_items', function (Blueprint $table) {
            $table->foreign('sku')->references('sku')->on('products')->constrained();
            $table->unique(['id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('line_items');
    }
};
