<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class AddProductionTable extends Migration
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

        Schema::connection($this->sConnection)->create('mms_status_order', function (blueprint $table) {
          $table->increments('id_status');
          $table->char('name', 100);
          $table->boolean('is_deleted');
          $table->integer('created_by_id')->unsigned();
          $table->integer('updated_by_id')->unsigned();
          $table->timestamps();

          $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
        });
        DB::connection($this->sConnection)->table('mms_status_order')->insert([
          ['id_status' => '1','name' => 'NUEVA','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_status' => '2','name' => 'EN PESADO','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_status' => '3','name' => 'EN PISO','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_status' => '4','name' => 'EN PROCESO','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_status' => '5','name' => 'TERMINADA','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_status' => '6','name' => 'CERRADA','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
        ]);

        Schema::connection($this->sConnection)->create('mms_type_order', function (blueprint $table) {
          $table->increments('id_type');
          $table->char('name', 100);
          $table->boolean('is_requested_father');
          $table->boolean('is_deleted');
          $table->integer('created_by_id')->unsigned();
          $table->integer('updated_by_id')->unsigned();
          $table->timestamps();

          $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
        });

        DB::connection($this->sConnection)->table('mms_type_order')->insert([
          ['id_type' => '1','name' => 'CONTINUA','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_type' => '2','name' => 'LOTE','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_type' => '3','name' => 'PREPARACION','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_type' => '4','name' => 'EMPAQUE','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
          ['id_type' => '5','name' => 'REACONDICIONAMIENTO','is_deleted' => '0','created_by_id' => '1', 'updated_by_id' => '1'],
        ]);

        Schema::connection($this->sConnection)->create('mms_production_orders', function (blueprint $table) {
        	$table->increments('id_order');
        	$table->char('folio', 50);
        	$table->char('identifier', 150);
        	$table->date('date');
        	$table->integer('charges');
        	$table->boolean('is_deleted');
        	$table->integer('plan_id')->unsigned();
        	$table->integer('branch_id')->unsigned();
        	$table->integer('floor_id')->unsigned();
        	$table->integer('type_id')->unsigned();
        	$table->integer('status_id')->unsigned();
        	$table->integer('item_id')->unsigned();
        	$table->integer('unit_id')->unsigned();
        	$table->integer('father_order_id')->unsigned();
        	$table->integer('formula_id')->unsigned();
        	$table->integer('created_by_id')->unsigned();
        	$table->integer('updated_by_id')->unsigned();
        	$table->timestamps();

        	$table->foreign('plan_id')->references('id_production_plan')->on('mms_production_planes')->onDelete('cascade');
        	$table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
        	$table->foreign('floor_id')->references('id_floor')->on('mms_floor')->onDelete('cascade');
        	$table->foreign('type_id')->references('id_type')->on('mms_type_order')->onDelete('cascade');
        	$table->foreign('status_id')->references('id_status')->on('mms_status_order')->onDelete('cascade');
        	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
        	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
        	$table->foreign('father_order_id')->references('id_order')->on('mms_production_orders')->onDelete('cascade');
        	$table->foreign('formula_id')->references('id_formula')->on('mms_formulas')->onDelete('cascade');
        	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
        	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
        });

        DB::connection($this->sConnection)->table('mms_production_orders')->insert([
        	['id_order' => '1','folio' => '0','identifier' => '','date' => '2017-01-01',
            'charges' => '0', 'is_deleted' => '1', 'plan_id' => '1', 'branch_id' => '1',
            'floor_id' => '1', 'type_id' => '1', 'status_id' => '1', 'item_id' => '1',
             'unit_id' => '1', 'father_order_id' => '1', 'formula_id' => '1',
             'created_by_id' => '1', 'updated_by_id' => '1'],
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

        Schema::connection($this->sConnection)->drop('mms_production_orders');
        Schema::connection($this->sConnection)->drop('mms_type_order');
        Schema::connection($this->sConnection)->drop('mms_status_order');
      }
    }
}
