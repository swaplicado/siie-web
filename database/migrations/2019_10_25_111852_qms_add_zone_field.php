<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddZoneField extends Migration
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
            SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

            Schema::connection($this->sConnection)->table('qms_doc_configurations', function (blueprint $table) {
                $table->integer('config_zone_id')->unsigned()->default('1')->after('element_id');

                $table->foreign('config_zone_id')->references('id_config_zone')->on('qmss_config_zones')->onDelete('cascade');
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

            Schema::connection($this->sConnection)->table('qms_doc_configurations', function($table)
            {
                $table->dropForeign(['config_zone_id']);

                $table->dropColumn('config_zone_id');
            });
        }
    }
}
