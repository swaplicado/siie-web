<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsAddFormulasSubsTable extends Migration {
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

          Schema::connection($this->sConnection)->create('mms_form_substitutes', function (blueprint $table) {
          	$table->increments('id_substitute');
          	$table->decimal('percentage', 23,8);
          	$table->decimal('percentage_max', 23,8);
          	$table->boolean('is_deleted');
          	$table->integer('formula_row_id')->unsigned();
          	$table->integer('formula_id')->unsigned();
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('item_formula_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('formula_row_id')->references('id_formula_row')->on('mms_formula_rows')->onDelete('cascade');
          	$table->foreign('formula_id')->references('id_formula')->on('mms_formulas')->onDelete('cascade');
          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('item_formula_id')->references('id_formula')->on('mms_formulas')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('mms_form_substitutes')->insert([
          	['id_substitute' => '1','percentage' => '0','percentage_max' => '0',
            'is_deleted' => '1','formula_row_id' => '1','formula_id' => '1',
            'item_id' => '1','unit_id' => '1','item_formula_id' => '1',
            'created_by_id' => '1','updated_by_id' => '1'],
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

          Schema::connection($this->sConnection)->drop('mms_form_substitutes');
        }
    }
}
