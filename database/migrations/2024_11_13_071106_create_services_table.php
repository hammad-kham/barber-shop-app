<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('location_type')->nullable();
            $table->string('shop_name');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('building')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('time_open_close');
            $table->string('book_before')->nullable();
            $table->string('phone_no')->nullable();
            $table->string('bio')->nullable();
            $table->timestamps();
            //forgein key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('services');
    }
}
