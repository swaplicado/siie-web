<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsAddProdPlanTable extends Migration {
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

          Schema::connection($this->sConnection)->create('mms_floor', function (blueprint $table) {
            $table->increments('id_floor');
            $table->char('code', 50);
            $table->char('name', 100);
            $table->boolean('is_deleted');
            $table->integer('branch_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();

            $table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('mms_floor')->insert([
          	['id_floor' => '1','code' => 'NA','name' => 'N/A', 'is_deleted' => '1',
            'branch_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('mms_production_planes', function (blueprint $table) {
          	$table->increments('id_production_plan');
          	$table->char('folio', 50);
          	$table->char('production_plan', 150);
          	$table->date('dt_start');
          	$table->date('dt_end');
          	$table->boolean('is_deleted');
          	$table->integer('floor_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('floor_id')->references('id_floor')->on('mms_floor')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('mms_production_planes')->insert([
          	['id_production_plan' => '1','folio' => '0','production_plan' => 'N/A',
            'dt_start' => '2017-01-01','dt_end' => '2017-01-01', 'is_deleted' => '1',
            'floor_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('mms_production_planes');
          Schema::connection($this->sConnection)->drop('mms_floor');
        }
    }
}
