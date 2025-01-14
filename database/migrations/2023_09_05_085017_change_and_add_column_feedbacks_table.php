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
    Schema::table('feedbacks', function (Blueprint $table) {
      $table->after('comments', function (Blueprint $table) {
        $table->integer('question1');
        $table->integer('question2');
        $table->integer('question3');
        $table->integer('question4');
      });
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    //
  }
};
