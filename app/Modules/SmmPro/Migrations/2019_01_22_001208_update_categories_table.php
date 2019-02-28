<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('smmpro_categories', function (Blueprint $table) {
            $table->string('icon')->nullable()->after('alias');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('smmpro_categories', function (Blueprint $table) {
            $table->dropColumn('icon');
        });
    }
}
