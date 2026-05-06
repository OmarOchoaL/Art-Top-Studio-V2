<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_color_sizes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('color_id')->constrained('colores')->onDelete('cascade');
            $table->string('size');
            $table->integer('quantity')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'color_id', 'size']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_color_sizes');
    }
};