<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddMvtsTables extends Migration {
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

          Schema::connection($this->sConnection)->create('wmss_mvt_classes', function (blueprint $table) {
          	$table->increments('id_mvt_class');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('wmss_mvt_classes')->insert([
          	['id_mvt_class' => '1','code' => 'E','name' => 'ENTRADA','is_deleted' => '0'],
          	['id_mvt_class' => '2','code' => 'S','name' => 'SALIDA','is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('wmss_mvt_types', function (blueprint $table) {
          	$table->increments('id_mvt_type');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->integer('mvt_class_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('mvt_class_id')->references('id_mvt_class')->on('wmss_mvt_classes')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wmss_mvt_types')->insert([
          	['id_mvt_type' => '1','code' => 'EV','name' => 'ENTRADA VENTA','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '2','code' => 'EC','name' => 'ENTRADA COMPRA','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '3','code' => 'EA','name' => 'ENTRADA AJUSTE','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '4','code' => 'ET','name' => 'ENTRADA TRASPASO','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '5','code' => 'EN','name' => 'ENTRADA CONVERSIÓN','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '6','code' => 'EP','name' => 'ENTRADA PRODUCCIÓN','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '7','code' => 'EG','name' => 'ENTRADA GASTOS','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '8','code' => 'SV','name' => 'SALIDA VENTA','mvt_class_id' => '2','is_deleted' => '0'],
          	['id_mvt_type' => '9','code' => 'SC','name' => 'SALIDA COMPRA','mvt_class_id' => '2','is_deleted' => '0'],
          	['id_mvt_type' => '10','code' => 'SA','name' => 'SALIDA AJUSTE','mvt_class_id' => '2','is_deleted' => '0'],
          	['id_mvt_type' => '11','code' => 'ST','name' => 'SALIDA TRASPASO','mvt_class_id' => '2','is_deleted' => '0'],
          	['id_mvt_type' => '12','code' => 'SN','name' => 'SALIDA CONVERSIÓN','mvt_class_id' => '2','is_deleted' => '0'],
          	['id_mvt_type' => '13','code' => 'SP','name' => 'SALIDA PRODUCCIÓN','mvt_class_id' => '2','is_deleted' => '0'],
          	['id_mvt_type' => '14','code' => 'SG','name' => 'SALIDA GASTOS','mvt_class_id' => '2','is_deleted' => '0'],
          	['id_mvt_type' => '15','code' => 'RTE','name' => 'RECONFIGURACION ENTRADA','mvt_class_id' => '1','is_deleted' => '0'],
          	['id_mvt_type' => '16','code' => 'RTS','name' => 'RECONFIGURACIÓN SALIDA','mvt_class_id' => '2','is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('wmss_mvt_trn_types', function (blueprint $table) {
          	$table->increments('id_mvt_trn_type');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('wmss_mvt_trn_types')->insert([
          	['id_mvt_trn_type' => '1','code' => 'NA','name' => 'N/A','is_deleted' => '0'],
          	['id_mvt_trn_type' => '2','code' => 'SD','name' => 'SURTIDO/DEVOLUCIÓN','is_deleted' => '0'],
          	['id_mvt_trn_type' => '3','code' => 'CAM','name' => 'CAMBIO','is_deleted' => '0'],
          	['id_mvt_trn_type' => '4','code' => 'GAR','name' => 'GARANTÍA','is_deleted' => '0'],
          	['id_mvt_trn_type' => '5','code' => 'CON','name' => 'CONSIGNACIÓN','is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('wmss_mvt_adj_types', function (blueprint $table) {
          	$table->increments('id_mvt_adj_type');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('wmss_mvt_adj_types')->insert([
          	['id_mvt_adj_type' => '1','code' => 'NA','name' => 'N/A','is_deleted' => '0'],
          	['id_mvt_adj_type' => '2','code' => 'IIF','name' => 'INVENTARIO (INICIAL Y FINAL)','is_deleted' => '0'],
          	['id_mvt_adj_type' => '3','code' => 'CPI','name' => 'CORRECCIÓN POR DISCREPANCIA','is_deleted' => '0'],
          	['id_mvt_adj_type' => '4','code' => 'CPE','name' => 'CORRECCIÓN POR EQUIVOCACIÓN','is_deleted' => '0'],
          	['id_mvt_adj_type' => '5','code' => 'DPO','name' => 'DEPOSICIÓN POR OBSOLESCENCIA','is_deleted' => '0'],
          	['id_mvt_adj_type' => '6','code' => 'DPC','name' => 'DEPOSICIÓN POR CADUCIDAD','is_deleted' => '0'],
          	['id_mvt_adj_type' => '7','code' => 'DPD','name' => 'DEPOSICIÓN POR DAÑO','is_deleted' => '0'],
          	['id_mvt_adj_type' => '8','code' => 'MCO','name' => 'MUESTRA COMERCIAL','is_deleted' => '0'],
          	['id_mvt_adj_type' => '9','code' => 'MPR','name' => 'MUESTRA PROMOCIONAL','is_deleted' => '0'],
          	['id_mvt_adj_type' => '10','code' => 'IYD','name' => 'INVESTIGACIÓN Y DESARROLLO','is_deleted' => '0'],
          	['id_mvt_adj_type' => '11','code' => 'LAB','name' => 'LABORATORIO','is_deleted' => '0'],
          	['id_mvt_adj_type' => '12','code' => 'DEG','name' => 'DEGUSTACIÓN','is_deleted' => '0'],
          	['id_mvt_adj_type' => '13','code' => 'DON','name' => 'DONACIÓN','is_deleted' => '0'],
          	['id_mvt_adj_type' => '14','code' => 'OTR','name' => 'OTROS','is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('wmss_mvt_mfg_types', function (blueprint $table) {
          	$table->increments('id_mvt_mfg_type');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('wmss_mvt_mfg_types')->insert([
          	['id_mvt_mfg_type' => '1','code' => 'NA','name' => 'N/A','is_deleted' => '0'],
          	['id_mvt_mfg_type' => '2','code' => 'MAT','name' => 'MATERIALES','is_deleted' => '0'],
          	['id_mvt_mfg_type' => '3','code' => 'PRO','name' => 'PRODUCTO','is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('wmss_mvt_exp_types', function (blueprint $table) {
          	$table->increments('id_mvt_exp_type');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('wmss_mvt_exp_types')->insert([
          	['id_mvt_exp_type' => '1','code' => 'NA','name' => 'N/A','is_deleted' => '0'],
          	['id_mvt_exp_type' => '2','code' => 'C','name' => 'COMPRAS','is_deleted' => '0'],
          	['id_mvt_exp_type' => '3','code' => 'P','name' => 'PRODUCCIÓN','is_deleted' => '0'],
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

          Schema::connection($this->sConnection)->drop('wmss_mvt_exp_types');
          Schema::connection($this->sConnection)->drop('wmss_mvt_mfg_types');
          Schema::connection($this->sConnection)->drop('wmss_mvt_adj_types');
          Schema::connection($this->sConnection)->drop('wmss_mvt_trn_types');
          Schema::connection($this->sConnection)->drop('wmss_mvt_types');
          Schema::connection($this->sConnection)->drop('wmss_mvt_classes');

        }
    }
}
