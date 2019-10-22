<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class SysAddQdocsPermission extends Migration {
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
      DB::table('syss_permissions')->insert([
        ['code' => '051','name' => 'CONFIGURACIÓN PAPELETAS CALIDAD', 'is_deleted' => '0','module_id' => '3'],
        ['code' => '052','name' => 'PAPELETAS CALIDAD', 'is_deleted' => '0','module_id' => '3'],
        ['code' => '070','name' => 'REPORTES CALIDAD', 'is_deleted' => '0','module_id' => '3'],
      ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::table('syss_permissions')->where('code', '051')->delete();
      DB::table('syss_permissions')->where('code', '052')->delete();
      DB::table('syss_permissions')->where('code', '070')->delete();
    }
}
