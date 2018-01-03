<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddMvtRowLotsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wms_mvt_row_lots', function (blueprint $table) {
          	$table->bigIncrements('id_mvt_row_lot');
          	$table->decimal('quantity', 23,8);
          	$table->decimal('amount_unit', 23,8);
          	$table->decimal('amount', 17,2);
          	$table->decimal('length', 23,8);
          	$table->decimal('surface', 23,8);
          	$table->decimal('volume', 23,8);
          	$table->decimal('mass', 23,8);
          	$table->bigInteger('mvt_row_id')->unsigned();
          	$table->integer('lot_id')->unsigned();
          	
          	$table->foreign('mvt_row_id')->references('id_mvt_row')->on('wms_mvt_rows')->onDelete('cascade');
          	$table->foreign('lot_id')->references('id_lot')->on('wms_lots')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_mvt_row_lots')->insert([
            ['id_mvt_row_lot' => '1','quantity' => '0','amount_unit' => '0','amount' => '0',
            'length' => '0','surface' => '0','volume' => '0','mass' => '0','mvt_row_id' => '1','lot_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('wms_mvt_row_lots');
        }
    }
}
