<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductvariationsTable extends Migration
{
    public function up()
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->id();
            $table->string('slug');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->float('price');
            $table->float('sale_price');
            $table->string('stock_status');
            $table->integer('stock_qty');
            $table->string('sku');
            $table->timestamps();        
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
}