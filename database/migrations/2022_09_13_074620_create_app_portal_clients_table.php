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
        Schema::create('app_portal_clients', function (Blueprint $table) {
            $table->id();

            $table->integer('request_type')->nullable();
            
            $table->string('reference_number')->nullable();

            $table->string('email')->nullable();
            $table->string('contact')->nullable();

            $table->string('member_id')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('dob')->nullable();

            $table->integer('is_dependent')->nullable();

            $table->string('dependent_member_id')->nullable();
            $table->string('dependent_first_name')->nullable();
            $table->string('dependent_last_name')->nullable();
            $table->string('dependent_dob')->nullable();

            $table->string('remarks', 500)->nullable();

            $table->integer('status')->nullable();

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
        Schema::dropIfExists('app_portal_clients');
    }
};
