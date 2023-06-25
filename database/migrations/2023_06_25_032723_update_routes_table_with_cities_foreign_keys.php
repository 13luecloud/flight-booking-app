<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateRoutesTableWithCitiesForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->renameColumn('origin', 'origin_id');
            $table->renameColumn('destination', 'destination_id');
        });

        Schema::table('routes', function (Blueprint $table) {
            $table->foreignId('origin_id')->reference('id')->on('cities')->change();
            $table->foreignId('destination_id')->reference('id')->on('cities')->change();
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
