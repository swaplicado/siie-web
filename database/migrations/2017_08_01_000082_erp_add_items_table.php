<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddItemsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erpu_items', function (blueprint $table) {
          	$table->increments('id_item');
          	$table->char('code', 50);
          	$table->char('name', 255);
          	$table->decimal('length', 23,8);
          	$table->decimal('surface', 23,8);
          	$table->decimal('volume', 23,8);
          	$table->decimal('mass', 23,8);
          	$table->integer('external_id');
            $table->unique('external_id');
          	$table->boolean('is_lot');
          	$table->boolean('is_bulk');
          	$table->boolean('is_deleted');
          	$table->integer('item_gender_id')->index();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('item_gender_id')->references('external_id')->on('erpu_item_genders')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::statement('ALTER TABLE siie_cartro.erpu_items AUTO_INCREMENT = 0;');
          DB::connection($this->sConnection)->table('erpu_items')->insert([
          	['id_item' => '0','code' => 'N/A','name' => 'N/A','length' => '0','surface' => '0','volume' => '0',
            'mass' => '0','is_lot' => '0','is_bulk' => '0','item_gender_id' => '0','unit_id' => '1',
            'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erpu_items');
        }
    }
}
