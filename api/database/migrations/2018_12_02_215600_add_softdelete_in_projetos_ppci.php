<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSoftdeleteInProjetosPpci extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('projetos_ppci', function (Blueprint $table) {
      $table->softDeletes();
      $table->dateTime('updated')->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('projetos_ppci', function (Blueprint $table) {
      $table->dropSoftDeletes();
      $table->timestamp('updated')->change();
    });
  }
}
