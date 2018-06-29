<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddItemStatusTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erps_item_status', function (blueprint $table) {
          	$table->increments('id_item_status');
          	$table->char('code', 3)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_item_status')->insert([
          	['id_item_status' => '1','code' => 'ACT','name' => 'ACTIVO', 'is_deleted' => '0'],
          	['id_item_status' => '2','code' => 'RES','name' => 'RESTRINGIDO', 'is_deleted' => '0'],
          	['id_item_status' => '3','code' => 'BLO','name' => 'BLOQUEADO', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->table('erpu_items', function ($table) {
              $table->integer('item_status_id')->unsigned()->default(1)->after('unit_id');

              $table->foreign('item_status_id')->references('id_item_status')->on('erps_item_status')->onDelete('cascade');
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

          Schema::connection($this->sConnection)->table('erpu_items', function ($table) {
              $table->dropForeign(['item_status_id']);

              $table->dropColumn('item_status_id');
          });

          Schema::connection($this->sConnection)->drop('erps_item_status');
        }
    }
}
