<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBroadcastListenerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('broadcast_listener', function (Blueprint $table) {
            $table->bigIncrements('listener_id');
            $table->string('fk_user_id')->nullable();
            $table->string('channel')->index();
            $table->string('connection_id')->index();
            $table->string('api_id');
            $table->string('region');
            $table->string('stage');
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
        Schema::dropIfExists('broadcast_listener');
    }
}
