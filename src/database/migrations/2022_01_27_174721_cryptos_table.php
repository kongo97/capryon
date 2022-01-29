<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CryptosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cryptos', function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->string("symbol")->unique();
            $table->string("start")->nullable();
            $table->string("price")->nullable();
            $table->float("delta_percent")->nullable();
            $table->string("min")->nullable();
            $table->string("max")->nullable();
            $table->json("history_24h")->nullable();
            $table->json("history_1h")->nullable();
            $table->json("history_15m")->nullable();
            $table->boolean("isDailyUp")->default(false);
            $table->boolean("isDailyUpdated")->default(false);
            $table->boolean("isQuick")->default(false);
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
        //
    }
}
