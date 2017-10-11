<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syss_user_types', function (blueprint $table) {
        	$table->increments('id_user_type');
        	$table->char('name', 100);
        	$table->boolean('is_deleted');
        	$table->timestamps();
        });

        DB::table('syss_user_types')->insert([
        	['id_user_type' => '1','name' => 'ESTÁNDAR', 'is_deleted' => '0'],
        	['id_user_type' => '2','name' => 'ADMINISTRADOR', 'is_deleted' => '0'],
        	['id_user_type' => '3','name' => 'ADMINISTRADOR SISTEMA', 'is_deleted' => '0'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('syss_user_types');
    }
}
