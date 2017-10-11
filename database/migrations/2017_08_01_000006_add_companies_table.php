<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sysu_companies', function (blueprint $table) {
        	$table->increments('id_company');
        	$table->char('name', 100);
        	$table->char('dbms_host', 100);
        	$table->char('dbms_port', 10);
        	$table->char('database_name', 100);
        	$table->char('user_name', 50);
        	$table->char('user_password', 100);
        	$table->boolean('is_deleted');
        	$table->integer('created_by_id')->unsigned();
        	$table->integer('updated_by_id')->unsigned();
        	$table->timestamps();

        	$table->foreign('created_by_id')->references('id')->on('users')->onDelete('cascade');
        	$table->foreign('updated_by_id')->references('id')->on('users')->onDelete('cascade');
        });

        DB::table('sysu_companies')->insert([
        	['id_company' => '1','name' => 'Cartro','database_name' => 'siie_cartro','dbms_host' => 'localhost','dbms_port' => '3306','user_name' => 'root','user_password' => 'msroot', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
        	['id_company' => '2','name' => 'AETH','database_name' => 'siie_aeth','dbms_host' => 'localhost','dbms_port' => '3306','user_name' => 'root','user_password' => 'msroot', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('sysu_companies');
    }
}
