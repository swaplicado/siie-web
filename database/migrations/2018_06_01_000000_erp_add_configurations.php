<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddConfigurations extends Migration
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

        DB::connection($this->sConnection)->table('erp_configuration')->insert([
          ['id_configuration' => '13','code' => '013','name' => 'DECIMALES PORCENTAJES','val_boolean' => '0','val_int' => '4','val_text' => ' ','val_decimal' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_configuration' => '14','code' => '014','name' => 'FOLIOS LONGITUD','val_boolean' => '0','val_int' => '5','val_text' => ' ','val_decimal' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

        DB::connection($this->sConnection)->table('erp_configuration')
                                              ->where('id_configuration', '13')
                                              ->delete();
        DB::connection($this->sConnection)->table('erp_configuration')
                                              ->where('id_configuration', '14')
                                              ->delete();
      }
    }
}
