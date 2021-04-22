<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsAddQtyPos extends Migration
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

            Schema::connection($this->sConnection)->table('mms_production_orders', function (blueprint $table) {
                $table->decimal('quantity', 23,8)->default('0')->after('charges');
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
    
            Schema::connection($this->sConnection)->table('mms_production_orders', function($table)
            {
                $table->dropColumn('quantity');
            });
        }
    }
}
