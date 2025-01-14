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
    Schema::create('feedbacks', function (Blueprint $table) {
      $table->id();
      $table->string('request_id');
      $table->string('company_code');
      $table->string('member_id');
      $table->integer('request_status');
      $table->string('comments');
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
    Schema::dropIfExists('feedbacks');
  }
};
