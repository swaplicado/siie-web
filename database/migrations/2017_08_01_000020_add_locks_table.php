<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syss_locks', function (blueprint $table) {
          $table->bigIncrements('id_lock');
          $table->char('session_id', 50);
          $table->integer('company_id')->unsigned();
          $table->char('table_name', 100);
          $table->integer('record_id');
          $table->integer('user_id')->unsigned();
          $table->timestamps();

          $table->unique(['company_id','table_name','record_id']);
          $table->foreign('company_id')->references('id_company')->on('sysu_companies')->onDelete('cascade');
          $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('syss_locks');
    }
}
