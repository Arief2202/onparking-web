<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mall_id');
            $table->foreignId('user_id');
            $table->integer('progress')->default(0);
            $table->timestamp('expired_time')->nullable();
            $table->timestamp('order_time')->nullable();
            $table->timestamp('checkIn_time')->nullable();
            $table->timestamp('checkOut_time')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
};
