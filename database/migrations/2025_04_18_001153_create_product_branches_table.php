<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductbranchesTable extends Migration
{
    public function up()
    {
        Schema::create('product_branches', function (Blueprint $table) {
            $table->id();
            $table->integer('branch_id');
            $table->integer('product_id');
            $table->integer('variation_id');
            $table->integer('status');
  $table->timestamps();
          
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_branches');
    }
}