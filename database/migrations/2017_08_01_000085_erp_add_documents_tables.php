<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddDocumentsTables extends Migration {
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

          Schema::connection($this->sConnection)->create('erpu_documents', function (blueprint $table) {
          	$table->bigIncrements('id_document');
          	$table->date('dt_date');
          	$table->date('dt_doc');
          	$table->char('num', 15);
          	$table->decimal('subtotal', 17,2);
          	$table->decimal('tax_charged', 17,2);
          	$table->decimal('tax_retained', 17,2);
          	$table->decimal('total', 17,2);
          	$table->decimal('exchange_rate', 17,2);
          	$table->decimal('exchange_rate_sys', 17,2);
          	$table->decimal('subtotal_cur', 17,2);
          	$table->decimal('tax_charged_cur', 17,2);
          	$table->decimal('tax_retained_cur', 17,2);
          	$table->decimal('total_cur', 17,2);
          	$table->boolean('is_closed');
          	$table->boolean('is_deleted');
          	$table->integer('external_id')->unsigned();
          	$table->integer('year_id')->unsigned();
          	$table->integer('doc_category_id')->unsigned();
          	$table->integer('doc_class_id')->unsigned();
          	$table->integer('doc_type_id')->unsigned();
          	$table->integer('doc_status_id')->unsigned();
          	$table->bigInteger('doc_src_id')->unsigned();
          	$table->integer('currency_id')->unsigned();
          	$table->integer('partner_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          	$table->foreign('doc_category_id')->references('id_doc_category')->on('erps_doc_categories')->onDelete('cascade');
          	$table->foreign('doc_class_id')->references('id_doc_class')->on('erps_doc_classes')->onDelete('cascade');
          	$table->foreign('doc_type_id')->references('id_doc_type')->on('erps_doc_types')->onDelete('cascade');
          	$table->foreign('doc_status_id')->references('id_doc_status')->on('erps_doc_status')->onDelete('cascade');
            $table->foreign('doc_src_id')->references('id_document')->on('erpu_documents')->onDelete('cascade');
          	$table->foreign('currency_id')->references('id_currency')->on('erps_currencies')->onDelete('cascade');
          	$table->foreign('partner_id')->references('id_partner')->on('erpu_partners')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_documents')->insert([
          	['id_document' => '1','dt_date' => '2017-01-01','dt_doc' => '2017-01-01',
            'num' => 'NA','subtotal' => '0','tax_charged' => '0','tax_retained' => '0',
            'total' => '0','exchange_rate' => '0','exchange_rate_sys' => '0',
            'subtotal_cur' => '0','tax_charged_cur' => '0','tax_retained_cur' => '0',
            'total_cur' => '0','is_closed' => '0','is_deleted' => '0','external_id' => '0',
            'year_id' => '1','doc_category_id' => '1','doc_class_id' => '1',
            'doc_type_id' => '1', 'doc_status_id' => '1', 'doc_src_id' => '1',
            'currency_id' => '1', 'partner_id' => '1', 'created_by_id' => '1',
            'updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('erpu_document_rows', function (blueprint $table) {
          	$table->bigIncrements('id_document_row');
          	$table->char('concept_key', 35);
          	$table->char('concept', 130);
          	$table->char('reference', 25);
          	$table->decimal('quantity', 23,8);
          	$table->decimal('price_unit', 17,2);
          	$table->decimal('price_unit_sys', 17,2);
          	$table->decimal('subtotal', 17,2);
          	$table->decimal('tax_charged', 17,2);
          	$table->decimal('tax_retained', 17,2);
          	$table->decimal('total', 17,2);
          	$table->decimal('price_unit_cur', 17,2);
          	$table->decimal('price_unit_sys_cur', 17,2);
          	$table->decimal('subtotal_cur', 17,2);
          	$table->decimal('tax_charged_cur', 17,2);
          	$table->decimal('tax_retained_cur', 17,2);
          	$table->decimal('total_cur', 17,2);
          	$table->decimal('length', 23,8);
          	$table->decimal('surface', 23,8);
          	$table->decimal('volume', 23,8);
          	$table->decimal('mass', 23,8);
          	$table->boolean('is_inventory');
          	$table->boolean('is_deleted');
          	$table->integer('external_id')->unsigned();
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('year_id')->unsigned();
          	$table->bigInteger('document_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          	$table->foreign('document_id')->references('id_document')->on('erpu_documents')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_document_rows')->insert([
          	['id_document_row' => '1','concept_key' => 'NA','concept' => 'NA',
            'reference' => 'NA','quantity' => '0','price_unit' => '0',
            'price_unit_sys' => '0','subtotal' => '0','tax_charged' => '0',
            'tax_retained' => '0','total' => '0','price_unit_cur' => '0',
            'price_unit_sys_cur' => '0','subtotal_cur' => '0','tax_charged_cur' => '0',
            'tax_retained_cur' => '0','total_cur' => '0','length' => '0','surface' => '0',
            'volume' => '0','mass' => '0','is_inventory' => '0','is_deleted' => '1',
            'external_id' => '0','item_id' => '1','unit_id' => '1','year_id' => '1',
            'document_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('erpu_doc_row_taxes', function (blueprint $table) {
          	$table->bigIncrements('id_row_tax');
          	$table->decimal('percentage', 17,2);
          	$table->decimal('value_unit', 17,2);
          	$table->decimal('value', 17,2);
          	$table->decimal('tax', 17,2);
          	$table->decimal('tax_currency', 17,2);
          	$table->integer('external_id')->unsigned();
          	$table->bigInteger('document_row_id')->unsigned();
          	$table->bigInteger('document_id')->unsigned();
          	$table->integer('year_id')->unsigned();

          	$table->foreign('document_row_id')->references('id_document_row')->on('erpu_document_rows')->onDelete('cascade');
          	$table->foreign('document_id')->references('id_document')->on('erpu_documents')->onDelete('cascade');
          	$table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_doc_row_taxes')->insert([
          	['id_row_tax' => '1','percentage' => '0','value_unit' => '0',
            'value' => '0','tax' => '0','tax_currency' => '0','external_id' => '0',
            'document_row_id' => '1','document_id' => '1','year_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erpu_doc_row_taxes');
          Schema::connection($this->sConnection)->drop('erpu_document_rows');
          Schema::connection($this->sConnection)->drop('erpu_documents');
        }
    }
}
