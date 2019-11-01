<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddSignField extends Migration
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
            SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

            Schema::connection($this->sConnection)->table('qms_quality_documents', function (blueprint $table) {
                $table->bigInteger('signature_argox_id')->unsigned()->default('1')->after('sup_production_id');
                $table->bigInteger('signature_coding_id')->unsigned()->default('1')->after('signature_argox_id');
                $table->bigInteger('signature_mb_id')->unsigned()->default('1')->after('signature_argox_id');

                $table->foreign('signature_argox_id')->references('id_signature')->on('erp_signatures')->onDelete('cascade');
                $table->foreign('signature_coding_id')->references('id_signature')->on('erp_signatures')->onDelete('cascade');
                $table->foreign('signature_mb_id')->references('id_signature')->on('erp_signatures')->onDelete('cascade');
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

            Schema::connection($this->sConnection)->table('qms_quality_documents', function($table)
            {
                $table->dropForeign(['signature_argox_id']);
                $table->dropForeign(['signature_coding_id']);
                $table->dropForeign(['signature_mb_id']);

                $table->dropColumn('signature_argox_id');
                $table->dropColumn('signature_coding_id');
                $table->dropColumn('signature_mb_id');
            });
        }
    }
}
