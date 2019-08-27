<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpsAddImportRows extends Migration
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

        DB::connection($this->sConnection)->table('erps_importations')->insert([
            ['id_importation' => '12','code' => '012','name' => 'FÓRMULAS','last_importation' => '2019-02-18','updated_by_id' => '1'],
            ['id_importation' => '13','code' => '013','name' => 'ÓRDENES DE PRODUCCIÓN','last_importation' => '2019-07-01','updated_by_id' => '1'],
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                  $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        DB::connection($this->sConnection)->table('erps_importations')
                                              ->where('id_importation', '12')
                                              ->delete();
        DB::connection($this->sConnection)->table('erps_importations')
                                              ->where('id_importation', '13')
                                              ->delete();
      }
    }
}
