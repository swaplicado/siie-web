<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
uy

class ErpAddAccessWarehouse extends Migration
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
      foreach ($this->lDatabases as $base) {
        $this->sDataBase = $base;
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        Schema::connection($this->sConnection)->create('erpu_access_whs', function (blueprint $table) {
          $table->increments('id_access_whs');
          $table->integer('user_id')->unsigned();
          $table->integer('whs_id')->unsigned();

          $table->foreign('user_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          $table->foreign('whs_id')->references('id_whs')->on('wmsu_whs')->onDelete('cascade');
        });
      }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
