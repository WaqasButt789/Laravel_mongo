<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFriends extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('friends', function (Blueprint $table) {
            $table->id('fid');
            $table->bigInteger('userid_1')->unsigned();
            $table->bigInteger('userid_2')->unsigned();
            $table->timestamps();
            $table->foreign('userid_1')->references('uid')->on('users');
            $table->foreign('userid_2')->references('uid')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('friends');
    }
}
