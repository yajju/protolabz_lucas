<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    // {
    //     Schema::create('qa_topics', function (Blueprint $table) {
    //         $table->increments('id');
    //         $table->string('subject');
    //         $table->integer('creator_id')->unsigned();
    //         $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
    //         $table->integer('receiver_id')->unsigned();
    //         $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
    //         $table->timestamps();
    //     });
    // }
    {
        Schema::create('qa_topics', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('subject');
            $table->string('creator_id',10);
            $table->string('receiver_id',10);
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
        Schema::dropIfExists('users');
    }
}
