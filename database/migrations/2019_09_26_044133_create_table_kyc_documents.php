<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKycDocuments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kyc_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('merchant_id');
            $table->string('document_type');
            $table->string('type');
            $table->string('filename');
            $table->string('status');
            $table->string('filesize')->nullable();
            $table->string('remarks')->nullable();
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
        Schema::dropIfExists('kyc_documents');
    }
}
