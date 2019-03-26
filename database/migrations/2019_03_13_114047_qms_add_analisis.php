<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddAnalisis extends Migration
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
    
            Schema::connection($this->sConnection)->create('qms_analysis', function (blueprint $table) {	
                $table->increments('id_analysis');
                $table->char('code', 5);
                $table->char('name', 50);
                $table->char('standard', 100);
                $table->decimal('min_value', 15,6);
                $table->decimal('max_value', 15,6);
                $table->char('result_unit', 20);
                $table->char('specification', 150);
                $table->integer('order_num');
                $table->boolean('is_deleted');
                $table->integer('type_id')->unsigned();
                $table->integer('created_by_id')->unsigned();
                $table->integer('updated_by_id')->unsigned();
                $table->timestamps();
                
                $table->foreign('type_id')->references('id_analysis_type')->on('qmss_analysis_types')->onDelete('cascade');
                $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
                $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            });	
                
            DB::connection($this->sConnection)->table('qms_analysis')->insert([	
                ['id_analysis' => '1','code' => 'MESOF','name' => 'MESOFILOS AEROBIOS','standard' => 'NOM-092-SSA1-1994','min_value' => '0','result_unit' => 'UFC/g','max_value' => '0','is_deleted' => '0','type_id' => '2','created_by_id' => '1','updated_by_id' => '1','specification' => '500 UFC/g','order_num' => '1'],
                ['id_analysis' => '2','code' => 'COLIF','name' => 'COLIFORMES TOTALES','standard' => 'NOM-113-SSA1-1994','min_value' => '0','result_unit' => 'UFC/g','max_value' => '0','is_deleted' => '0','type_id' => '2','created_by_id' => '1','updated_by_id' => '1','specification' => '<10 UFC/g','order_num' => '2'],
                ['id_analysis' => '3','code' => 'MOHOS','name' => 'MOHOS','standard' => 'NOM-111-SSA1-1994','min_value' => '0','result_unit' => 'UFC/g','max_value' => '0','is_deleted' => '0','type_id' => '2','created_by_id' => '1','updated_by_id' => '1','specification' => '<20 UFC/g','order_num' => '3'],
                ['id_analysis' => '4','code' => 'LEVAD','name' => 'LEVADURAS','standard' => 'NOM-111-SSA1-1994','min_value' => '0','result_unit' => 'UFC/g','max_value' => '0','is_deleted' => '0','type_id' => '2','created_by_id' => '1','updated_by_id' => '1','specification' => '<20 UFC/g','order_num' => '4'],
                ['id_analysis' => '5','code' => 'ACIDZ','name' => 'ACIDEZ (%)','standard' => 'AOCS MET. 935.57','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => '0.85-1.3','order_num' => '5'],
                ['id_analysis' => '6','code' => 'CLORU','name' => 'CLORUROS (%)','standard' => 'NMX-F-150-SCFI-1981','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => '1.7-2.2','order_num' => '6'],
                ['id_analysis' => '7','code' => 'HUMED','name' => 'HUMEDAD (%)','standard' => 'NMX-F-083-1986','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => 'Máx 86','order_num' => '7'],
                ['id_analysis' => '8','code' => 'L','name' => 'LAB L:','standard' => 'COLORÍMETRO COLORFLEX 45/0_MET. HUNTER LAB','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => 'L:18-22','order_num' => '8'],
                ['id_analysis' => '9','code' => 'A','name' => 'LAB A:','standard' => 'COLORÍMETRO COLORFLEX 45/0_MET. HUNTER LAB','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => 'a:14-21','order_num' => '9'],
                ['id_analysis' => '10','code' => 'B','name' => 'LAB B:','standard' => 'COLORÍMETRO COLORFLEX 45/0_MET. HUNTER LAB','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => 'b:8-14','order_num' => '10'],
                ['id_analysis' => '11','code' => 'VISCO','name' => 'VISCOSIDAD','standard' => 'VISCOSÍMETRO BROOKFIELD','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => 'Min 65,000','order_num' => '11'],
                ['id_analysis' => '12','code' => 'PH','name' => 'PH','standard' => 'NMX-F-317-2013','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => '<3.9','order_num' => '12'],
                ['id_analysis' => '13','code' => '°BX','name' => '°BX','standard' => 'GS 0008','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => 'Min 14.8','order_num' => '13'],
                ['id_analysis' => '14','code' => 'DENSI','name' => 'DENSIDAD (KG/L)','standard' => 'NMX-F-075-SCFI-2012','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => '','order_num' => '14'],
                ['id_analysis' => '15','code' => 'AGLIB','name' => 'ÁCIDOS GRASOS LIBRES','standard' => 'NMX-F-101-SCFI-2006','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => '','order_num' => '15'],
                ['id_analysis' => '16','code' => 'PEROX','name' => 'INDICE DE PERÓXIDOS','standard' => 'AOCS CD 8-53','min_value' => '0','result_unit' => '','max_value' => '0','is_deleted' => '0','type_id' => '1','created_by_id' => '1','updated_by_id' => '1','specification' => '','order_num' => '16'],
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

            Schema::connection($this->sConnection)->drop('qms_analysis');
        }
    }
}
