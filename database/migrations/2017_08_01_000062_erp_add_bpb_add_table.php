<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddBpbAddTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erpu_branch_addresses', function (blueprint $table) {
          	$table->increments('id_branch_address');
          	$table->char('name', 100);
          	$table->char('street', 100);
          	$table->char('num_ext', 50);
          	$table->char('num_int', 50);
          	$table->char('neighborhood', 100);
          	$table->char('reference', 100);
          	$table->char('locality', 100);
          	$table->char('county', 100);
          	$table->char('state', 100);
          	$table->char('zip_code', 15);
          	$table->integer('external_id');
          	$table->boolean('is_main');
          	$table->boolean('is_deleted');
          	$table->integer('branch_id')->unsigned();
          	$table->integer('country_id')->unsigned();
          	$table->integer('country_state_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('country_id')->references('id_country')->on('erps_countries')->onDelete('cascade');
          	$table->foreign('country_state_id')->references('id_state')->on('erps_country_states')->onDelete('cascade');
          	$table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
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

          Schema::connection($this->sConnection)->drop('erpu_branch_addresses');
        }
    }
}
