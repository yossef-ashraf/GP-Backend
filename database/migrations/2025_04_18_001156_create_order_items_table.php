<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderitemsTable extends Migration
{
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Assuming you have an orders table
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Assuming you have a products table
            $table->foreignId('variation_id')->nullable()->constrained('product_variations')->onDelete('cascade'); // Assuming you have a product_variations table
            $table->float('total_amount');
            $table->integer('quantity');
            $table->float('price')->nullable();
            $table->text('variation_data')->nullable();
            $table->timestamps();        
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
}