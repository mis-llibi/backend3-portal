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
        Schema::create('hr_philhealth', function (Blueprint $table) {
            $table->id();

            $table->integer('link_id')->nullable();

            $table->string('philhealth_conditions')->nullable();
            $table->string('position')->nullable();
            $table->string('plan_type')->nullable();
            $table->string('branch_name')->nullable();
            $table->string('philhealth_no')->nullable();
            $table->string('senior_citizen_id_no')->nullable();
            
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
        Schema::dropIfExists('hr_philhealth');
    }
};
