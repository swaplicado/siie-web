<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddSegregationTables extends Migration {
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
          	$table->date('dt_date');
          	$table->boolean('is_deleted');
          	$table->integer('segregation_type_id')->unsigned();
          	$table->integer('reference_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('segregation_type_id')->references('id_segregation_type')->on('qmss_segregation_types')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          Schema::connection($this->sConnection)->create('wms_segregation_rows', function (blueprint $table) {
          	$table->increments('id_segregation_row');
          	$table->decimal('quantity', 23,8);
            $table->boolean('is_deleted');
          	$table->integer('segregation_id')->unsigned();
          	$table->integer('segregation_mvt_type_id')->unsigned();
            $table->integer('segregation_event_id')->unsigned();
            $table->integer('branch_id')->unsigned();
            $table->integer('whs_id')->unsigned();
            $table->integer('pallet_id')->unsigned();
          	$table->integer('lot_id')->unsigned();
          	$table->integer('year_id')->unsigned();
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
            $table->text('notes')->unsigned();
            $table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('segregation_id')->references('id_segregation')->on('wms_segregations')->onDelete('cascade');
            $table->foreign('segregation_mvt_type_id')->references('id_segregation_mvt_type')->on('qmss_segregation_mvt_types')->onDelete('cascade');
            $table->foreign('segregation_event_id')->references('id_segregation_event')->on('qmss_segregation_events')->onDelete('cascade');
            $table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
            $table->foreign('whs_id')->references('id_whs')->on('wmsu_whs')->onDelete('cascade');
            $table->foreign('pallet_id')->references('id_pallet')->on('wms_pallets')->onDelete('cascade');
            $table->foreign('lot_id')->references('id_lot')->on('wms_lots')->onDelete('cascade');
          	$table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
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
          SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

          Schema::connection($this->sConnection)->drop('wms_segregation_rows');
          Schema::connection($this->sConnection)->drop('wms_segregations');
        }
    }
}
