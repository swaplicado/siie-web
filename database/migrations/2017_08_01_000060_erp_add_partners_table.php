<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddPartnersTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erpu_partners', function (blueprint $table) {
          	$table->increments('id_partner');
            $table->char('code', 50)->unique();
          	$table->char('name', 200);
          	$table->char('last_name', 100);
          	$table->char('first_name', 100);
          	$table->char('fiscal_id', 50);
          	$table->char('person_id', 50);
          	$table->integer('external_id');
          	$table->boolean('is_company');
          	$table->boolean('is_customer');
          	$table->boolean('is_supplier');
          	$table->boolean('is_related_party');
          	$table->boolean('is_deleted');
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_partners')->insert([
            ['id_partner' => '1','code' => 'NA','name' => 'NA','last_name' => '','first_name' => '','fiscal_id' => 'XX','person_id' => 'XX','external_id' => '0','is_company' => '1','is_customer' => '0','is_supplier' => '0','is_related_party' => '0', 'is_deleted' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
            ['id_partner' => '2','code' => 'Cartro SA de CV','name' => 'Cartro SA de CV','last_name' => ' ','first_name' => ' ','fiscal_id' => 'CARTRO12345','person_id' => 'CARTRO12345','external_id' => '0','is_company' => '1','is_customer' => '0','is_supplier' => '0','is_related_party' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
            ['id_partner' => '3','code' => 'Aceites Especiales TH','name' => 'Aceites Especiales TH','last_name' => ' ','first_name' => ' ','fiscal_id' => 'AETH123445','person_id' => 'AETH7864654','external_id' => '0','is_company' => '1','is_customer' => '0','is_supplier' => '0','is_related_party' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erpu_partners');
        }
    }
}
