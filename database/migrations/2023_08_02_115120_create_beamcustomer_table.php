<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeamcustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beamcustomer', function (Blueprint $table) {
            $table->id();
            $table->string('shop', 255);
            $table->string('customer_email', 100);
            $table->string('mobile', 15);
            $table->string('order_status', 15);
            $table->string('customer_note', 255);
            $table->string('cu_first_name', 100);
            $table->string('cu_last_name', 100);
            $table->string('bl_first_name', 100);
            $table->string('bl_last_name', 100);
            $table->string('bl_address', 255);
            $table->string('bl_phone', 15);
            $table->string('bl_city', 150);
            $table->string('bl_province', 150);
            $table->string('bl_country', 150);
            $table->string('bl_zip', 10);
            $table->string('dl_first_name', 100);
            $table->string('dl_last_name', 100);
            $table->string('dl_address', 255);
            $table->string('dl_phone', 15);
            $table->string('dl_city', 150);
            $table->string('dl_province', 150);
            $table->string('dl_country', 150);
            $table->string('dl_zip', 10);
            $table->string('order_amt', 20);
            $table->string('beampurchaseid', 100);
            $table->string('shoporderid', 100);
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
        Schema::dropIfExists('beamcustomer');
    }
}
