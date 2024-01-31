<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateClientFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->renameColumn('minutes_left', 'purchased_minutes_table');
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->unsignedBigInteger('purchased_minutes_office')->default(0);
            $table->boolean('is_resident')->default(false);
            $table->boolean('is_235')->default(false);
            $table->unsignedBigInteger('included_minutes_table')->default(0);
            $table->unsignedBigInteger('included_minutes_office')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
}
