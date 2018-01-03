<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddAccessBranch extends Migration
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

        Schema::connection($this->sConnection)->create('erpu_access_branch', function (blueprint $table) {
          $table->increments('id_access_branch');
          $table->integer('user_id')->unsigned();
          $table->integer('branch_id')->unsigned();
          $table->boolean('is_universal');
          $table->timestamps();

          $table->foreign('user_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          $table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
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
      foreach ($this->lDatabases as $base) {
        $this->sDataBase = $base;
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        Schema::connection($this->sConnection)->drop('erpu_access_branch');
      }
    }
}
