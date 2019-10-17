<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddDocumentsTables extends Migration
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                                                $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        Schema::connection($this->sConnection)->create('qms_quality_documents', function (blueprint $table) {
            $table->increments('id_document');
            $table->char('title', 50);
            $table->date('dt_document');
            $table->char('body_id', 250);
            $table->boolean('is_deleted');
            $table->integer('lot_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->integer('father_po_id')->unsigned();
            $table->integer('son_po_id')->unsigned();
            $table->integer('sup_quality_id')->unsigned();
            $table->integer('sup_process_id')->unsigned();
            $table->integer('sup_production_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('lot_id')->references('id_lot')->on('wms_lots')->onDelete('cascade');
            $table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
            $table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
            $table->foreign('father_po_id')->references('id_order')->on('mms_production_orders')->onDelete('cascade');
            $table->foreign('son_po_id')->references('id_order')->on('mms_production_orders')->onDelete('cascade');
            $table->foreign('sup_quality_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('sup_process_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('sup_production_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
        });

        Schema::connection($this->sConnection)->create('qms_doc_sections', function (blueprint $table) {
            $table->increments('id_section');
            $table->char('title', 200);
            $table->date('dt_section');
            $table->char('comments', 250);
            $table->integer('order');
            $table->boolean('is_deleted');
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
        });

        Schema::connection($this->sConnection)->create('qmss_element_types', function (blueprint $table) {
            $table->increments('id_element_type');
            $table->char('element_type', 150);
            $table->char('table_name', 200);
            $table->boolean('is_table');
            $table->boolean('is_deleted');
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
        });

        DB::connection($this->sConnection)->table('qmss_element_types')->insert([	
          ['id_element_type' => '1','element_type' => 'texto','table_name'=>'','is_table'=>'0','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
          ['id_element_type' => '2','element_type' => 'decimal','table_name'=>'','is_table'=>'0','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
          ['id_element_type' => '3','element_type' => 'entero','table_name'=>'','is_table'=>'0','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
          ['id_element_type' => '4','element_type' => 'fecha','table_name'=>'','is_table'=>'0','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
          ['id_element_type' => '5','element_type' => 'usuario','table_name'=>'users','is_table'=>'1','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
          ['id_element_type' => '6','element_type' => 'análisis','table_name'=>'qms_analysis','is_table'=>'1','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
          ['id_element_type' => '7','element_type' => 'booleano','table_name'=>'','is_table'=>'0','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
          ['id_element_type' => '8','element_type' => 'archivo','table_name'=>'','is_table'=>'0','is_deleted' => '0','created_by_id' => '1','updated_by_id' => '1'],
        ]);

        Schema::connection($this->sConnection)->create('qms_doc_elements', function (blueprint $table) {	
          $table->increments('id_element');
          $table->char('element', 150);
          $table->integer('n_values')->unsigned();
          $table->integer('analysis_id')->unsigned();
          $table->boolean('is_deleted');
          $table->integer('element_type_id')->unsigned();
          $table->integer('created_by_id')->unsigned();
          $table->integer('updated_by_id')->unsigned();
          $table->timestamps();
          
          $table->foreign('element_type_id')->references('id_element_type')->on('qmss_element_types')->onDelete('cascade');
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                    $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);
                    
        Schema::connection($this->sConnection)->drop('qms_doc_elements');
        Schema::connection($this->sConnection)->drop('qmss_element_types');
        Schema::connection($this->sConnection)->drop('qms_doc_sections');
        Schema::connection($this->sConnection)->drop('qms_quality_documents');
      }
    }
}
