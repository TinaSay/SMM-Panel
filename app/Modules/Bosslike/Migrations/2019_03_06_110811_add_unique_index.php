<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUniqueIndex extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('social_users', function (Blueprint $table) {
            $table->unique(['social_id', 'user_id'])->unique('unique_social_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('social_users', function (Blueprint $table) {
            $table->dropIndex(['social_id', 'user_id']);
        });
    }
}
