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

        DB::table('wms_componet_barcodes')->insert([
          ['id_component' => '1','name' => 'id_lot','digits' => '6', 'created_by_id' => '1','updated_by_id' => '1','created_at' => '0000-00-00','updated_at' => '0000-00-00'],
          ['id_component' => '2','name' => 'text_lot','digits' => '15', 'created_by_id' => '1','updated_by_id' => '1','created_at' => '0000-00-00','updated_at' => '0000-00-00'],
          ['id_component' => '3','name' => 'id_item','digits' => '4', 'created_by_id' => '1','updated_by_id' => '1','created_at' => '0000-00-00','updated_at' => '0000-00-00'],
          ['id_component' => '4','name' => 'id_unit','digits' => '3', 'created_by_id' => '1','updated_by_id' => '1','created_at' => '0000-00-00','updated_at' => '0000-00-00'],
          ['id_component' => '5','name' => 'id_pallet','digits' => '9', 'created_by_id' => '1','updated_by_id' => '1','created_at' => '0000-00-00','updated_at' => '0000-00-00'],
          ['id_component' => '6','name' => 'text_pallet','digits' => '16', 'created_by_id' => '1','updated_by_id' => '1','created_at' => '0000-00-00','updated_at' => '0000-00-00'],
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
        //
    }
}
