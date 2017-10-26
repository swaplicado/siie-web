<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddAuthStatusTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erps_auth_status', function (blueprint $table) {
          	$table->increments('id_auth_status');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_auth_status')->insert([
          	['id_auth_status' => '1','code' => ' ','name' => 'N/A', 'is_deleted' => '0'],
          	['id_auth_status' => '2','code' => 'PA','name' => 'POR AUTORIZAR', 'is_deleted' => '0'],
          	['id_auth_status' => '3','code' => 'A','name' => 'AUTORIZADO', 'is_deleted' => '0'],
          	['id_auth_status' => '4','code' => 'R','name' => 'RECHAZAD', 'is_deleted' => '0'],
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

          Schema::connection($this->sConnection)->drop('erps_auth_status');
        }
    }
}
