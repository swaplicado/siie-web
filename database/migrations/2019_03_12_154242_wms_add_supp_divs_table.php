<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddSuppDivsTable extends Migration
{
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
            SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);
                
            Schema::connection($this->sConnection)->create('wms_supplies_divisions', function (blueprint $table) {	
            $table->bigIncrements('id_division');
            $table->boolean('is_deleted');
            $table->bigInteger('out_division_id')->unsigned();
            $table->bigInteger('in_division_id')->unsigned();
            $table->bigInteger('mvt_reference_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('out_division_id')->references('id_mvt')->on('wms_mvts')->onDelete('cascade');
            $table->foreign('in_division_id')->references('id_mvt')->on('wms_mvts')->onDelete('cascade');
            $table->foreign('mvt_reference_id')->references('id_mvt')->on('wms_mvts')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                    $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        Schema::connection($this->sConnection)->drop('wms_supplies_divisions');
      }
    }
}
