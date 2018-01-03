<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddBranchesTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erpu_branches', function (blueprint $table) {
          	$table->increments('id_branch');
          	$table->char('code', 10);
          	$table->char('name', 100);
          	$table->integer('external_id');
          	$table->boolean('is_headquarters');
          	$table->boolean('is_deleted');
          	$table->integer('partner_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('partner_id')->references('id_partner')->on('erpu_partners')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_branches')->insert([
          	['id_branch' => '1','code' => 'N/A','name' => 'N/A','external_id' => 'N/A','is_headquarters' => '1','partner_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
            ['id_branch' => '2','code' => '001','name' => 'MATRIZ','external_id' => '0','is_headquarters' => '1','partner_id' => '2', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
            ['id_branch' => '3','code' => '002','name' => 'MATRIZ','external_id' => '0','is_headquarters' => '1','partner_id' => '3', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erpu_branches');
        }
    }
}
