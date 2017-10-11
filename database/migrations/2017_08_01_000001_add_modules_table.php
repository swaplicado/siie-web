<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syss_modules', function (blueprint $table) {
        	$table->increments('id_module');
        	$table->char('name', 100);
        	$table->boolean('is active');
        	$table->boolean('is_deleted');
        	$table->timestamps();
        });

        DB::table('syss_modules')->insert([
        	['id_module' => '1','name' => 'CENTRAL','is active' => '0', 'is_deleted' => '0'],
        	['id_module' => '2','name' => 'PRODUCCIÓN','is active' => '0', 'is_deleted' => '0'],
        	['id_module' => '3','name' => 'CALIDAD','is active' => '0', 'is_deleted' => '0'],
        	['id_module' => '4','name' => 'ALMACENES','is active' => '0', 'is_deleted' => '0'],
        	['id_module' => '5','name' => 'EMBARQUES','is active' => '0', 'is_deleted' => '0'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('syss_modules');
    }
}
