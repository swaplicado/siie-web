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
        	['id_permission' => '1','code' => '001','name' => 'Módulo Producción', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '2','code' => '002','name' => 'Módulo Calidad', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '3','code' => '003','name' => 'Módulo Almacenes', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '4','code' => '004','name' => 'Módulo Embarques', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '5','code' => '005','name' => 'Módulo Central', 'is_deleted' => '0','module_id' => '1'],
        	['id_permission' => '6','code' => '007','name' => 'siie_empresas', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '7','code' => '008','name' => 'Sucursales', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '8','code' => '009','name' => 'Periodos', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '9','code' => '010','name' => 'Ejercicios', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '10','code' => '011','name' => 'Asociados de negocios', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '11','code' => '012','name' => 'Unidades', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '12','code' => '013','name' => 'Almacenes', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '13','code' => '014','name' => 'Ubicaciones', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '14','code' => '015','name' => 'Familias', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '15','code' => '016','name' => 'Grupos', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '16','code' => '017','name' => 'Géneros', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '17','code' => '018','name' => 'ítems', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '18','code' => '019','name' => 'IÍtem-unidad', 'is_deleted' => '0','module_id' => '4'],
        	['id_permission' => '19','code' => '020','name' => 'Códigos de barras', 'is_deleted' => '0','module_id' => '4'],
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
