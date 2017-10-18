<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('syss_permission_types', function (blueprint $table) {
          $table->increments('id_permission_type');
          $table->char('name', 100);
          $table->boolean('is_deleted');
          $table->timestamps();
        });

        DB::table('syss_permission_types')->insert([
          ['id_permission_type' => '1','name' => 'USUARIO', 'is_deleted' => '0'],
          ['id_permission_type' => '2','name' => 'EMPRESA', 'is_deleted' => '0'],
          ['id_permission_type' => '3','name' => 'SUCURSAL', 'is_deleted' => '0'],
        ]);

        Schema::create('syss_permissions', function (blueprint $table) {
        	$table->increments('id_permission');
        	$table->char('code', 10)->unique();
        	$table->char('name', 100);
        	$table->boolean('is_deleted');
        	$table->integer('module_id')->unsigned();
        	$table->timestamps();

        	$table->foreign('module_id')->references('id_module')->on('syss_modules')->onDelete('cascade');
        });

        DB::table('syss_permissions')->insert([
        	['id_permission' => '1','code' => '001','name' => 'Administrador', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '2','code' => '002','name' => 'Configuración central', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '3','code' => '003','name' => 'Configuración de ítems', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '4','code' => '004','name' => 'Contenedores', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '5','code' => '005','name' => 'Movimientos de inventario', 'is_deleted' => '0','module_id' => '1'],
        ]);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('syss_permissions');
      Schema::drop('syss_permission_types');
    }
}
