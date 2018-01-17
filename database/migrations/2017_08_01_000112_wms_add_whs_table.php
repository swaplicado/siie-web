<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddWhsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wmsu_whs', function (blueprint $table) {
          	$table->increments('id_whs');
          	$table->char('code', 50)->unique();
          	$table->char('name', 100);
            $table->boolean('is_quality');
          	$table->boolean('is_deleted');
          	$table->integer('branch_id')->unsigned();
          	$table->integer('whs_type_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
          	$table->foreign('whs_type_id')->references('id_whs_type')->on('wmss_whs_types')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wmsu_whs')->insert([
            ['id_whs' => '1','code' => 'NA','name' => 'N/A','is_quality' => '0','is_deleted' => '1','branch_id' => '1','whs_type_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
            ['id_whs' => '2','code' => 'DEF','name' => 'DEFAULT','is_quality' => '0','is_deleted' => '0','branch_id' => '2','whs_type_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
            ['id_whs' => '3','code' => 'DEFA','name' => 'DEFAULT','is_quality' => '0','is_deleted' => '0','branch_id' => '3','whs_type_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('wmsu_whs');
        }
    }
}
