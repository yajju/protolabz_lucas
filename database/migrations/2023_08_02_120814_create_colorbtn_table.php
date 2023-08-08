<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateColorbtnTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('colorbtn', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('objidname', 255)->collation('utf8mb4_unicode_ci');
            $table->string('colname', 255)->collation('utf8mb4_unicode_ci');
            $table->string('remember_token', 100)->nullable()->collation('utf8mb4_unicode_ci');
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
        Schema::dropIfExists('colorbtn');
    }
}
