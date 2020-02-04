<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatMerchantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('account_id')->unique();
            $table->string('affiliation')->nullable();
            /*
                Personal
                Corporation
             */
            $table->string('type');
            $table->string('country')->nullable();
            $table->dateTime('birthdate')->nullable();
            $table->string('agent')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('postal')->nullable();
            $table->string('contact_number')->nullable();
            $table->boolean('active')->default(0);
            /*
                Pending
                Submitted
                Verified
             */
            $table->string('kyc_status')->default('Pending');
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchants');
    }
}
