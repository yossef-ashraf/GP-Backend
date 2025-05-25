<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCartsTable extends Migration
{
    public function up()
    {
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Assuming you have a users table
            $table->foreignId('product_id')->constrained()->onDelete('cascade'); // Assuming you have a products table
            $table->foreignId('variation_id')->nullable()->constrained('product_variations')->onDelete('cascade'); // Assuming you have a variations table
            $table->float('total');
            $table->integer('quantity');
            $table->timestamps();        
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
}