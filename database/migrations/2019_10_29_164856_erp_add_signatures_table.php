<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddSignaturesTable extends Migration
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

            Schema::connection($this->sConnection)->create('erps_signature_types', function (blueprint $table) {	
                $table->increments('id_signature_type');
                $table->char('code', 5);
                $table->char('type_name', 25);
                $table->char('description', 80);
            });	
                
            DB::connection($this->sConnection)->table('erps_signature_types')->insert([	
                ['id_signature_type' => '1','code' => 'SF','type_name' => 'Sin firma','description'=>'NO FIRMADO/NO APLICA'],
                ['id_signature_type' => '2','code' => 'ARGOX','type_name' => 'Etiqueta Argox','description'=>''],
                ['id_signature_type' => '3','code' => 'CODE','type_name' => 'Etiqueta Codificación','description'=>''],
                ['id_signature_type' => '4','code' => 'MB','type_name' => 'Análisis Microbiológicos','description'=>''],
            ]);	

            Schema::connection($this->sConnection)->create('erp_signatures', function (blueprint $table) {	
                $table->bigIncrements('id_signature');
                $table->boolean('signed');
                $table->boolean('is_deleted');
                $table->integer('signature_type_id')->unsigned();
                $table->integer('signed_by_id')->unsigned();
                $table->timestamps();
                
                $table->foreign('signature_type_id')->references('id_signature_type')->on('erps_signature_types')->onDelete('cascade');
                $table->foreign('signed_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            });	
                
            DB::connection($this->sConnection)->table('erp_signatures')->insert([	
                ['id_signature' => '1','signed' => '0','signature_type_id' => '1', 'is_deleted' => '0','signed_by_id'=>'1'],
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

            Schema::connection($this->sConnection)->drop('erp_signatures');
            Schema::connection($this->sConnection)->drop('erps_signature_types');
        }
    }
}
