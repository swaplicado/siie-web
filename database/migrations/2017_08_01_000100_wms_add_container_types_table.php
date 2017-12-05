<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddContainerTypesTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wmss_container_types', function (blueprint $table) {
          	$table->increments('id_container_type');
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();

          });

          DB::connection($this->sConnection)->table('wmss_container_types')->insert([
          	['id_container_type' => '1','name' => 'NA','is_deleted' => '1'],
          	['id_container_type' => '2','name' => 'UBICACIÓN','is_deleted' => '0'],
          	['id_container_type' => '3','name' => 'ALMACÉN','is_deleted' => '0'],
          	['id_container_type' => '4','name' => 'SUCURSAL','is_deleted' => '0'],
          	['id_container_type' => '5','name' => 'EMPRESA','is_deleted' => '0'],
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

          Schema::connection($this->sConnection)->drop('wmss_container_types');
        }
    }
}
