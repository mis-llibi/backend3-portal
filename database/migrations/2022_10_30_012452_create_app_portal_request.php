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
        Schema::create('app_portal_requests', function (Blueprint $table) {
            $table->id();
            
            $table->string('client_id')->nullable();
            $table->string('member_id')->nullable();

            $table->integer('provider_id')->nullable();
            $table->string('provider')->nullable();
            $table->string('doctor_id')->nullable();
            $table->string('doctor_name')->nullable();

            $table->string('loa_type')->nullable();

            $table->string('complaint')->nullable();
            $table->string('assessment_q1', 500)->nullable();
            $table->string('assessment_q2', 500)->nullable();
            $table->string('assessment_q3', 500)->nullable();
            
            $table->string('diagnosis')->nullable();
            $table->string('lab_attachment', 300)->nullable();
    
            $table->string('loa_number')->nullable();
            $table->string('loa_attachment', 300)->nullable();

            $table->string('approval_code')->nullable();

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
        Schema::dropIfExists('app_portal_requests');
    }
};
