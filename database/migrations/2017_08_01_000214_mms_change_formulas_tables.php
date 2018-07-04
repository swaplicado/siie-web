<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsChangeFormulasTables extends Migration {
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

          // Schema::connection($this->sConnection)->drop('mms_formula_notes');
          // Schema::connection($this->sConnection)->drop('mms_form_substitutes');
          // Schema::connection($this->sConnection)->drop('mms_formulas');
          // Schema::connection($this->sConnection)->drop('mms_formula_rows');

          Schema::connection($this->sConnection)->create('mms_formulas', function (blueprint $table) {
          	$table->increments('id_formula');
          	$table->char('folio', 50);
          	$table->integer('version');
          	$table->integer('recipe')->unsigned();
          	$table->char('identifier', 250);
          	$table->date('dt_date');
          	$table->char('notes', 250);
          	$table->decimal('quantity', 23,8);
          	$table->boolean('is_deleted');
          	$table->integer('item_id')->unsigned();
          	$table->integer('unit_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
          	$table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('mms_formulas')->insert([
          	['id_formula' => '1','folio' => '00000','version' => '0','recipe' => '0',
            'identifier' => 'NA','dt_date' => '2017-01-01','notes' => 'NA',
            'quantity' => '0','is_deleted' => '0','item_id' => '1','unit_id' => '1',
            'created_by_id' => '1','updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('mms_formula_rows', function (blueprint $table) {
            $table->increments('id_formula_row');
            $table->decimal('quantity', 23,8);
            $table->decimal('mass', 23,8);
            $table->boolean('is_deleted');
            $table->integer('formula_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->integer('unit_id')->unsigned();
            $table->integer('item_recipe_id')->unsigned();
            $table->integer('created_by_id')->unsigned();
            $table->integer('updated_by_id')->unsigned();
            $table->timestamps();

            $table->foreign('formula_id')->references('id_formula')->on('mms_formulas')->onDelete('cascade');
            $table->foreign('item_id')->references('id_item')->on('erpu_items')->onDelete('cascade');
            $table->foreign('unit_id')->references('id_unit')->on('erpu_units')->onDelete('cascade');
            // $table->foreign('item_recipe_id')->references('recipe')->on('mms_formulas')->onDelete('cascade');
            $table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
            $table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('mms_formula_rows')->insert([
          	['id_formula_row' => '1','quantity' => '0','mass' => '0','is_deleted' => '1',
            'formula_id' => '1','item_id' => '1','unit_id' => '1',
            'item_recipe_id' => '1','created_by_id' => '1','updated_by_id' => '1'],
          ]);

        }

        DB::table('syss_permissions')->insert([
          ['code' => '120','name' => 'MMS_FORMULAS', 'is_deleted' => '0','module_id' => '2'],
        ]);
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

          Schema::connection($this->sConnection)->drop('mms_formula_rows');
          Schema::connection($this->sConnection)->drop('mms_formulas');
        }
    }
}
