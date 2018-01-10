<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpenHousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('open_houses', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('listing_id');
            $table->string('mls_id');
            $table->string('event_unique_id')->nullable();
            $table->dateTime('last_modified')->nullable();
            $table->dateTime('event_start')->nullable();
            $table->dateTime('event_end')->nullable();
            $table->string('unique_listing_id')->nullable();
            $table->integer('list_price')->nullable();
            $table->string('listing_area')->nullable();
            $table->string('street_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('listing_agent_id')->nullable();
            $table->string('listing_agent_first_name')->nullable();
            $table->string('listing_agent_last_name')->nullable();
            $table->string('agent_primary_phone')->nullable();
            $table->string('listing_office_id')->nullable();
            $table->string('listing_office_name')->nullable();
            $table->string('listing_office_phone')->nullable();
            $table->string('comments')->nullable();
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
        Schema::table('open_houses', function (Blueprint $table) {
            //
        });
    }
}
