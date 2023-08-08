<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeamchkoutorderdataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beamchkoutorderdata', function (Blueprint $table) {
            $table->id();
            $table->string('custid', 10);
            $table->string('shop', 255)->charset('utf8mb4')->collation('utf8mb4_unicode_ci');
            $table->string('carttoken', 50);
            $table->string('variantid', 20);
            $table->string('itemqty', 10);
            $table->string('beampurchaseid', 100);
            $table->string('stat', 1);
            $table->string('shoporderid', 100);
            $table->timestamps();
            // $table->primary('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beamchkoutorderdata');
    }
}
