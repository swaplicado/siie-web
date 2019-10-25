<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddZonesTable extends Migration
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

            Schema::connection($this->sConnection)->create('qmss_config_zones', function (blueprint $table) {	
                $table->increments('id_config_zone');
                $table->char('zone_name', 150);
                $table->boolean('is_deleted');
                $table->timestamps();
            });

            DB::connection($this->sConnection)->table('qmss_config_zones')->insert([
                ['id_config_zone' => '1','zone_name' => 'Papeletas FQ','is_deleted'=>'0'],
                ['id_config_zone' => '2','zone_name' => 'Papeletas QB','is_deleted'=>'0'],
                ['id_config_zone' => '3','zone_name' => 'Papeletas OL','is_deleted'=>'0'],
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

          Schema::connection($this->sConnection)->drop('qmss_config_zones');
        }
    }
}
