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
    Schema::table('holiday', function (Blueprint $table) {
      $table->after('name', function (Blueprint $table) {
        $table->date('holiday_date');
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
    Schema::table('holiday', function (Blueprint $table) {
      $table->dropColumn(['holiday_date']);
    });
  }
};
