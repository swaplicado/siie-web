<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddContainerFoliosTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wmsu_container_folios', function (blueprint $table) {
          	$table->increments('id_container_folio');
          	$table->integer('folio_start');
          	$table->boolean('is_deleted');
          	$table->integer('container_type_id')->unsigned();
          	$table->integer('container_id')->unsigned();
          	$table->integer('mvt_class_id')->unsigned();
          	$table->integer('mvt_type_id')->unsigned();
          	$table->integer('mvt_trn_type_id')->unsigned();
          	$table->integer('mvt_adj_type_id')->unsigned();
          	$table->integer('mvt_mfg_type_id')->unsigned();
          	$table->integer('mvt_exp_type_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('container_type_id')->references('id_container_type')->on('wmss_container_types')->onDelete('cascade');
          	$table->foreign('mvt_class_id')->references('id_mvt_class')->on('wmss_mvt_classes')->onDelete('cascade');
          	$table->foreign('mvt_type_id')->references('id_mvt_type')->on('wmss_mvt_types')->onDelete('cascade');
          	$table->foreign('mvt_trn_type_id')->references('id_mvt_trn_type')->on('wmss_mvt_trn_types')->onDelete('cascade');
          	$table->foreign('mvt_adj_type_id')->references('id_mvt_adj_type')->on('wmss_mvt_adj_types')->onDelete('cascade');
          	$table->foreign('mvt_mfg_type_id')->references('id_mvt_mfg_type')->on('wmss_mvt_mfg_types')->onDelete('cascade');
          	$table->foreign('mvt_exp_type_id')->references('id_mvt_exp_type')->on('wmss_mvt_exp_types')->onDelete('cascade');
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

          Schema::connection($this->sConnection)->drop('wmsu_container_folios');
        }
    }
}
