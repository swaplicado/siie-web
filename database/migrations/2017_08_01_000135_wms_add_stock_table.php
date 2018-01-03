<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddStockTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wms_stock', function (blueprint $table) {
          	$table->bigIncrements('id_stock');
          	$table->date('dt_date');
          	$table->decimal('input', 23,8);
          	$table->decimal('output', 23,8);
          	$table->decimal('cost_unit', 23,8);
          	$table->decimal('debit', 17,2);
          	$table->decimal('credit', 17,2);
          	$table->boolean('is_deleted');
          	$table->integer('mvt_whs_class_id')->unsigned();
          	$table->integer('mvt_whs_type_id')->unsigned();
          	$table->integer('mvt_trn_type_id')->unsigned();
          	$table->integer('mvt_adj_type_id')->unsigned();
          	$table->integer('mvt_mfg_type_id')->unsigned();
          	$table->integer('mvt_exp_type_id')->unsigned();
          	$table->integer('branch_id')->unsigned();
          	$table->integer('whs_id')->unsigned();
          	$table->integer('location_id')->unsigned();
          	$table->bigInteger('mvt_id')->unsigned();
          	$table->bigInteger('mvt_row_id')->unsigned();
          	$table->bigInteger('mvt_row_lot_id')->unsigned();
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('lot_id')->unsigned();
          	$table->integer('pallet_id')->unsigned();
          	$table->bigInteger('doc_order_row_id')->unsigned();
          	$table->bigInteger('doc_invoice_row_id')->unsigned();
          	$table->bigInteger('doc_debit_note_row_id')->unsigned();
          	$table->bigInteger('doc_credit_note_row_id')->unsigned();
          	$table->integer('mfg_dept_id')->unsigned();
          	$table->integer('mfg_line_id')->unsigned();
          	$table->integer('mfg_job_id')->unsigned();

          	$table->foreign('mvt_whs_class_id')->references('id_mvt_class')->on('wmss_mvt_classes')->onDelete('cascade');
          	$table->foreign('mvt_whs_type_id')->references('id_mvt_type')->on('wmss_mvt_types')->onDelete('cascade');
          	$table->foreign('mvt_trn_type_id')->references('id_mvt_trn_type')->on('wmss_mvt_trn_types')->onDelete('cascade');
          	$table->foreign('mvt_adj_type_id')->references('id_mvt_adj_type')->on('wmss_mvt_adj_types')->onDelete('cascade');
          	$table->foreign('mvt_mfg_type_id')->references('id_mvt_mfg_type')->on('wmss_mvt_mfg_types')->onDelete('cascade');
          	$table->foreign('mvt_exp_type_id')->references('id_mvt_exp_type')->on('wmss_mvt_exp_types')->onDelete('cascade');
          	$table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
          	$table->foreign('whs_id')->references('id_whs')->on('wmsu_whs')->onDelete('cascade');
          	$table->foreign('location_id')->references('id_whs_location')->on('wmsu_whs_locations')->onDelete('cascade');
          	$table->foreign('mvt_id')->references('id_mvt')->on('wms_mvts')->onDelete('cascade');
          	$table->foreign('mvt_row_id')->references('id_mvt_row')->on('wms_mvt_rows')->onDelete('cascade');
          	$table->foreign('mvt_row_lot_id')->references('id_mvt_row_lot')->on('wms_mvt_row_lots')->onDelete('cascade');
          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('lot_id')->references('id_lot')->on('wms_lots')->onDelete('cascade');
          	$table->foreign('pallet_id')->references('id_pallet')->on('wms_pallets')->onDelete('cascade');
            $table->foreign('doc_order_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
            $table->foreign('doc_invoice_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
            $table->foreign('doc_debit_note_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
            $table->foreign('doc_credit_note_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_stock')->insert([
            ['id_stock' => '1','dt_date' => '2017-01-01','input' => '0','output' => '0','cost_unit' => '0',
            'debit' => '0','credit' => '0','is_deleted' => '0','mvt_whs_class_id' => '1','mvt_whs_type_id' => '1',
            'mvt_trn_type_id' => '1','mvt_adj_type_id' => '1','mvt_mfg_type_id' => '1','mvt_exp_type_id' => '1',
            'branch_id' => '1','whs_id' => '1','location_id' => '1','mvt_id' => '1','mvt_row_id' => '1','mvt_row_lot_id' => '1',
            'item_id' => '1','unit_id' => '1','lot_id' => '1','pallet_id' => '1','doc_order_row_id' => '1','doc_invoice_row_id' => '1',
            'doc_debit_note_row_id' => '1','doc_credit_note_row_id' => '1','mfg_dept_id' => '1','mfg_line_id' => '1','mfg_job_id' => '1'],
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
