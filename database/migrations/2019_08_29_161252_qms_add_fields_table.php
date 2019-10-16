<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class QmsAddFieldsTable extends Migration
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                                                $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        Schema::connection($this->sConnection)->create('qms_element_fields', function (blueprint $table) {	
            $table->increments('id_field');
            $table->char('field_name', 150);
            $table->char('field_default_value', 150);
            $table->boolean('is_deleted');
            $table->integer('element_id')->unsigned();
            $table->timestamps();
            
            $table->foreign('element_id')->references('id_element')->on('qms_doc_elements')->onDelete('cascade');
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                                                $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        Schema::connection($this->sConnection)->drop('qms_element_fields');
      }
    }
}
