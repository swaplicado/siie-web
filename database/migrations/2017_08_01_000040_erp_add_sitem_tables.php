<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddSitemTables extends Migration {
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

          Schema::connection($this->sConnection)->create('erps_item_link_types', function (blueprint $table) {
          	$table->increments('id_item_link_type');
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          });

          DB::connection($this->sConnection)->table('erps_item_link_types')->insert([
          	['id_item_link_type' => '1','name' => 'TODO','is_deleted' => '0'],
          	['id_item_link_type' => '2','name' => 'CLASE ÍTEM','is_deleted' => '0'],
          	['id_item_link_type' => '3','name' => 'TIPO ÍTEM','is_deleted' => '0'],
          	['id_item_link_type' => '4','name' => 'FAMILIA ÍTEM','is_deleted' => '0'],
          	['id_item_link_type' => '5','name' => 'GRUPO ÍTEM','is_deleted' => '0'],
          	['id_item_link_type' => '6','name' => 'GÉNERO ÍTEM','is_deleted' => '0'],
          	['id_item_link_type' => '7','name' => 'ÍTEM','is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('erps_item_classes', function (blueprint $table) {
          	$table->increments('id_class');
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          });

          DB::connection($this->sConnection)->table('erps_item_classes')->insert([
          	['id_class' => '1','name' => 'MATERIAL','is_deleted' => '0'],
          	['id_class' => '2','name' => 'PRODUCTO','is_deleted' => '0'],
          	['id_class' => '3','name' => 'GASTO','is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('erps_item_types', function (blueprint $table) {
          	$table->increments('id_item_type');
          	$table->integer('id_type');
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->integer('class_id')->unsigned();

          	$table->unique(['class_id', 'id_type']);
          	$table->foreign('class_id')->references('id_class')->on('erps_item_classes')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erps_item_types')->insert([
          	['id_item_type' => '1','id_type' => '1','name' => 'MATERIAL DIRECTO INSUMO', 'is_deleted' => '0', 'class_id' => '1'],
          	['id_item_type' => '2','id_type' => '2','name' => 'MATERIAL DIRECTO EMPAQUE', 'is_deleted' => '0', 'class_id' => '1'],
          	['id_item_type' => '3','id_type' => '3','name' => 'MATERIAL INDIRECTO', 'is_deleted' => '0', 'class_id' => '1'],
          	['id_item_type' => '4','id_type' => '4','name' => 'REPROCESO', 'is_deleted' => '0', 'class_id' => '1'],
          	['id_item_type' => '5','id_type' => '1','name' => 'PRODUCTO', 'is_deleted' => '0', 'class_id' => '2'],
          	['id_item_type' => '6','id_type' => '2','name' => 'PRODUCTO BASE', 'is_deleted' => '0', 'class_id' => '2'],
          	['id_item_type' => '7','id_type' => '3','name' => 'PRODUCTO TERMINADO', 'is_deleted' => '0', 'class_id' => '2'],
          	['id_item_type' => '8','id_type' => '4','name' => 'SUBPRODUCTO', 'is_deleted' => '0', 'class_id' => '2'],
          	['id_item_type' => '9','id_type' => '5','name' => 'DESECHO', 'is_deleted' => '0', 'class_id' => '2'],
          	['id_item_type' => '10','id_type' => '1','name' => 'GASTO COMPRAS', 'is_deleted' => '0', 'class_id' => '3'],
          	['id_item_type' => '11','id_type' => '2','name' => 'GASTO DIRECTO', 'is_deleted' => '0', 'class_id' => '3'],
          	['id_item_type' => '12','id_type' => '3','name' => 'GASTO INDIRECTO', 'is_deleted' => '0', 'class_id' => '3'],
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

          Schema::connection($this->sConnection)->drop('erps_item_types');
          Schema::connection($this->sConnection)->drop('erps_item_classes');
          Schema::connection($this->sConnection)->drop('erps_item_link_types');
        }
    }
}
