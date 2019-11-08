<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddDocsClosedfield extends Migration
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
                $table->boolean('is_closed')->default(false)->after('body_id');
                $table->integer('closed_by_id')->default(1)->after('signature_coding_id')->unsigned();
                $table->datetime('closed_at')->after('updated_by_id');

                $table->foreign('closed_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            });

            DB::connection($this->sConnection)->table('erps_signature_types')->insert([	
                ['id_signature_type' => '5','code' => 'CLOSE','type_name' => 'Cerrar papeletas',
                    'description'=>'Firma para cerrar papeletas y ya no permitir la captura de resultados'],
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

            Schema::connection($this->sConnection)->table('qms_quality_documents', function($table)
            {
                $table->dropForeign(['closed_by_id']);

                $table->dropColumn('closed_by_id');
                $table->dropColumn('is_closed');
                $table->dropColumn('closed_at');
            });

            DB::connection($this->sConnection)->table('erps_signature_types')->where('id_signature_type', '5')->delete();
        }
    }
}
