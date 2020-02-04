<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOutletRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('outlet_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('description')->nullable();
            $table->boolean('individual')->default(1);
            $table->boolean('corporate')->default(1);
            $table->boolean('required')->default(0);
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
        Schema::dropIfExists('outlet_requirements');
    }
}
