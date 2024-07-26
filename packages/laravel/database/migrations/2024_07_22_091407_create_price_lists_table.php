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
        Schema::create('price_lists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name', 255);
            // $table->unsignedBigInteger('price');
            // $table->string('sku', 255);
            // $table->string('sku', 255)->references('sku')->on('products')->constrained();
            // $table->unique(['id', 'sku']);
        });

        // Schema::table('price_lists', function (Blueprint $table) {
        //     $table->foreign('sku')->references('sku')->on('products')->constrained();
        //     $table->unique(['id', 'sku']);
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_lists');
    }
};
