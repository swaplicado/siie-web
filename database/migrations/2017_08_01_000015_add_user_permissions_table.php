<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class AddUserPermissionsTable extends Migration
{
   private $lDatabases;
   private $sConnection;
   private $sDataBase;
   private $bDefault;
   private $sHost;
   private $sUser;
   private $sPassword;


    public function __construct()
     {
       $this->lDatabases = Config::getDataBases();
       $this->sConnection = 'company';
       $this->sDataBase = '';
       $this->bDefault = false;
       $this->sHost = NULL;
       $this->sUser = NULL;
       $this->sPassword = NULL;
     }

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
      	$table->integer('branch_id')->unsigned();
      	$table->integer('whs_id')->unsigned();

      	$table->unique(['user_id','permission_id','privilege_id']);
      	$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
      	$table->foreign('permission_id')->references('id_permission')->on('syss_permissions')->onDelete('cascade');
      	$table->foreign('privilege_id')->references('id_privilege')->on('syss_privileges')->onDelete('cascade');
      	$table->foreign('module_id')->references('id_module')->on('syss_modules')->onDelete('cascade');

        $table->foreign('branch_id')->references('id_branch')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'erpu_branches')->onDelete('cascade');
      	$table->foreign('whs_id')->references('id_whs')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'wmsu_whs')->onDelete('cascade');
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
