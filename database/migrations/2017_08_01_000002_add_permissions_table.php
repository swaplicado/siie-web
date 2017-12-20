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
        	['id_permission' => '1','code' => '001','name' => 'ADMINISTRADOR', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '2','code' => '002','name' => 'CONFIGURACIÓN CENTRAL', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '3','code' => '003','name' => 'CONFIGURACIÓN DE ÍTEMS', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '4','code' => '004','name' => 'CONTENEDORES', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '5','code' => '005','name' => 'MOVIMIENTOS DE INVENTARIO', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '6','code' => '006','name' => 'ADMINISTRADOR MOVIMIENTOS DE ALMACÉN', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '7','code' => '007','name' => 'ADMINISTRACIÓN DE DOCUMENTOS', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '8','code' => '101','name' => 'MODULO CENTRAL', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '9','code' => '102','name' => 'MÓDULO DE PRODUCCIÓN', 'is_deleted' => '0','module_id' => '2'],
        	['id_permission' => '10','code' => '103','name' => 'MÓDULO DE CALIDAD', 'is_deleted' => '0','module_id' => '3'],
        	['id_permission' => '11','code' => '104','name' => 'MÓDULO DE ALMACENES', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '12','code' => '105','name' => 'MÓDULO DE EMBARQUES', 'is_deleted' => '0','module_id' => '5'],
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
