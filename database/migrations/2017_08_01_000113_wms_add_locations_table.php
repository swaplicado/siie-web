<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddLocationsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wmsu_whs_locations', function (blueprint $table) {
          	$table->increments('id_whs_location');
          	$table->char('code', 50);
          	$table->char('name', 100);
          	$table->boolean('is_default');
            $table->boolean('is_recondition');
            $table->boolean('is_reprocess');
            $table->boolean('is_destruction');
          	$table->boolean('is_deleted');
          	$table->integer('whs_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();
          	$table->foreign('whs_id')->references('id_whs')->on('wmsu_whs')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wmsu_whs_locations')->insert([
          	['id_whs_location' => '1','code' => 'NA','name' => 'N/A','is_default' => '1','is_recondition' => '0','is_reprocess' => '0','is_destruction' => '0','is_deleted' => '0','whs_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('wmsu_whs_locations');
        }
    }
}
