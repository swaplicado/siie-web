<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddErpConfigurationsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erp_configuration', function (blueprint $table) {
          	$table->increments('id_configuration');
          	$table->char('code', 10)->unique();
          	$table->char('name', 200);
          	$table->integer('val_int');
          	$table->char('val_text', 50);
          	$table->decimal('val_dec', 10,10);
          	$table->boolean('is_deleted');
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();
          });

          DB::connection($this->sConnection)->table('erp_configuration')->insert([
          	['id_configuration' => '1','code' => '001','name' => 'id partner empresa','val_int' => '1','val_text' => ' ','val_dec' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_configuration' => '2','code' => '002','name' => 'num decimales','val_int' => '3','val_text' => ' ','val_dec' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erp_configuration');
        }
    }
}
