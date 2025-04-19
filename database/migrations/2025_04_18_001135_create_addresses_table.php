<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAddressesTable extends Migration
{
    public function up()
    {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Assuming you have a users table
            $table->integer('area_id');
            $table->string('state');
            $table->string('zip_code');
            $table->string('street');
            $table->string('building_number');
            $table->string('apartment_number');
            $table->timestamps();
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('addresses');
    }
}