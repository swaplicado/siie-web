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

          Schema::connection($this->sConnection)->create('qmss_segregation_events', function (blueprint $table) {
          	$table->increments('id_segregation_event');
          	$table->char('code', 5);
          	$table->char('name', 50);
          	$table->boolean('is_deleted');
          	$table->char('origin_type', 1);
          	$table->timestamps();


          });

          DB::connection($this->sConnection)->table('qmss_segregation_events')->insert([
          	['id_segregation_event' => '1','code' => 'EMBQS','name' => 'EMBARQUES', 'is_deleted' => '0','origin_type'=>'E'],
            ['id_segregation_event' => '2','code' => 'PROCN','name' => 'PRODUCCION', 'is_deleted' => '0','origin_type'=>'P'],
            ['id_segregation_event' => '3','code' => 'INSPE','name' => 'POR INSPECCIONAR', 'is_deleted' => '0','origin_type'=>'Q'],
            ['id_segregation_event' => '4','code' => 'CUARE','name' => 'EN CUARENTENA', 'is_deleted' => '0','origin_type'=>'Q'],
            ['id_segregation_event' => '5','code' => 'LIBAN','name' => 'LIBERACION ANTICIPADA', 'is_deleted' => '0','origin_type'=>'Q'],
            ['id_segregation_event' => '6','code' => 'LIBPA','name' => 'LIBERACION PARCIAL', 'is_deleted' => '0','origin_type'=>'Q'],
            ['id_segregation_event' => '7','code' => 'LIBTO','name' => 'LIBERACION TOTAL', 'is_deleted' => '0','origin_type'=>'Q'],
            ['id_segregation_event' => '8','code' => 'REREA','name' => 'RECHAZO, A REACONDICIONAMIENTO', 'is_deleted' => '0','origin_type'=>'Q'],
            ['id_segregation_event' => '9','code' => 'REREP','name' => 'RECHAZO, A REPROCESAMIENTO', 'is_deleted' => '0','origin_type'=>'Q'],
            ['id_segregation_event' => '10','code' => 'REDES','name' => 'RECHAZO, A DESTRUIR', 'is_deleted' => '0','origin_type'=>'Q'],
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

          Schema::connection($this->sConnection)->drop('qmss_segregation_events');
        }
    }
}
