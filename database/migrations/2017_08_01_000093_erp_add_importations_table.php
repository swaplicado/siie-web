<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddImportationsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erps_importations', function (blueprint $table) {
          	$table->increments('id_importation');
          	$table->char('code', 10);
          	$table->char('name', 200);
          	$table->datetime('last_importation');
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erps_importations')->insert([
          	['id_importation' => '1','code' => '001','name' => 'UNIDADES DE ITEMS','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '2','code' => '002','name' => 'FAMILIAS DE ITEMS','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '3','code' => '003','name' => 'GRUPOS DE ÍTEMS','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '4','code' => '004','name' => 'GÉNEROS DE ÍTEMS','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '5','code' => '005','name' => 'ITEMS','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '6','code' => '006','name' => 'ASOCIADOS DE NEGOCIOS','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '7','code' => '007','name' => 'SUCURSALES','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '8','code' => '008','name' => 'DIRECCIONES','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '9','code' => '009','name' => 'DOCUMENTOS','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '10','code' => '010','name' => 'RENGLONES','last_importation' => '0000-00-00','updated_by_id' => '1'],
          	['id_importation' => '11','code' => '011','name' => 'RENGLONES II','last_importation' => '0000-00-00','updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erps_importations');
        }
    }
}
