<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQaMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    // {
    //     Schema::create('qa_messages', function (Blueprint $table) {
    //         $table->increments('id');
    //         $table->integer('topic_id')->unsigned();
    //         $table->foreign('topic_id')->references('id')->on('qa_topics')->onDelete('cascade');
    //         $table->integer('sender_id')->unsigned();
    //         $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
    //         $table->timestamp('read_at')->nullable();
    //         $table->text('content');
    //         $table->timestamps();
    //     });
    // }
    {
        Schema::create('qa_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('topic_id',10);
            $table->string('sender_id',10);
            $table->timestamp('read_at')->nullable();
            $table->text('content');
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
