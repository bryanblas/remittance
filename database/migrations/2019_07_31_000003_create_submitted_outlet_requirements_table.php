<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubmittedOutletRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('submitted_outlet_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('outlet_requirements_id');
            $table->unsignedInteger('outlet_id');
            $table->string('filename');
            /*
                0-rejected
                1-submitted
                2-approved
            */
            $table->boolean('state')->default(1);
            $table->boolean('status')->default(1);
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
        Schema::dropIfExists('submitted_outlet_requirements');
    }
}
