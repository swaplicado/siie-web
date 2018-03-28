<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddSupplyTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wms_indirect_supply_links', function (blueprint $table) {
          	$table->bigIncrements('id_indirect_supply_link');
          	$table->decimal('quantity', 23,8);
          	$table->boolean('is_deleted');
          	$table->bigInteger('src_doc_row_id')->unsigned();
          	$table->bigInteger('des_doc_row_id')->unsigned();
          	$table->bigInteger('mvt_row_id')->unsigned();
          	$table->integer('pallet_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('src_doc_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
          	$table->foreign('des_doc_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
          	$table->foreign('mvt_row_id')->references('id_mvt_row')->on('wms_mvt_rows')->onDelete('cascade');
          	$table->foreign('pallet_id')->references('id_pallet')->on('wms_pallets')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_indirect_supply_links')->insert([
          	['id_indirect_supply_link' => '1','quantity' => '0','is_deleted' => '1','src_doc_row_id' => '1','des_doc_row_id' => '1','mvt_row_id' => '1','pallet_id' => '1','created_by_id' => '1','updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('wms_indirect_supply_link_lots', function (blueprint $table) {
          	$table->bigIncrements('id_indirect_supply_link_lot');
          	$table->decimal('quantity', 23,8);
          	$table->boolean('is_deleted');
          	$table->bigInteger('indirect_supply_link_id')->unsigned();
          	$table->integer('lot_id')->unsigned();
          	$table->bigInteger('mvt_row_lot_id')->unsigned();

          	$table->foreign('indirect_supply_link_id')->references('id_indirect_supply_link')->on('wms_indirect_supply_links')->onDelete('cascade');
          	$table->foreign('lot_id')->references('id_lot')->on('wms_lots')->onDelete('cascade');
            $table->foreign('mvt_row_lot_id')->references('id_mvt_row_lot')->on('wms_mvt_row_lots')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_indirect_supply_link_lots')->insert([
          	['id_indirect_supply_link_lot' => '1','quantity' => '0','is_deleted' => '1','indirect_supply_link_id' => '1','lot_id' => '1','mvt_row_lot_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('wms_indirect_supply_link_lots');
          Schema::connection($this->sConnection)->drop('wms_indirect_supply_links');
        }
    }
}
