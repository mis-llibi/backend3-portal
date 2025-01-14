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
        Schema::create('expired_logs', function (Blueprint $table) {
            $table->id();
            $table->string('member_id');
            $table->string('company_code');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('incepfrom');
            $table->date('incepto');
            $table->date('birth_date');
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
        Schema::dropIfExists('expired_logs');
    }
};
