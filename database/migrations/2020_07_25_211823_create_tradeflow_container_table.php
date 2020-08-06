<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTradeflowContainerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tradeflow_container', function (Blueprint $table) {
            $table->unsignedBigInteger('tradeflow_id');
            $table->unsignedBigInteger('container_id');

            $table
                ->foreign('tradeflow_id')
                ->references('id')
                ->on('tradeflows');

            $table
                ->foreign('container_id')
                ->references('id')
                ->on('containers');

            $table->unique(['tradeflow_id', 'container_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tradeflow_container');
    }
}
