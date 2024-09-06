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
    Schema::create('game_sessions', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->json('game_state')->nullable();
 // Store game data
        $table->integer('wins')->default(0);
        $table->integer('losses')->default(0);
        $table->timestamps();

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
        Schema::dropIfExists('game_sessions');
    }
};
