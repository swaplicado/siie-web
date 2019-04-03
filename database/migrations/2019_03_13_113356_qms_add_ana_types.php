<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddAnaTypes extends Migration
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

            Schema::connection($this->sConnection)->create('qmss_analysis_types', function (blueprint $table) {	
                $table->increments('id_analysis_type');
                $table->char('code', 3);
                $table->char('name', 50);
                $table->integer('order');
                $table->boolean('is_deleted');
                $table->timestamps();
            });	
                
            DB::connection($this->sConnection)->table('qmss_analysis_types')->insert([	
                ['id_analysis_type' => '1','code' => 'FQ','name' => 'FISICO QUÍMICO', 'is_deleted' => '0','order'=>'1'],
                ['id_analysis_type' => '2','code' => 'MB','name' => 'MICROBIOLÓGICO', 'is_deleted' => '0','order'=>'3'],
                ['id_analysis_type' => '3','code' => 'OL','name' => 'ORGANOLÉPTICO', 'is_deleted' => '0','order'=>'2'],
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
            SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                        $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

            Schema::connection($this->sConnection)->drop('qmss_analysis_types');
        }
    }
}
