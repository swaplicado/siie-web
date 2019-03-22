<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddQltyConfigurations extends Migration
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

        DB::connection($this->sConnection)->table('erp_configuration', function ($table) {
            $table->string('val_text', 200)->change();
        });

        DB::connection($this->sConnection)->table('erp_configuration')->insert([
          ['id_configuration' => '16','code' => '016','name' => 'SUPERVISOR MICROB','val_boolean' => '0','val_int' => '0',
                    'val_text' => 'I.B.Q. Jorge Luis Pérez Arias ## Q.F.B. Mayeli Elideth Rodríguez Casas',
                    'val_decimal' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_configuration' => '17','code' => '017','name' => 'GERENTE CAL','val_boolean' => '0','val_int' => '0',
                    'val_text' => 'Q.F.B. Francisco Santiago Chima',
                    'val_decimal' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          
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
                                              ->whereIn('id_configuration', ['16', '17'])
                                              ->delete();

        DB::connection($this->sConnection)->table('erp_configuration', function ($table) {
            $table->string('val_text', 50)->change();
        });
      }
    }
}
