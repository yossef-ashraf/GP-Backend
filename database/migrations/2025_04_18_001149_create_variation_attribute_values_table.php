<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationattributevaluesTable extends Migration
{
    public function up()
    {
        Schema::create('variation_attribute_values', function (Blueprint $table) {
            $table->id();
            $table->integer('variation_id');
            $table->integer('attribute_value_id');
  $table->timestamps();
          
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('variation_attribute_values');
    }
}