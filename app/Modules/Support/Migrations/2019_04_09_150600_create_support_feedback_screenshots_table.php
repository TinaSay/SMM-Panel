<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSupportFeedbackScreenshotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('support_feedback_screenshots', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('feedback_id')->unsigned();
            $table->string('image');
            $table->timestamps();

            $table->foreign('feedback_id')->references('id')->on('support_feedback')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('support_feedback_screenshots');
    }
}
