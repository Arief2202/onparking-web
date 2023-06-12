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
        Schema::create('mall_lists', function (Blueprint $table) {
            $table->id();
            $table->string('namaMall');
            $table->string('alamatMall');
            $table->string('openTimeMall');
            $table->string('fotoMall');
            $table->integer('user_id')->nullable();
            // $table->integer('kuotaMall')->default('5');
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
        Schema::dropIfExists('mall_lists');
    }
};
