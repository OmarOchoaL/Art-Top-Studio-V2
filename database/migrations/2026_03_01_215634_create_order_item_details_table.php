<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('order_item_details', function (Blueprint $table) {
        $table->id();

        $table->foreignId('order_item_id')
            ->constrained()
            ->onDelete('cascade');

        $table->string('field_name');   // color, talla, texto
        $table->string('field_value');  // rojo, M, Juan

        $table->timestamps();
    });
}

public function down()
{
    Schema::dropIfExists('order_item_details');
}

};
