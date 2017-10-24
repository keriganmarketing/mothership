<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateListingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('mls_account')->nullable()->index();
            $table->integer('price')->nullable();
            $table->string('area')->nullable()->index();
            $table->string('sub_area')->nullable()->index();
            $table->string('subdivision')->nullable()->index();
            $table->string('city')->nullable()->index();
            $table->string('street_number')->nullable();
            $table->string('street_name')->nullable();
            $table->string('unit_number')->nullable();
            $table->string('state')->nullable();
            $table->string('zip')->nullable();
            $table->float('latitude', 10, 6)->default(0.0);
            $table->float('longitude', 10, 6)->default(0.0);
            $table->integer('bedrooms')->defautl(0);
            $table->integer('bathrooms')->default(0);
            $table->integer('sq_ft')->default(0);
            $table->float('acreage', 8, 2)->default(0.0);
            $table->string('class')->nullable();
            $table->string('property_type')->nullable();
            $table->string('preferred_image')->nullable();
            $table->string('status')->nullable();
            $table->string('waterfront')->nullable();
            $table->boolean('foreclosure')->nullable();
            $table->boolean('garage')->nullable();
            $table->boolean('pool')->nullable();
            $table->boolean('distressed')->nullable();
            $table->dateTime('date_modified')->nullable();
            $table->string('agent_id')->nullable();
            $table->string('colist_agent_id')->nullable();
            $table->string('office_id')->nullable();
            $table->string('colist_office_id')->nullable();
            $table->date('list_date')->nullable();
            $table->date('sold_date')->nullable();
            $table->decimal('sold_price', 15, 10)->nullable();
            $table->string('association');
            $table->string('listing_member_shortid')->nullable();
            $table->string('colisting_member_shortid')->nullable();
            $table->text('interior')->nullable();
            $table->text('appliances')->nullable();
            $table->text('amenities')->nullable();
            $table->text('exterior')->nullable();
            $table->text('lot_description')->nullable();
            $table->text('energy_features')->nullable();
            $table->text('construction')->nullable();
            $table->text('utilities')->nullable();
            $table->text('zoning')->nullable();
            $table->text('waterview_description')->nullable();
            $table->string('elementary_school')->nullable();
            $table->string('middle_school')->nullable();
            $table->string('high_school')->nullable();
            $table->string('sqft_source')->nullable();
            $table->integer('year_built')->default(0);
            $table->string('lot_dimensions')->nullable();
            $table->decimal('stories', 6, 0)->default(0);
            $table->integer('full_baths')->nullable();
            $table->integer('half_baths')->nullable();
            $table->integer('last_taxes')->nullable();
            $table->decimal('last_tax_year', 4, 0)->default(0);
            $table->text('description')->nullable();
            $table->string('apn')->nullable();
            $table->text('directions')->nullable();
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
        Schema::dropIfExists('listings');
    }
}
