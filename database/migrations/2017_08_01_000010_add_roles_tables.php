<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRolesTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

      Schema::create('sysu_roles', function (blueprint $table) {
      	$table->increments('id_role');
      	$table->char('name', 100);
      	$table->boolean('is_deleted');
      	$table->integer('created_by_id')->unsigned();
      	$table->integer('updated_by_id')->unsigned();
      	$table->timestamps();

      	$table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
      	$table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
      });

      Schema::create('sysu_role_permissions', function (blueprint $table) {
        $table->increments('id_role');
        $table->integer('role_id')->unsigned();
        $table->integer('permission_id')->unsigned();
        $table->integer('privilege_id')->unsigned();

        $table->foreign('role_id')->references('id_role')->on('sysu_roles')->onDelete('cascade');
        $table->foreign('permission_id')->references('id_permission')->on('syss_permissions')->onDelete('cascade');
        $table->foreign('privilege_id')->references('id_privilege')->on('syss_privileges')->onDelete('cascade');
        });

        Schema::create('user_roles', function (blueprint $table) {
        	$table->increments('id_user_permission');
        	$table->integer('user_id')->unsigned();
        	$table->integer('role_id')->unsigned();
        	$table->integer('permission_type_id')->unsigned();
        	$table->integer('company_id_opt')->unsigned()->nullable();

        	$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        	$table->foreign('role_id')->references('id_role')->on('sysu_roles')->onDelete('cascade');
        	$table->foreign('permission_type_id')->references('id_permission_type')->on('syss_permission_types')->onDelete('cascade');
        	$table->foreign('company_id_opt')->references('id_company')->on('sysu_companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::drop('user_roles');
      Schema::drop('sysu_role_permissions');
      Schema::drop('sysu_roles');
    }
}
