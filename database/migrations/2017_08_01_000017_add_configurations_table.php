<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddConfigurationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sys_configuration', function (blueprint $table) {
        	$table->increments('id_configuration');
        	$table->char('version', 25);
          $table->integer('partner_id')->unsigned();
        	$table->timestamps();
        });

        DB::table('sys_configuration')->insert([
        	['id_configuration' => '1','version' => '1.0','partner_id' => '1'],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sys_configuration');
    }
}
