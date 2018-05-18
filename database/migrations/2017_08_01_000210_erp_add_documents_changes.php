<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddDocumentsChanges extends Migration {
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

          Schema::connection($this->sConnection)->table('erpu_documents', function (blueprint $table) {
          	$table->integer('address_id')->unsigned()->default('1')->after('branch_id');
          	$table->integer('billing_branch_id')->unsigned()->default('1')->after('year_id');

          	$table->foreign('address_id')
                    ->references('id_branch_address')
                    ->on('erpu_branch_addresses')
                    ->onDelete('cascade');
          	$table->foreign('billing_branch_id')
                    ->references('id_branch_address')
                    ->on('erpu_branch_addresses')
                    ->onDelete('cascade');
          });

          Schema::connection($this->sConnection)->table('erpu_branch_addresses', function (blueprint $table) {
          	$table->integer('external_ad_id')->default('1')->after('external_id');
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

          Schema::connection($this->sConnection)->table('erpu_documents', function($table)
          {
              $table->dropForeign('erpu_documents_billing_branch_id_foreign');
              $table->dropColumn('billing_branch_id');
              $table->dropForeign('erpu_documents_address_id_foreign');
              $table->dropColumn('address_id');
          });

          Schema::connection($this->sConnection)->table('erpu_branch_addresses', function($table)
          {
              $table->dropColumn('external_ad_id');
          });
        }
    }
}
