<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddMvtsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('wms_mvts', function (blueprint $table) {
          	$table->bigIncrements('id_mvt');
          	$table->date('dt_date');
          	$table->integer('folio');
          	$table->decimal('total_amount', 17,2);
          	$table->decimal('total_length', 23,8);
          	$table->decimal('total_surface', 23,8);
          	$table->decimal('total_volume', 23,8);
          	$table->decimal('total_mass', 23,8);
          	$table->boolean('is_closed_shipment');
          	$table->boolean('is_deleted');
          	$table->integer('mvt_whs_class_id')->unsigned();
          	$table->integer('mvt_whs_type_id')->unsigned();
          	$table->integer('mvt_trn_type_id')->unsigned();
          	$table->integer('mvt_adj_type_id')->unsigned();
          	$table->integer('mvt_mfg_type_id')->unsigned();
          	$table->integer('mvt_exp_type_id')->unsigned();
          	$table->integer('branch_id')->unsigned();
          	$table->integer('whs_id')->unsigned();
            $table->integer('year_id')->unsigned();
          	$table->integer('auth_status_id')->unsigned();
          	$table->bigInteger('src_mvt_id')->unsigned();
          	$table->bigInteger('doc_order_id')->unsigned();
          	$table->bigInteger('doc_invoice_id')->unsigned();
          	$table->bigInteger('doc_debit_note_id')->unsigned();
          	$table->bigInteger('doc_credit_note_id')->unsigned();
          	$table->integer('mfg_dept_id')->unsigned();
          	$table->integer('mfg_line_id')->unsigned();
          	$table->integer('mfg_job_id')->unsigned();
          	$table->integer('auth_status_by_id')->unsigned();
          	$table->integer('closed_shipment_by_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->datetime('ts_auth_status');
          	$table->datetime('ts_closed_shipment');
          	$table->timestamps();

          	$table->foreign('mvt_whs_class_id')->references('id_mvt_class')->on('wmss_mvt_classes')->onDelete('cascade');
          	$table->foreign('mvt_whs_type_id')->references('id_mvt_type')->on('wmss_mvt_types')->onDelete('cascade');
          	$table->foreign('mvt_trn_type_id')->references('id_mvt_trn_type')->on('wmss_mvt_trn_types')->onDelete('cascade');
          	$table->foreign('mvt_adj_type_id')->references('id_mvt_adj_type')->on('wmss_mvt_adj_types')->onDelete('cascade');
          	$table->foreign('mvt_mfg_type_id')->references('id_mvt_mfg_type')->on('wmss_mvt_mfg_types')->onDelete('cascade');
          	$table->foreign('mvt_exp_type_id')->references('id_mvt_exp_type')->on('wmss_mvt_exp_types')->onDelete('cascade');
          	$table->foreign('branch_id')->references('id_branch')->on('erpu_branches')->onDelete('cascade');
          	$table->foreign('whs_id')->references('id_whs')->on('wmsu_whs')->onDelete('cascade');
            $table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          	$table->foreign('auth_status_id')->references('id_auth_status')->on('erps_auth_status')->onDelete('cascade');
          	$table->foreign('src_mvt_id')->references('id_mvt')->on('wms_mvts')->onDelete('cascade');
          	$table->foreign('doc_order_id')->references('id_document')->on('erpu_documents')->onDelete('cascade');
          	$table->foreign('doc_invoice_id')->references('id_document')->on('erpu_documents')->onDelete('cascade');
          	$table->foreign('doc_debit_note_id')->references('id_document')->on('erpu_documents')->onDelete('cascade');
          	$table->foreign('doc_credit_note_id')->references('id_document')->on('erpu_documents')->onDelete('cascade');
          	$table->foreign('auth_status_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('closed_shipment_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('wms_mvts')->insert([
            ['id_mvt' => '1','dt_date' => '2017-01-01','folio' => 'N/A','total_amount' => '0',
            'total_length' => '0','total_surface' => '0','total_volume' => '0','total_mass' => '0',
            'is_closed_shipment' => '1','is_deleted' => '1','mvt_whs_class_id' => '1',
            'mvt_whs_type_id' => '1','mvt_trn_type_id' => '1','mvt_adj_type_id' => '1',
            'mvt_mfg_type_id' => '1','mvt_exp_type_id' => '1','branch_id' => '1','whs_id' => '1',
            'year_id' => '1','auth_status_id' => '1','src_mvt_id' => '1','doc_order_id' => '1',
            'doc_invoice_id' => '1','doc_debit_note_id' => '1','doc_credit_note_id' => '1',
            'mfg_dept_id' => '1','mfg_line_id' => '1','mfg_job_id' => '1','auth_status_by_id' => '1',
            'closed_shipment_by_id' => '1','created_by_id' => '1','updated_by_id' => '1',
            'ts_auth_status' => '2017-01-01','ts_closed_shipment' => '2017-01-01'],
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

          Schema::connection($this->sConnection)->drop('wms_mvts');
        }
    }
}
