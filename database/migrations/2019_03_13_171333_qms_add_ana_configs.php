<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddAnaConfigs extends Migration
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
    
            Schema::connection($this->sConnection)->create('qms_ana_configs', function (blueprint $table) {
                $table->increments('id_config');
                $table->boolean('is_deleted');
                $table->integer('analysis_id')->unsigned();
                $table->integer('item_link_type_id')->unsigned();
                $table->integer('item_link_id')->unsigned();
                $table->decimal('min_value', 15,6);
                $table->decimal('max_value', 15,6);
                $table->integer('created_by_id')->unsigned();
                $table->integer('updated_by_id')->unsigned();
                $table->timestamps();

                $table->unique(['analysis_id', 'item_link_type_id', 'item_link_id'], 'configuration_unique');
                $table->foreign('analysis_id')->references('id_analysis')->on('qms_analysis')->onDelete('cascade');
                $table->foreign('item_link_type_id')->references('id_item_link_type')->on('erps_item_link_types')->onDelete('cascade');
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
    
            Schema::connection($this->sConnection)->drop('qms_ana_configs');
        }
    }
}
