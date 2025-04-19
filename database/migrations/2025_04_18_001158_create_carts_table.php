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
            $table->float('cart_total');
            $table->timestamps();        
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('carts');
    }
}