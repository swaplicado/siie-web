<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SUtil;

class WmsAddWhsTypesTable extends Migration {
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
          SUtil::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

          Schema::connection($this->sConnection)->create('wmss_whs_types', function (blueprint $table) {
          	$table->increments('id_type');
          	$table->char('code', 5)->unique();
          	$table->char('name', 50);
          	$table->boolean('is_deleted');
          });

          DB::connection($this->sConnection)->table('wmss_whs_types')->insert([
          	['id_type' => '1','code' => 'NA','name' => 'N/A','is_deleted' => '0'],
          	['id_type' => '2','code' => 'MA','name' => 'MATERIALES','is_deleted' => '0'],
          	['id_type' => '3','code' => 'PC','name' => 'PRODUCCIÓN','is_deleted' => '0'],
          	['id_type' => '4','code' => 'PS','name' => 'PRODUCTOS','is_deleted' => '0'],
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
          SUtil::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

          Schema::connection($this->sConnection)->drop('wmss_whs_types');
        }
    }
}
