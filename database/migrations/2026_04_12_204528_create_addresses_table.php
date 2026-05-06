<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('alias');
            $table->string('recipient_name');
            $table->string('phone')->nullable();
            $table->string('street');
            $table->string('neighborhood')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->text('references')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};