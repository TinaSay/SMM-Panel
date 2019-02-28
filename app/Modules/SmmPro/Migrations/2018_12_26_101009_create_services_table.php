<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateServicesTable
 */
class CreateServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smmpro_services', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('quantity');
            $table->string('service_api');
            $table->string('service_order_api');
            $table->string('type')->nullable();
            $table->string('price');
            $table->string('reseller_price')->nullable();
            $table->boolean('active')->default(1);
            $table->timestamps();

            $table->foreign('category_id')
                ->references('id')->on('smmpro_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smmpro_services');
    }
}
