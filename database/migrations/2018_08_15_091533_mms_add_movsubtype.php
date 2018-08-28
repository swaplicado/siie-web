<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsAddMovsubtype extends Migration
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

        DB::connection($this->sConnection)->table('wmss_mvt_mfg_types')->insert([
          ['id_mvt_mfg_type' => '4','code' => 'MEM','name' => 'MATERIAL DE EMPAQUE','is_deleted' => '0'],
          ['id_mvt_mfg_type' => '5','code' => 'MPT','name' => 'PRODUCTO TERMINADO','is_deleted' => '0'],
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

        DB::connection($this->sConnection)->table('wmss_mvt_mfg_types')
                                              ->where('id_mvt_mfg_type', '4')
                                              ->delete();
        DB::connection($this->sConnection)->table('wmss_mvt_mfg_types')
                                              ->where('id_mvt_mfg_type', '5')
                                              ->delete();
      }
    }
}
