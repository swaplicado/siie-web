<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class SysAddAnaCertPermissions extends Migration {
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
        ['code' => '053','name' => 'CERTIFICADOS', 'is_deleted' => '0','module_id' => '3'],
        ['code' => '054','name' => 'CONFIGURACIÓN DE CERTIFICADOS Y ANÁLISIS', 'is_deleted' => '0','module_id' => '3'],
        ['code' => '055','name' => 'ZONA FISICOQUÍMICOS', 'is_deleted' => '0','module_id' => '3'],
        ['code' => '056','name' => 'ZONA MICROBIOLÓGICOS', 'is_deleted' => '0','module_id' => '3'],
        ['code' => '057','name' => 'ZONA ORGANOLÉPTICOS', 'is_deleted' => '0','module_id' => '3'],
      ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      DB::table('syss_permissions')->whereIn('code', [
                                  '053', 
                                  '054', 
                                  '055', 
                                  '056', 
                                  '057'
                                  ])->delete();
    }
}
