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
        Schema::create('product_user', function (Blueprint $table) {
            $table->id();
            // $table->timestamps();
            $table->foreignId('user_id')->constrained();
            $table->string('sku', 255);
            $table->unsignedInteger('price');
        });

        Schema::table('product_user', function (Blueprint $table) {
            $table->foreign('sku')->references('sku')->on('products')->constrained();
            $table->unique(['user_id', 'sku']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_user');
    }
};
