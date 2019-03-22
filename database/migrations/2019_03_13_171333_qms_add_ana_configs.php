<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddAnaConfigs extends Migration
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
    
            Schema::connection($this->sConnection)->create('qms_ana_configs', function (blueprint $table) {
                $table->increments('id_config');
                $table->boolean('is_deleted');
                $table->integer('analysis_id')->unsigned();
                $table->integer('item_link_type_id')->unsigned();
                $table->integer('item_link_id')->unsigned();
                $table->decimal('min_value', 15,6);
                $table->decimal('max_value', 15,6);
                $table->integer('created_by_id')->unsigned();
                $table->integer('updated_by_id')->unsigned();
                $table->timestamps();

                $table->unique(['analysis_id', 'item_link_type_id', 'item_link_id'], 'configuration_unique');
                $table->foreign('analysis_id')->references('id_analysis')->on('qms_analysis')->onDelete('cascade');
                $table->foreign('item_link_type_id')->references('id_item_link_type')->on('erps_item_link_types')->onDelete('cascade');
                $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
                $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            });

            DB::connection($this->sConnection)->table('qms_ana_configs')->insert([	
                ['id_config' => '1','is_deleted' => '0','analysis_id' => '1','item_link_type_id' => '4','item_link_id' => '2','min_value' => '3000','max_value' => '3000','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '2','is_deleted' => '0','analysis_id' => '1','item_link_type_id' => '6','item_link_id' => '8','min_value' => '500','max_value' => '500','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '3','is_deleted' => '0','analysis_id' => '1','item_link_type_id' => '6','item_link_id' => '9','min_value' => '500','max_value' => '500','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '4','is_deleted' => '0','analysis_id' => '1','item_link_type_id' => '4','item_link_id' => '5','min_value' => '1000','max_value' => '1000','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '5','is_deleted' => '0','analysis_id' => '1','item_link_type_id' => '4','item_link_id' => '3','min_value' => '500','max_value' => '500','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '6','is_deleted' => '0','analysis_id' => '2','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '7','is_deleted' => '0','analysis_id' => '2','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '8','is_deleted' => '0','analysis_id' => '2','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '9','is_deleted' => '0','analysis_id' => '2','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '10','is_deleted' => '0','analysis_id' => '2','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '11','is_deleted' => '0','analysis_id' => '3','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '20','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '12','is_deleted' => '0','analysis_id' => '3','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '13','is_deleted' => '0','analysis_id' => '3','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '14','is_deleted' => '0','analysis_id' => '3','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '15','is_deleted' => '0','analysis_id' => '3','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '20','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '16','is_deleted' => '0','analysis_id' => '4','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '50','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '17','is_deleted' => '0','analysis_id' => '4','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '18','is_deleted' => '0','analysis_id' => '4','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '19','is_deleted' => '0','analysis_id' => '4','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '10','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '20','is_deleted' => '0','analysis_id' => '4','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '20','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '21','is_deleted' => '0','analysis_id' => '5','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '22','is_deleted' => '0','analysis_id' => '5','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '23','is_deleted' => '0','analysis_id' => '5','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '24','is_deleted' => '0','analysis_id' => '5','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '25','is_deleted' => '0','analysis_id' => '5','item_link_type_id' => '4','item_link_id' => '10','min_value' => '4.85','max_value' => '5.15','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '26','is_deleted' => '0','analysis_id' => '6','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '27','is_deleted' => '0','analysis_id' => '6','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '28','is_deleted' => '0','analysis_id' => '6','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '29','is_deleted' => '0','analysis_id' => '6','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '30','is_deleted' => '0','analysis_id' => '6','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '31','is_deleted' => '0','analysis_id' => '7','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '32','is_deleted' => '0','analysis_id' => '7','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '33','is_deleted' => '0','analysis_id' => '7','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '34','is_deleted' => '0','analysis_id' => '7','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '35','is_deleted' => '0','analysis_id' => '7','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '36','is_deleted' => '0','analysis_id' => '7','item_link_type_id' => '5','item_link_id' => '12','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '37','is_deleted' => '0','analysis_id' => '8','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '38','is_deleted' => '0','analysis_id' => '8','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '39','is_deleted' => '0','analysis_id' => '8','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '40','is_deleted' => '0','analysis_id' => '8','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '41','is_deleted' => '0','analysis_id' => '8','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '42','is_deleted' => '0','analysis_id' => '8','item_link_type_id' => '4','item_link_id' => '4','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '43','is_deleted' => '0','analysis_id' => '9','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '44','is_deleted' => '0','analysis_id' => '9','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '45','is_deleted' => '0','analysis_id' => '9','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '46','is_deleted' => '0','analysis_id' => '9','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '47','is_deleted' => '0','analysis_id' => '9','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '48','is_deleted' => '0','analysis_id' => '9','item_link_type_id' => '4','item_link_id' => '4','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '49','is_deleted' => '0','analysis_id' => '10','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '50','is_deleted' => '0','analysis_id' => '10','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '51','is_deleted' => '0','analysis_id' => '10','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '52','is_deleted' => '0','analysis_id' => '10','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '53','is_deleted' => '0','analysis_id' => '10','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '54','is_deleted' => '0','analysis_id' => '10','item_link_type_id' => '4','item_link_id' => '4','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '55','is_deleted' => '0','analysis_id' => '11','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '56','is_deleted' => '0','analysis_id' => '11','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '57','is_deleted' => '0','analysis_id' => '11','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '58','is_deleted' => '0','analysis_id' => '11','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '59','is_deleted' => '0','analysis_id' => '11','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '60','is_deleted' => '0','analysis_id' => '11','item_link_type_id' => '4','item_link_id' => '4','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '61','is_deleted' => '0','analysis_id' => '12','item_link_type_id' => '4','item_link_id' => '2','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '62','is_deleted' => '0','analysis_id' => '12','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '63','is_deleted' => '0','analysis_id' => '12','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '64','is_deleted' => '0','analysis_id' => '12','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '65','is_deleted' => '0','analysis_id' => '13','item_link_type_id' => '6','item_link_id' => '8','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '66','is_deleted' => '0','analysis_id' => '13','item_link_type_id' => '6','item_link_id' => '9','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '67','is_deleted' => '0','analysis_id' => '13','item_link_type_id' => '4','item_link_id' => '5','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '68','is_deleted' => '0','analysis_id' => '13','item_link_type_id' => '4','item_link_id' => '3','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '69','is_deleted' => '0','analysis_id' => '14','item_link_type_id' => '4','item_link_id' => '10','min_value' => '1.012','max_value' => '1.013','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '70','is_deleted' => '0','analysis_id' => '14','item_link_type_id' => '4','item_link_id' => '4','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '71','is_deleted' => '0','analysis_id' => '15','item_link_type_id' => '4','item_link_id' => '4','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
                ['id_config' => '72','is_deleted' => '0','analysis_id' => '16','item_link_type_id' => '4','item_link_id' => '4','min_value' => '0','max_value' => '0','created_by_id' => '1','updated_by_id' => '1'],
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
    
            Schema::connection($this->sConnection)->drop('qms_ana_configs');
        }
    }
}
