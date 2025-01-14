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
    Schema::create('feedbacks_corporate', function (Blueprint $table) {
      $table->id();
      $table->string('company_code');
      $table->string('member_id');
      $table->string('approval_code')->nullable();
      $table->string('comments')->nullable();
      $table->integer('question1')->nullable();
      $table->integer('question2')->nullable();
      $table->integer('question3')->nullable();
      $table->integer('question4')->nullable();
      $table->integer('question5')->nullable();
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
    Schema::dropIfExists('feedbacks_corporate');
  }
};
