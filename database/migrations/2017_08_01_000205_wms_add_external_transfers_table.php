<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddExternalTransfersTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wms_external_transfers', function (blueprint $table) {
          	$table->bigIncrements('id_external_transfer');
          	$table->boolean('is_deleted');
          	$table->integer('src_branch_id')->unsigned();
          	$table->integer('des_branch_id')->unsigned();
          	$table->bigInteger('mvt_reference_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
            $table->timestamps();

          	$table->foreign('src_branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
          	$table->foreign('des_branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
          	$table->foreign('mvt_reference_id')->references('id_mvt')->on('wms_mvts')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_external_transfers')->insert([
          	['id_external_transfer' => '1','is_deleted' => '0','src_branch_id' => '1','des_branch_id' => '1','mvt_reference_id' => '1','created_by_id' => '1','updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('wms_external_transfers');
        }
    }
}
