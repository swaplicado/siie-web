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
          	$table->integer('doc_category_id')->unsigned();
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_doc_classes')->insert([
          	['id_doc_class' => '1','code' => 'CC','name' => 'COTIZACIÓN CPA.','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_class' => '2','code' => 'PC','name' => 'PEDIDO CPA.','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_class' => '3','code' => 'DC','name' => 'DOCUMENTO CPA.','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_class' => '4','code' => 'TC','name' => 'TRASLADO CPA.','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_class' => '5','code' => 'AC','name' => 'AJUSTE CPA.','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_class' => '6','code' => 'CV','name' => 'COTIZACIÓN VTA.','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_class' => '7','code' => 'PV','name' => 'PEDIDO VTA.','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_class' => '8','code' => 'DV','name' => 'DOCUMENTO VTA.','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_class' => '9','code' => 'TV','name' => 'TRASLADO VTA.','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_class' => '10','code' => 'AV','name' => 'AJUSTE VTA.','doc_category_id' => '2', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('erps_doc_types', function (blueprint $table) {
          	$table->increments('id_doc_type');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->integer('doc_class_id')->unsigned();
          	$table->integer('doc_category_id')->unsigned();
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_doc_types')->insert([
          	['id_doc_type' => '1','code' => 'COTP','name' => 'COTIZACIÓN PRV.','doc_class_id' => '1','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '2','code' => 'CONP','name' => 'CONTRATO PRV.','doc_class_id' => '1','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '3','code' => 'PEDP','name' => 'PEDIDO PRV.','doc_class_id' => '2','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '4','code' => 'FACP','name' => 'FACTURA PRV.','doc_class_id' => '3','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '5','code' => 'REMP','name' => 'REMISIÓN PRV.','doc_class_id' => '3','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '6','code' => 'NVP','name' => 'NOTA VENTA PRV.','doc_class_id' => '3','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '7','code' => 'TICP','name' => 'TICKET PRV.','doc_class_id' => '3','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '8','code' => 'CPP','name' => 'CARTA PORTE PRV.','doc_class_id' => '4','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '9','code' => 'NCP','name' => 'NOTA CRÉDITO PRV.','doc_class_id' => '5','doc_category_id' => '1', 'is_deleted' => '0'],
          	['id_doc_type' => '10','code' => 'COTC','name' => 'COTIZACIÓN CTE.','doc_class_id' => '6','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '11','code' => 'CONC','name' => 'CONTRATO CTE.','doc_class_id' => '6','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '12','code' => 'PEDC','name' => 'PEDIDO CTE.','doc_class_id' => '7','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '13','code' => 'FACC','name' => 'FACTURA CTE.','doc_class_id' => '8','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '14','code' => 'REMC','name' => 'REMISIÓN CTE.','doc_class_id' => '8','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '15','code' => 'NVC','name' => 'NOTA VENTA CTE.','doc_class_id' => '8','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '16','code' => 'TICC','name' => 'TICKET CTE.','doc_class_id' => '8','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '17','code' => 'CPC','name' => 'CARTA PORTE CTE.','doc_class_id' => '9','doc_category_id' => '2', 'is_deleted' => '0'],
          	['id_doc_type' => '18','code' => 'NCC','name' => 'NOTA CRÉDITO CTE.','doc_class_id' => '10','doc_category_id' => '2', 'is_deleted' => '0'],
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
