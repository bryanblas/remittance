<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_number')->nullable();
            $table->dateTime('transaction_date')->nullable();
            $table->integer('user_id')->nullable();
            $table->integer('merchant_id');
            $table->integer('bank_account_id');
            $table->string('currency');
            $table->string('amount');
            $table->string('fee')->nullable();
            $table->string('route')->nullable();
            $table->string('deposit_slip');
            $table->string('message')->nullable();
            $table->string('deposit_type')->nullable();
            $table->string('filename')->nullable();
            $table->string('status');
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
        Schema::dropIfExists('deposits');
    }
}
