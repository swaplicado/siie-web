<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddFamiliesGroupsGendersTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erpu_item_families', function (blueprint $table) {
          	$table->increments('id_item_family');
          	$table->char('name', 100);
          	$table->integer('external_id');
          	$table->boolean('is_deleted');
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_item_families')->insert([
          	['id_item_family' => '1','name' => 'N/A','external_id' => '0', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('erpu_item_groups', function (blueprint $table) {
          	$table->increments('id_item_group');
          	$table->char('name', 100);
          	$table->integer('external_id');
          	$table->boolean('is_deleted');
          	$table->integer('item_family_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('item_family_id')->references('id_item_family')->on('erpu_item_families')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_item_groups')->insert([
          	['id_item_group' => '1','name' => 'N/A','external_id' => '0', 'is_deleted' => '0','item_family_id' => '1', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('erpu_item_genders', function (blueprint $table) {
          	$table->increments('id_item_gender');
          	$table->char('name', 100);
          	$table->integer('external_id');
          	$table->boolean('is_length');
          	$table->boolean('is_length_var');
          	$table->boolean('is_surface');
          	$table->boolean('is_surface_var');
          	$table->boolean('is_volume');
          	$table->boolean('is_volume_var');
          	$table->boolean('is_mass');
          	$table->boolean('is_mass_var');
          	$table->boolean('is_lot');
          	$table->boolean('is_bulk');
          	$table->boolean('is_deleted');
          	$table->integer('item_group_id')->unsigned();
          	$table->integer('item_class_id')->unsigned();
          	$table->integer('item_type_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('item_group_id')->references('id_item_group')->on('erpu_item_groups')->onDelete('cascade');
          	$table->foreign('item_class_id')->references('id_item_class')->on('erps_item_classes')->onDelete('cascade');
          	$table->foreign('item_type_id')->references('id_item_type')->on('erps_item_types')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erpu_item_genders')->insert([	
          	['id_item_gender' => '1','name' => 'N/A','external_id' => '0','is_length' => '0','is_length_var' => '0','is_surface' => '0',
            'is_surface_var' => '0','is_volume' => '0','is_volume_var' => '0','is_mass' => '0','is_mass_var' => '0','is_lot' => '0','is_bulk' => '0',
            'item_group_id' => '1','item_class_id' => '1','item_type_id' => '1', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('erpu_item_genders');
          Schema::connection($this->sConnection)->drop('erpu_item_groups');
          Schema::connection($this->sConnection)->drop('erpu_item_families');
        }
    }
}
