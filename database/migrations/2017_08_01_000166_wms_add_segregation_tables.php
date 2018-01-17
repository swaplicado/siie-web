<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddSegregationTables extends Migration {
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

          Schema::connection($this->sConnection)->create('wms_segregations', function (blueprint $table) {
          	$table->increments('id_segregation');
          	$table->boolean('is_deleted');
          	$table->integer('segregation_type_id')->unsigned();
          	$table->integer('reference_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('segregation_type_id')->references('id_segregation_type')->on('wmss_segregation_types')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_segregations')->insert([
          	['id_segregation' => '1','is_deleted' => '3','segregation_type_id' => '3','reference_id'=>'1', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('wms_segregation_rows', function (blueprint $table) {
          	$table->increments('id_segregation_row');
          	$table->decimal('quantity', 23,8);
          	$table->integer('segregation_id')->unsigned();
          	$table->integer('move_type_id')->unsigned();
          	$table->integer('pallet_id')->unsigned();
          	$table->integer('whs_id')->unsigned();
          	$table->integer('branch_id')->unsigned();
          	$table->integer('year_id')->unsigned();
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('quality_status_id')->unsigned();

          	$table->foreign('segregation_id')->references('id_segregation')->on('wms_segregations')->onDelete('cascade');
            $table->foreign('move_type_id')->references('id_seg_mov_type_id')->on('wmss_seg_mov_types')->onDelete('cascade');
          	$table->foreign('pallet_id')->references('id_pallet')->on('wms_pallets')->onDelete('cascade');
          	$table->foreign('whs_id')->references('id_whs')->on('wmsu_whs')->onDelete('cascade');
          	$table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
          	$table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('quality_status_id')->references('id_status')->on('qmss_quality_status')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_segregation_rows')->insert([
          	['id_segregation_row' => '1','quantity' => '0','move_type_id' => '1','segregation_id'=>'1','pallet_id' => '1','branch_id' => '1','year_id' => '1','item_id' => '1','whs_id' => '1','unit_id' => '1','quality_status_id' => '5'],
          ]);

          Schema::connection($this->sConnection)->create('wms_seg_lot_rows', function (blueprint $table) {
          	$table->increments('id_seg_lot_row');
          	$table->decimal('quantity', 23,8);
          	$table->integer('move_type_id')->unsigned();
          	$table->integer('segregation_row_id')->unsigned();
          	$table->integer('lot_id')->unsigned();
          	$table->integer('year_id')->unsigned();
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('quality_status_id')->unsigned();

          	$table->foreign('move_type_id')->references('id_seg_mov_type_id')->on('wmss_seg_mov_types')->onDelete('cascade');
          	$table->foreign('segregation_row_id')->references('id_segregation_row')->on('wms_segregation_rows')->onDelete('cascade');
          	$table->foreign('lot_id')->references('id_lot')->on('wms_lots')->onDelete('cascade');
          	$table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('quality_status_id')->references('id_status')->on('qmss_quality_status')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_seg_lot_rows')->insert([
          	['id_seg_lot_row' => '1','quantity' => '0','move_type_id'=>'1','segregation_row_id' => '1','year_id' => '1','item_id' => '1','unit_id' => '1','lot_id' => '1','quality_status_id' => '5'],
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

          Schema::connection($this->sConnection)->drop('wms_seg_lot_rows');
          Schema::connection($this->sConnection)->drop('wms_segregation_rows');
          Schema::connection($this->sConnection)->drop('wms_segregations');
        }
    }
}
