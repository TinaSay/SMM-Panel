<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smmpro_orders', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('service_id');
            $table->integer('quantity');
            $table->string('link');
            $table->string('charge');
            $table->string('order_api_id');
            $table->string('status');
            $table->string('type');
            $table->integer('start_count')->nullable();
            $table->integer('remains')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('service_id')->references('id')->on('smmpro_services');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smmpro_orders');
    }
}
