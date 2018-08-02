<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsAddPoFk extends Migration
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

        Schema::connection($this->sConnection)->table('wms_mvts', function (blueprint $table) {
          $table->integer('prod_ord_id')->unsigned()->default('1')->after('doc_credit_note_id');

          $table->foreign('prod_ord_id')
                  ->references('id_order')
                  ->on('mms_production_orders')
                  ->onDelete('cascade');
        });
      }

      DB::table('syss_permissions')->insert([
        ['code' => '125','name' => 'ASIGNACIÓN A ORDENES DE PRODUCCIÓN',
            'is_deleted' => '0','module_id' => '2'],
      ]);
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

        Schema::connection($this->sConnection)->table('wms_mvts', function($table)
        {
            $table->dropForeign('wms_mvts_prod_ord_id_foreign');
            $table->dropColumn('prod_ord_id');
        });
      }

      DB::table('syss_permissions')->where('code', '125')->delete();
    }
}
