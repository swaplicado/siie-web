<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddSystemDocumentsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erps_doc_categories', function (blueprint $table) {
          	$table->increments('id_doc_category');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_doc_categories')->insert([
          	['id_doc_category' => '1','code' => 'C','name' => 'COMPRA', 'is_deleted' => '0'],
          	['id_doc_category' => '2','code' => 'V','name' => 'VENTA', 'is_deleted' => '0'],
          ]);


          Schema::connection($this->sConnection)->create('erps_doc_classes', function (blueprint $table) {
          	$table->increments('id_doc_class');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_doc_classes')->insert([
          	['id_doc_class' => '1','code' => 'COT','name' => 'COTIZACIÓN', 'is_deleted' => '0'],
          	['id_doc_class' => '2','code' => 'PED','name' => 'PEDIDO', 'is_deleted' => '0'],
          	['id_doc_class' => '3','code' => 'DOC','name' => 'DOCUMENTO', 'is_deleted' => '0'],
          	['id_doc_class' => '4','code' => 'TRA','name' => 'TRASLADO', 'is_deleted' => '0'],
          	['id_doc_class' => '5','code' => 'AJU','name' => 'AJUSTE', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('erps_doc_types', function (blueprint $table) {
          	$table->increments('id_doc_type');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_doc_types')->insert([
          	['id_doc_type' => '1','code' => 'COTP','name' => 'COTIZACIÓN', 'is_deleted' => '0'],
          	['id_doc_type' => '2','code' => 'CONP','name' => 'CONTRATO', 'is_deleted' => '0'],
          	['id_doc_type' => '3','code' => 'PEDP','name' => 'PEDIDO', 'is_deleted' => '0'],
          	['id_doc_type' => '4','code' => 'FACP','name' => 'FACTURA', 'is_deleted' => '0'],
          	['id_doc_type' => '5','code' => 'REMP','name' => 'REMISIÓN', 'is_deleted' => '0'],
          	['id_doc_type' => '6','code' => 'NVP','name' => 'NOTA VENTA', 'is_deleted' => '0'],
          	['id_doc_type' => '7','code' => 'TICP','name' => 'TICKET', 'is_deleted' => '0'],
          	['id_doc_type' => '8','code' => 'CPP','name' => 'CARTA PORT', 'is_deleted' => '0'],
          	['id_doc_type' => '9','code' => 'NCP','name' => 'NOTA', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('erps_doc_status', function (blueprint $table) {
          	$table->increments('id_doc_status');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_doc_status')->insert([
          	['id_doc_status' => '1','code' => 'SUR','name' => 'SURTIDO', 'is_deleted' => '0'],
          	['id_doc_category' => '2','code' => 'PEN','name' => 'PENDIENTE', 'is_deleted' => '0'],
          	['id_doc_category' => '3','code' => 'BLO','name' => 'BLOQUEADO', 'is_deleted' => '0'],
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

          Schema::connection($this->sConnection)->drop('erps_doc_status');
          Schema::connection($this->sConnection)->drop('erps_doc_types');
          Schema::connection($this->sConnection)->drop('erps_doc_classes');
          Schema::connection($this->sConnection)->drop('erps_doc_categories');
        }
    }
}
