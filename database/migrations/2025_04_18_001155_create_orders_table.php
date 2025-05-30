<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Assuming you have a users table
            $table->foreignId('coupon_id')->nullable()->constrained()->onDelete('cascade'); // Assuming you have a coupons table
            $table->string('address')->nullable();
            $table->float('total_amount');
            $table->enum('payment_method', ['cash', 'credit_card']);
            $table->enum('status' , ['pre-pay','pending', 'completed', 'cancelled','payed']); // Order status
            $table->string('tracking_number')->nullable(); // Optional tracking number for the order
            $table->text('notes')->nullable(); // Optional notes for the order
            $table->foreignId('area_id')->nullable()->constrained()->onDelete('cascade');
            $table->float('shipping_cost')->nullable();
            $table->timestamps();        
            $table->softDeletes(); // This will add a deleted_at column for soft deletes
        });
    }

    public function down()
    {
        Schema::dropIfExists('orders');
    }
}