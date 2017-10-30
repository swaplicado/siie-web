<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class WmsAddComponentBarcodesTable extends Migration
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

        Schema::connection($this->sConnection)->create('wms_componet_barcodes', function (blueprint $table) {

        $table->increments('id_component');
      	$table->char('name', 100);
      	$table->integer('digits');
      	$table->enum('type_barcode',['Item', 'Tarima']);
      	$table->integer('created_by_id')->unsigned();
      	$table->integer('updated_by_id')->unsigned();
      	$table->timestamps();

      	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
      	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
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
        //
    }
}
