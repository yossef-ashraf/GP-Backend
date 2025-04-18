<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductattributesTable extends Migration
{
    public function up()
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->integer('attribute_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_attributes');
    }
}