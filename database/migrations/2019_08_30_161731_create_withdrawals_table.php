<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWithdrawalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('withdrawals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_number');
            $table->integer('merchant_id');
            $table->integer('merchant_balance_id');
            $table->string('account_number');
            $table->string('beneficiary_name');
            $table->string('account_type');
            $table->string('currency');
            $table->string('amount');
            $table->string('rate');
            $table->string('fee')->nullable();
            $table->string('beneficiary_country');
            $table->string('beneficiary_address');
            $table->string('bank_name');
            $table->string('swift_code');
            $table->string('bank_country');
            $table->string('bank_address');
            $table->string('contact_number');
            $table->string('remarks')->nullable();
            /*
                Pending
                Rejected
                Completed
             */
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
        Schema::dropIfExists('withdrawals');
    }
}
