<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->string('type');
            $table->string('sku');
            $table->float('price');
            $table->string('image')->nullable();
            $table->float('sale_price');
            $table->integer('sold_individually')->nullable();
            $table->string('stock_status');
            $table->integer('stock_qty');
            $table->integer('total_sales')->default(0);
            $table->timestamps();      
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
}