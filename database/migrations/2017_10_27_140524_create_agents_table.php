<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('agent_id')->index();
            $table->string('office_id');
            $table->string('first_name')->index();
            $table->string('last_name')->index();
            $table->string('office_phone');
            $table->string('cell_phone');
            $table->string('home_phone');
            $table->string('fax');
            $table->string('email');
            $table->string('url');
            $table->string('street_1');
            $table->string('street_2');
            $table->string('city');
            $table->string('state');
            $table->string('zip');
            $table->string('short_id')->index();
            $table->string('middle_name');
            $table->string('full_name')->index();
            $table->string('primary_phone');
            $table->boolean('active_status');
            $table->boolean('active');
            $table->boolean('mls_status');
            $table->string('license_number');
            $table->dateTime('date_modified');
            $table->string('office_short_id')->index();
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
        Schema::dropIfExists('agents');
    }
}
