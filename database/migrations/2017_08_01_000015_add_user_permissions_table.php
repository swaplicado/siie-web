<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      Schema::create('user_permissions', function (blueprint $table) {
      	$table->increments('id_user_permission');
      	$table->integer('user_id')->unsigned();
      	$table->integer('permission_id')->unsigned();
      	$table->integer('permission_type_id')->unsigned();
      	$table->integer('company_id_opt')->unsigned()->nullable();
      	$table->integer('privilege_id')->unsigned();
      	$table->integer('module_id')->unsigned();

      	$table->unique(['user_id','permission_id','privilege_id']);
      	$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      	$table->foreign('permission_id')->references('id_permission')->on('syss_permissions')->onDelete('cascade');
      	$table->foreign('privilege_id')->references('id_privilege')->on('syss_privileges')->onDelete('cascade');
      	$table->foreign('module_id')->references('id_module')->on('syss_modules')->onDelete('cascade');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('user_permissions');
    }
}
