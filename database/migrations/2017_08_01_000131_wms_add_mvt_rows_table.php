<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddMvtRowsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wms_mvt_rows', function (blueprint $table) {
          	$table->bigIncrements('id_mvt_row');
          	$table->decimal('quantity', 23,8);
          	$table->decimal('amount_unit', 23,8);
          	$table->decimal('amount', 17,2);
          	$table->decimal('length', 23,8);
          	$table->decimal('surface', 23,8);
          	$table->decimal('volume', 23,8);
          	$table->decimal('mass', 23,8);
          	$table->boolean('is_deleted');
          	$table->bigInteger('mvt_id')->unsigned();
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('pallet_id')->unsigned();
          	$table->integer('location_id')->unsigned();
          	$table->bigInteger('doc_order_row_id')->unsigned();
          	$table->bigInteger('doc_invoice_row_id')->unsigned();
          	$table->bigInteger('doc_debit_note_row_id')->unsigned();
          	$table->bigInteger('doc_credit_note_row_id')->unsigned();

          	$table->foreign('mvt_id')->references('id_mvt')->on('wms_mvts')->onDelete('cascade');
          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('pallet_id')->references('id_pallet')->on('wms_pallets')->onDelete('cascade');
          	$table->foreign('location_id')->references('id_whs_location')->on('wmsu_whs_locations')->onDelete('cascade');
            $table->foreign('doc_order_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
            $table->foreign('doc_invoice_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
            $table->foreign('doc_debit_note_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
            $table->foreign('doc_credit_note_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_mvt_rows')->insert([
          	['id_mvt_row' => '1','quantity' => '0','amount_unit' => '0','amount' => '0','length' => '0','surface' => '0','volume' => '0','mass' => '0','is_deleted' => '0','mvt_id' => '1','item_id' => '1','unit_id' => '1','pallet_id' => '1','location_id' => '1','doc_order_row_id' => '1','doc_invoice_row_id' => '1','doc_debit_note_row_id' => '1','doc_credit_note_row_id' => '1',],
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

          Schema::connection($this->sConnection)->drop('wms_mvt_rows');
        }
    }
}
