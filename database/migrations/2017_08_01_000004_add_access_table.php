<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAccessTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syss_access', function (blueprint $table) {
        	$table->increments('id_access');
        	$table->char('name', 100);
        	$table->boolean('is_deleted');
        	$table->timestamps();
        });

        DB::table('syss_access')->insert([
        	['id_access' => '1','name' => 'EMPRESA', 'is_deleted' => '0'],
        	['id_access' => '2','name' => 'SUCURSAL', 'is_deleted' => '0'],
        	['id_access' => '3','name' => 'ALMACÉN', 'is_deleted' => '0'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('syss_access');
    }
}
