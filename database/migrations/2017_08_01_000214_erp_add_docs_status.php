<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddDocsStatus extends Migration {
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

          Schema::connection($this->sConnection)->create('erps_doc_sys_status', function (blueprint $table) {
            	$table->increments('id_doc_status');
            	$table->char('code', 2)->unique();
            	$table->char('name', 100);
            	$table->boolean('is_deleted');
            	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_doc_sys_status')->insert([
            	['id_doc_status' => '1','code' => 'N','name' => 'NUEVO', 'is_deleted' => '0'],
            	['id_doc_status' => '2','code' => 'E','name' => 'EMITIDO', 'is_deleted' => '0'],
            	['id_doc_status' => '3','code' => 'A','name' => 'ANULADO', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->table('erpu_documents', function ($table) {
              $table->integer('doc_sys_status_id')->unsigned()->default(2)->after('doc_status_id');

              $table->foreign('doc_sys_status_id')->references('id_doc_status')->on('erps_doc_sys_status')->onDelete('cascade');
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
          SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

          Schema::connection($this->sConnection)->table('erpu_documents', function ($table) {
              $table->dropForeign(['doc_sys_status_id']);

              $table->dropColumn('doc_sys_status_id');
          });

          Schema::connection($this->sConnection)->drop('erps_doc_sys_status');
        }
    }
}
