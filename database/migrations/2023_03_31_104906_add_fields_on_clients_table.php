<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('company_description')->nullable();
            $table->string('company_type')->nullable();
            $table->string('company_registration_number')->nullable();
            $table->string('company_ocas_affiliation_number')->nullable();
            $table->date('id_card_expiry_date')->nullable();
            $table->string('residence_permit')->nullable();
            $table->date('residence_permit_expiry_date')->nullable();
            $table->string('mailbox_code')->nullable();
            $table->string('domiciliation_type')->nullable();
            $table->json('services')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            //
        });
    }
};
