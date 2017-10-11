<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SUtil;

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
          SUtil::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

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
          	$table->foreign('item_class_id')->references('id_class')->on('erps_item_classes')->onDelete('cascade');
          	$table->foreign('item_type_id')->references('id_item_type')->on('erps_item_types')->onDelete('cascade');
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
        foreach ($this->lDatabases as $base) {
          $this->sDataBase = $base;
          SUtil::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

          Schema::connection($this->sConnection)->drop('erpu_item_genders');
          Schema::connection($this->sConnection)->drop('erpu_item_groups');
          Schema::connection($this->sConnection)->drop('erpu_item_families');
        }
    }
}
