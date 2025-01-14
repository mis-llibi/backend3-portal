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
        Schema::create('hr_members', function (Blueprint $table) {
            $table->id();

            $table->string('member_id')->nullable();
            
            $table->string('employee_no')->nullable();
            $table->string('first_name')->nullable();
            $table->string('middle_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('extension')->nullable();
            $table->string('gender')->nullable();

            $table->string('member_type')->nullable();
            $table->string('birth_date')->nullable();
            $table->string('relationship_id')->nullable();
            $table->string('civil_status')->nullable();
            $table->string('effective_date')->nullable();
            $table->string('date_hired')->nullable();
            $table->string('reg_date')->nullable();
            $table->integer('if_enrollee_is_a_philhealth_member')->nullable();

            $table->string('client_remarks')->nullable();

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
        Schema::dropIfExists('hr_members');
    }
};
