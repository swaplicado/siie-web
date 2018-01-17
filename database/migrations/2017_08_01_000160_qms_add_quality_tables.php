<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddQualityTables extends Migration {
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

          Schema::connection($this->sConnection)->create('qmss_status_types', function (blueprint $table) {
          	$table->increments('id_status_type');
          	$table->char('code', 5);
          	$table->char('name', 50);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('qmss_status_types')->insert([
          	['id_status_type' => '1','code' => 'EP','name' => 'ENTRADA DE PRODUCTO', 'is_deleted' => '0'],
          	['id_status_type' => '2','code' => 'DP','name' => 'DEVOLUCIÓN DE VENTAS', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('qmss_quality_status', function (blueprint $table) {
          	$table->increments('id_status');
          	$table->char('code', 5);
          	$table->char('name', 50);
          	$table->boolean('is_deleted');
          	$table->integer('status_type_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('status_type_id')->references('id_status_type')->on('qmss_status_types')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('qmss_quality_status')->insert([
          	['id_status' => '1','code' => 'EPE','name' => 'POR EVALUAR', 'is_deleted' => '0','status_type_id'=>'1'],
          	['id_status' => '2','code' => 'ERE','name' => 'RECHAZADO', 'is_deleted' => '0','status_type_id'=>'1'],
          	['id_status' => '3','code' => 'ECU','name' => 'CUARENTENA', 'is_deleted' => '0','status_type_id'=>'1'],
          	['id_status' => '4','code' => 'ELP','name' => 'LIBERADO PARCIAL', 'is_deleted' => '0','status_type_id'=>'1'],
          	['id_status' => '5','code' => 'ELI','name' => 'LIBERADO', 'is_deleted' => '0','status_type_id'=>'1'],
          	['id_status' => '6','code' => 'ELA','name' => 'LIBERADO ANTICIPADO', 'is_deleted' => '0','status_type_id'=>'1'],
          	['id_status' => '7','code' => 'DPE','name' => 'POR EVALUAR', 'is_deleted' => '0','status_type_id'=>'2'],
          	['id_status' => '8','code' => 'DRE','name' => 'REACONDICIONAR', 'is_deleted' => '0','status_type_id'=>'2'],
          	['id_status' => '9','code' => 'DRP','name' => 'REPROCESAR', 'is_deleted' => '0','status_type_id'=>'2'],
          	['id_status' => '10','code' => 'DDE','name' => 'DESTRUIR', 'is_deleted' => '0','status_type_id'=>'2'],
          ]);

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

          Schema::connection($this->sConnection)->drop('qmss_quality_status');
          Schema::connection($this->sConnection)->drop('qmss_status_types');
        }
    }
}
