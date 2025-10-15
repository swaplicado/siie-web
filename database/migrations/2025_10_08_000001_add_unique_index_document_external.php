<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\ERP\SDocumentRow;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class AddUniqueIndexDocumentExternal extends Migration
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

    public function up()
    {
        $tableName = (new SDocumentRow())->getTable();
        foreach ($this->lDatabases as $base) {
            $this->sDataBase = $base;
            SConnectionUtils::reconnectDataBase(
                $this->sConnection,
                $this->bDefault,
                $this->sHost,
                $this->sDataBase,
                $this->sUser,
                $this->sPassword
            );

            Schema::connection($this->sConnection)->table($tableName, function (Blueprint $table) use ($tableName) {
                // crea índice único para prevenir duplicados por (document_id, external_id)
                $table->unique(['document_id', 'external_id'], 'document_external_unique');
            });
        }
    }

    public function down()
    {
        $tableName = (new SDocumentRow())->getTable();
        foreach ($this->lDatabases as $base) {
            $this->sDataBase = $base;
            SConnectionUtils::reconnectDataBase(
                $this->sConnection,
                $this->bDefault,
                $this->sHost,
                $this->sDataBase,
                $this->sUser,
                $this->sPassword
            );

            Schema::connection($this->sConnection)->table($tableName, function ($table) {
                $table->dropUnique('document_external_unique');
            });
        }
    }
}