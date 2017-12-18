<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddMonthsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erpu_months', function (blueprint $table) {
          	$table->increments('id_month');
          	$table->integer('month');
          	$table->boolean('is_closed');
          	$table->boolean('is_deleted');
          	$table->integer('year_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->unique(['month', 'year_id']);
          	$table->foreign('year_id')->references('id_year')->on('erpu_years')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_months')->insert([
          	['id_month' => '1','month' => '1','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '2','month' => '2','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '3','month' => '3','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '4','month' => '4','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '5','month' => '5','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '6','month' => '6','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '7','month' => '7','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '8','month' => '8','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '9','month' => '9','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '10','month' => '10','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '11','month' => '11','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '12','month' => '12','is_closed' => '0','year_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '13','month' => '1','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '14','month' => '2','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '15','month' => '3','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '16','month' => '4','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '17','month' => '5','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '18','month' => '6','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '19','month' => '7','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '20','month' => '8','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '21','month' => '9','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '22','month' => '10','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '23','month' => '11','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '24','month' => '12','is_closed' => '0','year_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '25','month' => '1','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '26','month' => '2','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '27','month' => '3','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '28','month' => '4','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '29','month' => '5','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '30','month' => '6','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '31','month' => '7','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '32','month' => '8','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '33','month' => '9','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '34','month' => '10','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '35','month' => '11','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_month' => '36','month' => '12','is_closed' => '0','year_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erpu_months');
        }
    }
}
