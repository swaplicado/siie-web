<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class MmsAddMovementsTypes extends Migration
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        DB::connection($this->sConnection)->table('wmss_mvt_types')->insert([
          ['id_mvt_type' => '17','code' => 'SEMP','name' => 'SALIDA ENTREGA MP','mvt_class_id' => '2','is_deleted' => '0'],
          ['id_mvt_type' => '18','code' => 'SDMP','name' => 'SALIDA DEVOLUCIÓN MP','mvt_class_id' => '2','is_deleted' => '0'],
          ['id_mvt_type' => '19','code' => 'SEPP','name' => 'SALIDA ENTREGA PP','mvt_class_id' => '2','is_deleted' => '0'],
          ['id_mvt_type' => '20','code' => 'SDPP','name' => 'SALIDA DEVOLUCIÓN PP','mvt_class_id' => '2','is_deleted' => '0'],
          ['id_mvt_type' => '21','code' => 'SEPT','name' => 'SALIDA ENTREGA PT','mvt_class_id' => '2','is_deleted' => '0'],
          ['id_mvt_type' => '22','code' => 'SDPT','name' => 'SALIDA DEVOLUCIÓN PT','mvt_class_id' => '2','is_deleted' => '0'],
          ['id_mvt_type' => '23','code' => 'SCON','name' => 'SALIDA CONSUMO INSUMOS Y PT','mvt_class_id' => '2','is_deleted' => '0'],
          ['id_mvt_type' => '24','code' => 'EEMP','name' => 'ENTRADA ENTREGA MP','mvt_class_id' => '1','is_deleted' => '0'],
          ['id_mvt_type' => '25','code' => 'EDMP','name' => 'ENTRADA DEVOLUCIÓN MP','mvt_class_id' => '1','is_deleted' => '0'],
          ['id_mvt_type' => '26','code' => 'EEPP','name' => 'ENTRADA ENTREGA PP','mvt_class_id' => '1','is_deleted' => '0'],
          ['id_mvt_type' => '27','code' => 'EDPP','name' => 'ENTRADA DEVOLUCIÓN PP','mvt_class_id' => '1','is_deleted' => '0'],
          ['id_mvt_type' => '28','code' => 'EEPT','name' => 'ENTRADA ENTREGA PT','mvt_class_id' => '1','is_deleted' => '0'],
          ['id_mvt_type' => '29','code' => 'EDPT','name' => 'ENTRADA DEVOLUCIÓN PT','mvt_class_id' => '1','is_deleted' => '0'],
          ['id_mvt_type' => '30','code' => 'ECON','name' => 'ENTRADA CONSUMO INSUMOS Y PT','mvt_class_id' => '1','is_deleted' => '0'],
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
        SConnectionUtils::reconnectDataBase($this->sConnection, $this->bDefault,
                  $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

        DB::connection($this->sConnection)->table('wmss_mvt_types')
                                              ->whereIn('id_mvt_type',
                                              ['17', '18', '19', '20', '21',
                                                '22', '23', '24', '25', '26',
                                                '27', '28', '29','30'
                                                ])
                                              ->delete();
      }
    }
}
