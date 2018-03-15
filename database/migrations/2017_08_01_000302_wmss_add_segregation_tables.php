<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmssAddSegregationTables extends Migration {
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

          Schema::connection($this->sConnection)->create('qmss_segregation_types', function (blueprint $table) {
          	$table->increments('id_segregation_type');
          	$table->char('code', 5);
          	$table->char('name', 50);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('qmss_segregation_types')->insert([
          	['id_segregation_type' => '1','code' => 'EMB','name'=>'ORDEN DE EMBARQUE', 'is_deleted' => '0'],
          	['id_segregation_type' => '2','code' => 'OP','name'=>'ORDEN DE PRODUCCIÓN', 'is_deleted' => '0'],
          	['id_segregation_type' => '3','code' => 'INS','name'=>'CALIDAD POR INSPECCIONAR', 'is_deleted' => '0'],
            ['id_segregation_type' => '4','code' => 'CUA','name'=>'CALIDAD EN CUARENTENA', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('qmss_segregation_mvt_types', function (blueprint $table) {
          	$table->increments('id_segregation_mvt_type');
          	$table->char('code', 5);
          	$table->char('name', 50);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('qmss_segregation_mvt_types')->insert([
          	['id_segregation_mvt_type' => '1','code' => 'INC','name'=>'INCREMENTO', 'is_deleted' => '0'],
          	['id_segregation_mvt_type' => '2','code' => 'DEC','name'=>'DECREMENTO', 'is_deleted' => '0'],
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

          Schema::connection($this->sConnection)->drop('qmss_seg_mov_types');
          Schema::connection($this->sConnection)->drop('qmss_segregation_types');
        }
    }
}
