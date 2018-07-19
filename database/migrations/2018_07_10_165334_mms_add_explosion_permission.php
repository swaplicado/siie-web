<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsAddExplosionPermission extends Migration
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
        DB::table('syss_permissions')->insert([
          ['code' => '121','name' => 'PLANES DE PRODUCCIÓN', 'is_deleted' => '0','module_id' => '2'],
          ['code' => '122','name' => 'PLANTAS', 'is_deleted' => '0','module_id' => '2'],
          ['code' => '123','name' => 'EXPLOSIÓN DE MATERIALES', 'is_deleted' => '0','module_id' => '2'],
          ['code' => '124','name' => 'ÓRDENES DE PRODUCCIÓN', 'is_deleted' => '0','module_id' => '2'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('syss_permissions')->where('code', '121')->delete();
        DB::table('syss_permissions')->where('code', '122')->delete();
        DB::table('syss_permissions')->where('code', '123')->delete();
        DB::table('syss_permissions')->where('code', '124')->delete();
    }
}
