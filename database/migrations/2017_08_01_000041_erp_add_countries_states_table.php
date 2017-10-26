<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SConnectionUtils;

class ErpAddCountriesStatesTable extends Migration {
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

          Schema::connection($this->sConnection)->create('erps_countries', function (blueprint $table) {
          	$table->increments('id_country');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->char('abbreviation', 50);
          	$table->char('country_lan', 100);
          	$table->boolean('is_deleted');
          	$table->timestamps();
          });


          DB::connection($this->sConnection)->table('erps_countries')->insert([
          	['id_country' => '1','code' => 'N/A','name' => 'N/A','abbreviation' => 'NA','country_lan' => 'N/A', 'is_deleted' => '0'],
          	['id_country' => '2','code' => '101','name' => 'Afganistán','abbreviation' => 'AFG','country_lan' => 'Afganistán', 'is_deleted' => '0'],
          	['id_country' => '3','code' => '102','name' => 'Islas Åland','abbreviation' => 'ALA','country_lan' => 'Islas Åland', 'is_deleted' => '0'],
          	['id_country' => '4','code' => '103','name' => 'Albania','abbreviation' => 'ALB','country_lan' => 'Albania', 'is_deleted' => '0'],
          	['id_country' => '5','code' => '104','name' => 'Alemania','abbreviation' => 'DEU','country_lan' => 'Alemania', 'is_deleted' => '0'],
          	['id_country' => '6','code' => '105','name' => 'Andorra','abbreviation' => 'AND','country_lan' => 'Andorra', 'is_deleted' => '0'],
          	['id_country' => '7','code' => '106','name' => 'Angola','abbreviation' => 'AGO','country_lan' => 'Angola', 'is_deleted' => '0'],
          	['id_country' => '8','code' => '107','name' => 'Anguila','abbreviation' => 'AIA','country_lan' => 'Anguila', 'is_deleted' => '0'],
          	['id_country' => '9','code' => '108','name' => 'Antártida','abbreviation' => 'ATA','country_lan' => 'Antártida', 'is_deleted' => '0'],
          	['id_country' => '10','code' => '109','name' => 'Antigua y Barbuda','abbreviation' => 'ATG','country_lan' => 'Antigua y Barbuda', 'is_deleted' => '0'],
          	['id_country' => '11','code' => '110','name' => 'Arabia Saudita','abbreviation' => 'SAU','country_lan' => 'Arabia Saudita', 'is_deleted' => '0'],
          	['id_country' => '12','code' => '111','name' => 'Argelia','abbreviation' => 'DZA','country_lan' => 'Argelia', 'is_deleted' => '0'],
          	['id_country' => '13','code' => '112','name' => 'Argentina','abbreviation' => 'ARG','country_lan' => 'Argentina', 'is_deleted' => '0'],
          	['id_country' => '14','code' => '113','name' => 'Armenia','abbreviation' => 'ARM','country_lan' => 'Armenia', 'is_deleted' => '0'],
          	['id_country' => '15','code' => '114','name' => 'Aruba','abbreviation' => 'ABW','country_lan' => 'Aruba', 'is_deleted' => '0'],
          	['id_country' => '16','code' => '115','name' => 'Australia','abbreviation' => 'AUS','country_lan' => 'Australia', 'is_deleted' => '0'],
          	['id_country' => '17','code' => '116','name' => 'Austria','abbreviation' => 'AUT','country_lan' => 'Austria', 'is_deleted' => '0'],
          	['id_country' => '18','code' => '117','name' => 'Azerbaiyán','abbreviation' => 'AZE','country_lan' => 'Azerbaiyán', 'is_deleted' => '0'],
          	['id_country' => '19','code' => '118','name' => 'Bahamas (las)','abbreviation' => 'BHS','country_lan' => 'Bahamas (las)', 'is_deleted' => '0'],
          	['id_country' => '20','code' => '119','name' => 'Bangladés','abbreviation' => 'BGD','country_lan' => 'Bangladés', 'is_deleted' => '0'],
          	['id_country' => '21','code' => '120','name' => 'Barbados','abbreviation' => 'BRB','country_lan' => 'Barbados', 'is_deleted' => '0'],
          	['id_country' => '22','code' => '121','name' => 'Baréin','abbreviation' => 'BHR','country_lan' => 'Baréin', 'is_deleted' => '0'],
          	['id_country' => '23','code' => '122','name' => 'Bélgica','abbreviation' => 'BEL','country_lan' => 'Bélgica', 'is_deleted' => '0'],
          	['id_country' => '24','code' => '123','name' => 'Belice','abbreviation' => 'BLZ','country_lan' => 'Belice', 'is_deleted' => '0'],
          	['id_country' => '25','code' => '124','name' => 'Benín','abbreviation' => 'BEN','country_lan' => 'Benín', 'is_deleted' => '0'],
          	['id_country' => '26','code' => '125','name' => 'Bermudas','abbreviation' => 'BMU','country_lan' => 'Bermudas', 'is_deleted' => '0'],
          	['id_country' => '27','code' => '126','name' => 'Bielorrusia','abbreviation' => 'BLR','country_lan' => 'Bielorrusia', 'is_deleted' => '0'],
          	['id_country' => '28','code' => '127','name' => 'Myanmar','abbreviation' => 'MMR','country_lan' => 'Myanmar', 'is_deleted' => '0'],
          	['id_country' => '29','code' => '128','name' => 'Bolivia, Estado Plurinacional de','abbreviation' => 'BOL','country_lan' => 'Bolivia, Estado Plurinacional de', 'is_deleted' => '0'],
          	['id_country' => '30','code' => '129','name' => 'Bosnia y Herzegovina','abbreviation' => 'BIH','country_lan' => 'Bosnia y Herzegovina', 'is_deleted' => '0'],
          	['id_country' => '31','code' => '130','name' => 'Botsuana','abbreviation' => 'BWA','country_lan' => 'Botsuana', 'is_deleted' => '0'],
          	['id_country' => '32','code' => '131','name' => 'Brasil','abbreviation' => 'BRA','country_lan' => 'Brasil', 'is_deleted' => '0'],
          	['id_country' => '33','code' => '132','name' => 'Brunéi Darussalam','abbreviation' => 'BRN','country_lan' => 'Brunéi Darussalam', 'is_deleted' => '0'],
          	['id_country' => '34','code' => '133','name' => 'Bulgaria','abbreviation' => 'BGR','country_lan' => 'Bulgaria', 'is_deleted' => '0'],
          	['id_country' => '35','code' => '134','name' => 'Burkina Faso','abbreviation' => 'BFA','country_lan' => 'Burkina Faso', 'is_deleted' => '0'],
          	['id_country' => '36','code' => '135','name' => 'Burundi','abbreviation' => 'BDI','country_lan' => 'Burundi', 'is_deleted' => '0'],
          	['id_country' => '37','code' => '136','name' => 'Bután','abbreviation' => 'BTN','country_lan' => 'Bután', 'is_deleted' => '0'],
          	['id_country' => '38','code' => '137','name' => 'Cabo Verde','abbreviation' => 'CPV','country_lan' => 'Cabo Verde', 'is_deleted' => '0'],
          	['id_country' => '39','code' => '138','name' => 'Camboya','abbreviation' => 'KHM','country_lan' => 'Camboya', 'is_deleted' => '0'],
          	['id_country' => '40','code' => '139','name' => 'Camerún','abbreviation' => 'CMR','country_lan' => 'Camerún', 'is_deleted' => '0'],
          	['id_country' => '41','code' => '140','name' => 'Canadá','abbreviation' => 'CAN','country_lan' => 'Canadá', 'is_deleted' => '0'],
          	['id_country' => '42','code' => '141','name' => 'Catar','abbreviation' => 'QAT','country_lan' => 'Catar', 'is_deleted' => '0'],
          	['id_country' => '43','code' => '142','name' => 'Bonaire, San Eustaquio y Saba','abbreviation' => 'BES','country_lan' => 'Bonaire, San Eustaquio y Saba', 'is_deleted' => '0'],
          	['id_country' => '44','code' => '143','name' => 'Chad','abbreviation' => 'TCD','country_lan' => 'Chad', 'is_deleted' => '0'],
          	['id_country' => '45','code' => '144','name' => 'Chile','abbreviation' => 'CHL','country_lan' => 'Chile', 'is_deleted' => '0'],
          	['id_country' => '46','code' => '145','name' => 'China','abbreviation' => 'CHN','country_lan' => 'China', 'is_deleted' => '0'],
          	['id_country' => '47','code' => '146','name' => 'Chipre','abbreviation' => 'CYP','country_lan' => 'Chipre', 'is_deleted' => '0'],
          	['id_country' => '48','code' => '147','name' => 'Colombia','abbreviation' => 'COL','country_lan' => 'Colombia', 'is_deleted' => '0'],
          	['id_country' => '49','code' => '148','name' => 'Comoras','abbreviation' => 'COM','country_lan' => 'Comoras', 'is_deleted' => '0'],
          	['id_country' => '50','code' => '149','name' => 'Corea (la República Democrática Popular de)','abbreviation' => 'PRK','country_lan' => 'Corea (la República Democrática Popular de)', 'is_deleted' => '0'],
          	['id_country' => '51','code' => '150','name' => 'Corea (la República de)','abbreviation' => 'KOR','country_lan' => 'Corea (la República de)', 'is_deleted' => '0'],
          	['id_country' => '52','code' => '151','name' => 'Côte dIvoire','abbreviation' => 'CIV','country_lan' => 'Côte dIvoire', 'is_deleted' => '0'],
          	['id_country' => '53','code' => '152','name' => 'Costa Rica','abbreviation' => 'CRI','country_lan' => 'Costa Rica', 'is_deleted' => '0'],
          	['id_country' => '54','code' => '153','name' => 'Croacia','abbreviation' => 'HRV','country_lan' => 'Croacia', 'is_deleted' => '0'],
          	['id_country' => '55','code' => '154','name' => 'Cuba','abbreviation' => 'CUB','country_lan' => 'Cuba', 'is_deleted' => '0'],
          	['id_country' => '56','code' => '155','name' => 'Curaçao','abbreviation' => 'CUW','country_lan' => 'Curaçao', 'is_deleted' => '0'],
          	['id_country' => '57','code' => '156','name' => 'Dinamarca','abbreviation' => 'DNK','country_lan' => 'Dinamarca', 'is_deleted' => '0'],
          	['id_country' => '58','code' => '157','name' => 'Dominica','abbreviation' => 'DMA','country_lan' => 'Dominica', 'is_deleted' => '0'],
          	['id_country' => '59','code' => '158','name' => 'Ecuador','abbreviation' => 'ECU','country_lan' => 'Ecuador', 'is_deleted' => '0'],
          	['id_country' => '60','code' => '159','name' => 'Egipto','abbreviation' => 'EGY','country_lan' => 'Egipto', 'is_deleted' => '0'],
          	['id_country' => '61','code' => '160','name' => 'El Salvador','abbreviation' => 'SLV','country_lan' => 'El Salvador', 'is_deleted' => '0'],
          	['id_country' => '62','code' => '161','name' => 'Emiratos Árabes Unidos (Los)','abbreviation' => 'ARE','country_lan' => 'Emiratos Árabes Unidos (Los)', 'is_deleted' => '0'],
          	['id_country' => '63','code' => '162','name' => 'Eritrea','abbreviation' => 'ERI','country_lan' => 'Eritrea', 'is_deleted' => '0'],
          	['id_country' => '64','code' => '163','name' => 'Eslovaquia','abbreviation' => 'SVK','country_lan' => 'Eslovaquia', 'is_deleted' => '0'],
          	['id_country' => '65','code' => '164','name' => 'Eslovenia','abbreviation' => 'SVN','country_lan' => 'Eslovenia', 'is_deleted' => '0'],
          	['id_country' => '66','code' => '165','name' => 'España','abbreviation' => 'ESP','country_lan' => 'España', 'is_deleted' => '0'],
          	['id_country' => '67','code' => '166','name' => 'Estados Unidos (los)','abbreviation' => 'USA','country_lan' => 'Estados Unidos (los)', 'is_deleted' => '0'],
          	['id_country' => '68','code' => '167','name' => 'Estonia','abbreviation' => 'EST','country_lan' => 'Estonia', 'is_deleted' => '0'],
          	['id_country' => '69','code' => '168','name' => 'Etiopía','abbreviation' => 'ETH','country_lan' => 'Etiopía', 'is_deleted' => '0'],
          	['id_country' => '70','code' => '169','name' => 'Filipinas (las)','abbreviation' => 'PHL','country_lan' => 'Filipinas (las)', 'is_deleted' => '0'],
          	['id_country' => '71','code' => '170','name' => 'Finlandia','abbreviation' => 'FIN','country_lan' => 'Finlandia', 'is_deleted' => '0'],
          	['id_country' => '72','code' => '171','name' => 'Fiyi','abbreviation' => 'FJI','country_lan' => 'Fiyi', 'is_deleted' => '0'],
          	['id_country' => '73','code' => '172','name' => 'Francia','abbreviation' => 'FRA','country_lan' => 'Francia', 'is_deleted' => '0'],
          	['id_country' => '74','code' => '173','name' => 'Gabón','abbreviation' => 'GAB','country_lan' => 'Gabón', 'is_deleted' => '0'],
          	['id_country' => '75','code' => '174','name' => 'Gambia (La)','abbreviation' => 'GMB','country_lan' => 'Gambia (La)', 'is_deleted' => '0'],
          	['id_country' => '76','code' => '175','name' => 'Georgia','abbreviation' => 'GEO','country_lan' => 'Georgia', 'is_deleted' => '0'],
          	['id_country' => '77','code' => '176','name' => 'Ghana','abbreviation' => 'GHA','country_lan' => 'Ghana', 'is_deleted' => '0'],
          	['id_country' => '78','code' => '177','name' => 'Gibraltar','abbreviation' => 'GIB','country_lan' => 'Gibraltar', 'is_deleted' => '0'],
          	['id_country' => '79','code' => '178','name' => 'Granada','abbreviation' => 'GRD','country_lan' => 'Granada', 'is_deleted' => '0'],
          	['id_country' => '80','code' => '179','name' => 'Grecia','abbreviation' => 'GRC','country_lan' => 'Grecia', 'is_deleted' => '0'],
          	['id_country' => '81','code' => '180','name' => 'Groenlandia','abbreviation' => 'GRL','country_lan' => 'Groenlandia', 'is_deleted' => '0'],
          	['id_country' => '82','code' => '181','name' => 'Guadalupe','abbreviation' => 'GLP','country_lan' => 'Guadalupe', 'is_deleted' => '0'],
          	['id_country' => '83','code' => '182','name' => 'Guam','abbreviation' => 'GUM','country_lan' => 'Guam', 'is_deleted' => '0'],
          	['id_country' => '84','code' => '183','name' => 'Guatemala','abbreviation' => 'GTM','country_lan' => 'Guatemala', 'is_deleted' => '0'],
          	['id_country' => '85','code' => '184','name' => 'Guayana Francesa','abbreviation' => 'GUF','country_lan' => 'Guayana Francesa', 'is_deleted' => '0'],
          	['id_country' => '86','code' => '185','name' => 'Guernsey','abbreviation' => 'GGY','country_lan' => 'Guernsey', 'is_deleted' => '0'],
          	['id_country' => '87','code' => '186','name' => 'Guinea','abbreviation' => 'GIN','country_lan' => 'Guinea', 'is_deleted' => '0'],
          	['id_country' => '88','code' => '187','name' => 'Guinea-Bisáu','abbreviation' => 'GNB','country_lan' => 'Guinea-Bisáu', 'is_deleted' => '0'],
          	['id_country' => '89','code' => '188','name' => 'Guinea Ecuatorial','abbreviation' => 'GNQ','country_lan' => 'Guinea Ecuatorial', 'is_deleted' => '0'],
          	['id_country' => '90','code' => '189','name' => 'Guyana','abbreviation' => 'GUY','country_lan' => 'Guyana', 'is_deleted' => '0'],
          	['id_country' => '91','code' => '190','name' => 'Haití','abbreviation' => 'HTI','country_lan' => 'Haití', 'is_deleted' => '0'],
          	['id_country' => '92','code' => '191','name' => 'Honduras','abbreviation' => 'HND','country_lan' => 'Honduras', 'is_deleted' => '0'],
          	['id_country' => '93','code' => '192','name' => 'Hong Kong','abbreviation' => 'HKG','country_lan' => 'Hong Kong', 'is_deleted' => '0'],
          	['id_country' => '94','code' => '193','name' => 'Hungría','abbreviation' => 'HUN','country_lan' => 'Hungría', 'is_deleted' => '0'],
          	['id_country' => '95','code' => '194','name' => 'India','abbreviation' => 'IND','country_lan' => 'India', 'is_deleted' => '0'],
          	['id_country' => '96','code' => '195','name' => 'Indonesia','abbreviation' => 'IDN','country_lan' => 'Indonesia', 'is_deleted' => '0'],
          	['id_country' => '97','code' => '196','name' => 'Irak','abbreviation' => 'IRQ','country_lan' => 'Irak', 'is_deleted' => '0'],
          	['id_country' => '98','code' => '197','name' => 'Irán (la República Islámica de)','abbreviation' => 'IRN','country_lan' => 'Irán (la República Islámica de)', 'is_deleted' => '0'],
          	['id_country' => '99','code' => '198','name' => 'Irlanda','abbreviation' => 'IRL','country_lan' => 'Irlanda', 'is_deleted' => '0'],
          	['id_country' => '100','code' => '199','name' => 'Isla Bouvet','abbreviation' => 'BVT','country_lan' => 'Isla Bouvet', 'is_deleted' => '0'],
          	['id_country' => '101','code' => '200','name' => 'Isla de Man','abbreviation' => 'IMN','country_lan' => 'Isla de Man', 'is_deleted' => '0'],
          	['id_country' => '102','code' => '201','name' => 'Isla de Navidad','abbreviation' => 'CXR','country_lan' => 'Isla de Navidad', 'is_deleted' => '0'],
          	['id_country' => '103','code' => '202','name' => 'Isla Norfolk','abbreviation' => 'NFK','country_lan' => 'Isla Norfolk', 'is_deleted' => '0'],
          	['id_country' => '104','code' => '203','name' => 'Islandia','abbreviation' => 'ISL','country_lan' => 'Islandia', 'is_deleted' => '0'],
          	['id_country' => '105','code' => '204','name' => 'Islas Caimán (las)','abbreviation' => 'CYM','country_lan' => 'Islas Caimán (las)', 'is_deleted' => '0'],
          	['id_country' => '106','code' => '205','name' => 'Islas Cocos (Keeling)','abbreviation' => 'CCK','country_lan' => 'Islas Cocos (Keeling)', 'is_deleted' => '0'],
          	['id_country' => '107','code' => '206','name' => 'Islas Cook (las)','abbreviation' => 'COK','country_lan' => 'Islas Cook (las)', 'is_deleted' => '0'],
          	['id_country' => '108','code' => '207','name' => 'Islas Feroe (las)','abbreviation' => 'FRO','country_lan' => 'Islas Feroe (las)', 'is_deleted' => '0'],
          	['id_country' => '109','code' => '208','name' => 'Georgia del sur y las islas sandwich del sur','abbreviation' => 'SGS','country_lan' => 'Georgia del sur y las islas sandwich del sur', 'is_deleted' => '0'],
          	['id_country' => '110','code' => '209','name' => 'Isla Heard e Islas McDonald','abbreviation' => 'HMD','country_lan' => 'Isla Heard e Islas McDonald', 'is_deleted' => '0'],
          	['id_country' => '111','code' => '210','name' => 'Islas Malvinas [Falkland] (las)','abbreviation' => 'FLK','country_lan' => 'Islas Malvinas [Falkland] (las)', 'is_deleted' => '0'],
          	['id_country' => '112','code' => '211','name' => 'Islas Marianas del Norte (las)','abbreviation' => 'MNP','country_lan' => 'Islas Marianas del Norte (las)', 'is_deleted' => '0'],
          	['id_country' => '113','code' => '212','name' => 'Islas Marshall (las)','abbreviation' => 'MHL','country_lan' => 'Islas Marshall (las)', 'is_deleted' => '0'],
          	['id_country' => '114','code' => '213','name' => 'Pitcairn','abbreviation' => 'PCN','country_lan' => 'Pitcairn', 'is_deleted' => '0'],
          	['id_country' => '115','code' => '214','name' => 'Islas Salomón (las)','abbreviation' => 'SLB','country_lan' => 'Islas Salomón (las)', 'is_deleted' => '0'],
          	['id_country' => '116','code' => '215','name' => 'Islas Turcas y Caicos (las)','abbreviation' => 'TCA','country_lan' => 'Islas Turcas y Caicos (las)', 'is_deleted' => '0'],
          	['id_country' => '117','code' => '216','name' => 'Islas de Ultramar Menores de Estados Unidos (las)','abbreviation' => 'UMI','country_lan' => 'Islas de Ultramar Menores de Estados Unidos (las)', 'is_deleted' => '0'],
          	['id_country' => '118','code' => '217','name' => 'Islas Vírgenes (Británicas)','abbreviation' => 'VGB','country_lan' => 'Islas Vírgenes (Británicas)', 'is_deleted' => '0'],
          	['id_country' => '119','code' => '218','name' => 'Islas Vírgenes (EE.UU.)','abbreviation' => 'VIR','country_lan' => 'Islas Vírgenes (EE.UU.)', 'is_deleted' => '0'],
          	['id_country' => '120','code' => '219','name' => 'Israel','abbreviation' => 'ISR','country_lan' => 'Israel', 'is_deleted' => '0'],
          	['id_country' => '121','code' => '220','name' => 'Italia','abbreviation' => 'ITA','country_lan' => 'Italia', 'is_deleted' => '0'],
          	['id_country' => '122','code' => '221','name' => 'Jamaica','abbreviation' => 'JAM','country_lan' => 'Jamaica', 'is_deleted' => '0'],
          	['id_country' => '123','code' => '222','name' => 'Japón','abbreviation' => 'JPN','country_lan' => 'Japón', 'is_deleted' => '0'],
          	['id_country' => '124','code' => '223','name' => 'Jersey','abbreviation' => 'JEY','country_lan' => 'Jersey', 'is_deleted' => '0'],
          	['id_country' => '125','code' => '224','name' => 'Jordania','abbreviation' => 'JOR','country_lan' => 'Jordania', 'is_deleted' => '0'],
          	['id_country' => '126','code' => '225','name' => 'Kazajistán','abbreviation' => 'KAZ','country_lan' => 'Kazajistán', 'is_deleted' => '0'],
          	['id_country' => '127','code' => '226','name' => 'Kenia','abbreviation' => 'KEN','country_lan' => 'Kenia', 'is_deleted' => '0'],
          	['id_country' => '128','code' => '227','name' => 'Kirguistán','abbreviation' => 'KGZ','country_lan' => 'Kirguistán', 'is_deleted' => '0'],
          	['id_country' => '129','code' => '228','name' => 'Kiribati','abbreviation' => 'KIR','country_lan' => 'Kiribati', 'is_deleted' => '0'],
          	['id_country' => '130','code' => '229','name' => 'Kuwait','abbreviation' => 'KWT','country_lan' => 'Kuwait', 'is_deleted' => '0'],
          	['id_country' => '131','code' => '230','name' => 'Lao, (la) República Democrática Popular','abbreviation' => 'LAO','country_lan' => 'Lao, (la) República Democrática Popular', 'is_deleted' => '0'],
          	['id_country' => '132','code' => '231','name' => 'Lesoto','abbreviation' => 'LSO','country_lan' => 'Lesoto', 'is_deleted' => '0'],
          	['id_country' => '133','code' => '232','name' => 'Letonia','abbreviation' => 'LVA','country_lan' => 'Letonia', 'is_deleted' => '0'],
          	['id_country' => '134','code' => '233','name' => 'Líbano','abbreviation' => 'LBN','country_lan' => 'Líbano', 'is_deleted' => '0'],
          	['id_country' => '135','code' => '234','name' => 'Liberia','abbreviation' => 'LBR','country_lan' => 'Liberia', 'is_deleted' => '0'],
          	['id_country' => '136','code' => '235','name' => 'Libia','abbreviation' => 'LBY','country_lan' => 'Libia', 'is_deleted' => '0'],
          	['id_country' => '137','code' => '236','name' => 'Liechtenstein','abbreviation' => 'LIE','country_lan' => 'Liechtenstein', 'is_deleted' => '0'],
          	['id_country' => '138','code' => '237','name' => 'Lituania','abbreviation' => 'LTU','country_lan' => 'Lituania', 'is_deleted' => '0'],
          	['id_country' => '139','code' => '238','name' => 'Luxemburgo','abbreviation' => 'LUX','country_lan' => 'Luxemburgo', 'is_deleted' => '0'],
          	['id_country' => '140','code' => '239','name' => 'Macao','abbreviation' => 'MAC','country_lan' => 'Macao', 'is_deleted' => '0'],
          	['id_country' => '141','code' => '240','name' => 'Madagascar','abbreviation' => 'MDG','country_lan' => 'Madagascar', 'is_deleted' => '0'],
          	['id_country' => '142','code' => '241','name' => 'Malasia','abbreviation' => 'MYS','country_lan' => 'Malasia', 'is_deleted' => '0'],
          	['id_country' => '143','code' => '242','name' => 'Malaui','abbreviation' => 'MWI','country_lan' => 'Malaui', 'is_deleted' => '0'],
          	['id_country' => '144','code' => '243','name' => 'Maldivas','abbreviation' => 'MDV','country_lan' => 'Maldivas', 'is_deleted' => '0'],
          	['id_country' => '145','code' => '244','name' => 'Malí','abbreviation' => 'MLI','country_lan' => 'Malí', 'is_deleted' => '0'],
          	['id_country' => '146','code' => '245','name' => 'Malta','abbreviation' => 'MLT','country_lan' => 'Malta', 'is_deleted' => '0'],
          	['id_country' => '147','code' => '246','name' => 'Marruecos','abbreviation' => 'MAR','country_lan' => 'Marruecos', 'is_deleted' => '0'],
          	['id_country' => '148','code' => '247','name' => 'Martinica','abbreviation' => 'MTQ','country_lan' => 'Martinica', 'is_deleted' => '0'],
          	['id_country' => '149','code' => '248','name' => 'Mauricio','abbreviation' => 'MUS','country_lan' => 'Mauricio', 'is_deleted' => '0'],
          	['id_country' => '150','code' => '249','name' => 'Mauritania','abbreviation' => 'MRT','country_lan' => 'Mauritania', 'is_deleted' => '0'],
          	['id_country' => '151','code' => '250','name' => 'Mayotte','abbreviation' => 'MYT','country_lan' => 'Mayotte', 'is_deleted' => '0'],
          	['id_country' => '152','code' => '251','name' => 'México','abbreviation' => 'MEX','country_lan' => 'México', 'is_deleted' => '0'],
          	['id_country' => '153','code' => '252','name' => 'Micronesia (los Estados Federados de)','abbreviation' => 'FSM','country_lan' => 'Micronesia (los Estados Federados de)', 'is_deleted' => '0'],
          	['id_country' => '154','code' => '253','name' => 'Moldavia (la República de)','abbreviation' => 'MDA','country_lan' => 'Moldavia (la República de)', 'is_deleted' => '0'],
          	['id_country' => '155','code' => '254','name' => 'Mónaco','abbreviation' => 'MCO','country_lan' => 'Mónaco', 'is_deleted' => '0'],
          	['id_country' => '156','code' => '255','name' => 'Mongolia','abbreviation' => 'MNG','country_lan' => 'Mongolia', 'is_deleted' => '0'],
          	['id_country' => '157','code' => '256','name' => 'Montenegro','abbreviation' => 'MNE','country_lan' => 'Montenegro', 'is_deleted' => '0'],
          	['id_country' => '158','code' => '257','name' => 'Montserrat','abbreviation' => 'MSR','country_lan' => 'Montserrat', 'is_deleted' => '0'],
          	['id_country' => '159','code' => '258','name' => 'Mozambique','abbreviation' => 'MOZ','country_lan' => 'Mozambique', 'is_deleted' => '0'],
          	['id_country' => '160','code' => '259','name' => 'Namibia','abbreviation' => 'NAM','country_lan' => 'Namibia', 'is_deleted' => '0'],
          	['id_country' => '161','code' => '260','name' => 'Nauru','abbreviation' => 'NRU','country_lan' => 'Nauru', 'is_deleted' => '0'],
          	['id_country' => '162','code' => '261','name' => 'Nepal','abbreviation' => 'NPL','country_lan' => 'Nepal', 'is_deleted' => '0'],
          	['id_country' => '163','code' => '262','name' => 'Nicaragua','abbreviation' => 'NIC','country_lan' => 'Nicaragua', 'is_deleted' => '0'],
          	['id_country' => '164','code' => '263','name' => 'Níger (el)','abbreviation' => 'NER','country_lan' => 'Níger (el)', 'is_deleted' => '0'],
          	['id_country' => '165','code' => '264','name' => 'Nigeria','abbreviation' => 'NGA','country_lan' => 'Nigeria', 'is_deleted' => '0'],
          	['id_country' => '166','code' => '265','name' => 'Niue','abbreviation' => 'NIU','country_lan' => 'Niue', 'is_deleted' => '0'],
          	['id_country' => '167','code' => '266','name' => 'Noruega','abbreviation' => 'NOR','country_lan' => 'Noruega', 'is_deleted' => '0'],
          	['id_country' => '168','code' => '267','name' => 'Nueva Caledonia','abbreviation' => 'NCL','country_lan' => 'Nueva Caledonia', 'is_deleted' => '0'],
          	['id_country' => '169','code' => '268','name' => 'Nueva Zelanda','abbreviation' => 'NZL','country_lan' => 'Nueva Zelanda', 'is_deleted' => '0'],
          	['id_country' => '170','code' => '269','name' => 'Omán','abbreviation' => 'OMN','country_lan' => 'Omán', 'is_deleted' => '0'],
          	['id_country' => '171','code' => '270','name' => 'Países Bajos (los)','abbreviation' => 'NLD','country_lan' => 'Países Bajos (los)', 'is_deleted' => '0'],
          	['id_country' => '172','code' => '271','name' => 'Pakistán','abbreviation' => 'PAK','country_lan' => 'Pakistán', 'is_deleted' => '0'],
          	['id_country' => '173','code' => '272','name' => 'Palaos','abbreviation' => 'PLW','country_lan' => 'Palaos', 'is_deleted' => '0'],
          	['id_country' => '174','code' => '273','name' => 'Palestina, Estado de','abbreviation' => 'PSE','country_lan' => 'Palestina, Estado de', 'is_deleted' => '0'],
          	['id_country' => '175','code' => '274','name' => 'Panamá','abbreviation' => 'PAN','country_lan' => 'Panamá', 'is_deleted' => '0'],
          	['id_country' => '176','code' => '275','name' => 'Papúa Nueva Guinea','abbreviation' => 'PNG','country_lan' => 'Papúa Nueva Guinea', 'is_deleted' => '0'],
          	['id_country' => '177','code' => '276','name' => 'Paraguay','abbreviation' => 'PRY','country_lan' => 'Paraguay', 'is_deleted' => '0'],
          	['id_country' => '178','code' => '277','name' => 'Perú','abbreviation' => 'PER','country_lan' => 'Perú', 'is_deleted' => '0'],
          	['id_country' => '179','code' => '278','name' => 'Polinesia Francesa','abbreviation' => 'PYF','country_lan' => 'Polinesia Francesa', 'is_deleted' => '0'],
          	['id_country' => '180','code' => '279','name' => 'Polonia','abbreviation' => 'POL','country_lan' => 'Polonia', 'is_deleted' => '0'],
          	['id_country' => '181','code' => '280','name' => 'Portugal','abbreviation' => 'PRT','country_lan' => 'Portugal', 'is_deleted' => '0'],
          	['id_country' => '182','code' => '281','name' => 'Puerto Rico','abbreviation' => 'PRI','country_lan' => 'Puerto Rico', 'is_deleted' => '0'],
          	['id_country' => '183','code' => '282','name' => 'Reino Unido (el)','abbreviation' => 'GBR','country_lan' => 'Reino Unido (el)', 'is_deleted' => '0'],
          	['id_country' => '184','code' => '283','name' => 'República Centroafricana (la)','abbreviation' => 'CAF','country_lan' => 'República Centroafricana (la)', 'is_deleted' => '0'],
          	['id_country' => '185','code' => '284','name' => 'República Checa (la)','abbreviation' => 'CZE','country_lan' => 'República Checa (la)', 'is_deleted' => '0'],
          	['id_country' => '186','code' => '285','name' => 'Macedonia (la antigua República Yugoslava de)','abbreviation' => 'MKD','country_lan' => 'Macedonia (la antigua República Yugoslava de)', 'is_deleted' => '0'],
          	['id_country' => '187','code' => '286','name' => 'Congo','abbreviation' => 'COG','country_lan' => 'Congo', 'is_deleted' => '0'],
          	['id_country' => '188','code' => '287','name' => 'Congo (la República Democrática del)','abbreviation' => 'COD','country_lan' => 'Congo (la República Democrática del)', 'is_deleted' => '0'],
          	['id_country' => '189','code' => '288','name' => 'República Dominicana (la)','abbreviation' => 'DOM','country_lan' => 'República Dominicana (la)', 'is_deleted' => '0'],
          	['id_country' => '190','code' => '289','name' => 'Reunión','abbreviation' => 'REU','country_lan' => 'Reunión', 'is_deleted' => '0'],
          	['id_country' => '191','code' => '290','name' => 'Ruanda','abbreviation' => 'RWA','country_lan' => 'Ruanda', 'is_deleted' => '0'],
          	['id_country' => '192','code' => '291','name' => 'Rumania','abbreviation' => 'ROU','country_lan' => 'Rumania', 'is_deleted' => '0'],
          	['id_country' => '193','code' => '292','name' => 'Rusia, (la) Federación de','abbreviation' => 'RUS','country_lan' => 'Rusia, (la) Federación de', 'is_deleted' => '0'],
          	['id_country' => '194','code' => '293','name' => 'Sahara Occidental','abbreviation' => 'ESH','country_lan' => 'Sahara Occidental', 'is_deleted' => '0'],
          	['id_country' => '195','code' => '294','name' => 'Samoa','abbreviation' => 'WSM','country_lan' => 'Samoa', 'is_deleted' => '0'],
          	['id_country' => '196','code' => '295','name' => 'Samoa Americana','abbreviation' => 'ASM','country_lan' => 'Samoa Americana', 'is_deleted' => '0'],
          	['id_country' => '197','code' => '296','name' => 'San Bartolomé','abbreviation' => 'BLM','country_lan' => 'San Bartolomé', 'is_deleted' => '0'],
          	['id_country' => '198','code' => '297','name' => 'San Cristóbal y Nieves','abbreviation' => 'KNA','country_lan' => 'San Cristóbal y Nieves', 'is_deleted' => '0'],
          	['id_country' => '199','code' => '298','name' => 'San Marino','abbreviation' => 'SMR','country_lan' => 'San Marino', 'is_deleted' => '0'],
          	['id_country' => '200','code' => '299','name' => 'San Martín (parte francesa)','abbreviation' => 'MAF','country_lan' => 'San Martín (parte francesa)', 'is_deleted' => '0'],
          	['id_country' => '201','code' => '300','name' => 'San Pedro y Miquelón','abbreviation' => 'SPM','country_lan' => 'San Pedro y Miquelón', 'is_deleted' => '0'],
          	['id_country' => '202','code' => '301','name' => 'San Vicente y las Granadinas','abbreviation' => 'VCT','country_lan' => 'San Vicente y las Granadinas', 'is_deleted' => '0'],
          	['id_country' => '203','code' => '302','name' => 'Santa Helena, Ascensión y Tristán de Acuña','abbreviation' => 'SHN','country_lan' => 'Santa Helena, Ascensión y Tristán de Acuña', 'is_deleted' => '0'],
          	['id_country' => '204','code' => '303','name' => 'Santa Lucía','abbreviation' => 'LCA','country_lan' => 'Santa Lucía', 'is_deleted' => '0'],
          	['id_country' => '205','code' => '304','name' => 'Santo Tomé y Príncipe','abbreviation' => 'STP','country_lan' => 'Santo Tomé y Príncipe', 'is_deleted' => '0'],
          	['id_country' => '206','code' => '305','name' => 'Senegal','abbreviation' => 'SEN','country_lan' => 'Senegal', 'is_deleted' => '0'],
          	['id_country' => '207','code' => '306','name' => 'Serbia','abbreviation' => 'SRB','country_lan' => 'Serbia', 'is_deleted' => '0'],
          	['id_country' => '208','code' => '307','name' => 'Seychelles','abbreviation' => 'SYC','country_lan' => 'Seychelles', 'is_deleted' => '0'],
          	['id_country' => '209','code' => '308','name' => 'Sierra leona','abbreviation' => 'SLE','country_lan' => 'Sierra leona', 'is_deleted' => '0'],
          	['id_country' => '210','code' => '309','name' => 'Singapur','abbreviation' => 'SGP','country_lan' => 'Singapur', 'is_deleted' => '0'],
          	['id_country' => '211','code' => '310','name' => 'Sint Maarten (parte holandesa)','abbreviation' => 'SXM','country_lan' => 'Sint Maarten (parte holandesa)', 'is_deleted' => '0'],
          	['id_country' => '212','code' => '311','name' => 'Siria, (la) República Árabe','abbreviation' => 'SYR','country_lan' => 'Siria, (la) República Árabe', 'is_deleted' => '0'],
          	['id_country' => '213','code' => '312','name' => 'Somalia','abbreviation' => 'SOM','country_lan' => 'Somalia', 'is_deleted' => '0'],
          	['id_country' => '214','code' => '313','name' => 'Sri Lanka','abbreviation' => 'LKA','country_lan' => 'Sri Lanka', 'is_deleted' => '0'],
          	['id_country' => '215','code' => '314','name' => 'Suazilandia','abbreviation' => 'SWZ','country_lan' => 'Suazilandia', 'is_deleted' => '0'],
          	['id_country' => '216','code' => '315','name' => 'Sudáfrica','abbreviation' => 'ZAF','country_lan' => 'Sudáfrica', 'is_deleted' => '0'],
          	['id_country' => '217','code' => '316','name' => 'Sudán (el)','abbreviation' => 'SDN','country_lan' => 'Sudán (el)', 'is_deleted' => '0'],
          	['id_country' => '218','code' => '317','name' => 'Sudán del Sur','abbreviation' => 'SSD','country_lan' => 'Sudán del Sur', 'is_deleted' => '0'],
          	['id_country' => '219','code' => '318','name' => 'Suecia','abbreviation' => 'SWE','country_lan' => 'Suecia', 'is_deleted' => '0'],
          	['id_country' => '220','code' => '319','name' => 'Suiza','abbreviation' => 'CHE','country_lan' => 'Suiza', 'is_deleted' => '0'],
          	['id_country' => '221','code' => '320','name' => 'Surinam','abbreviation' => 'SUR','country_lan' => 'Surinam', 'is_deleted' => '0'],
          	['id_country' => '222','code' => '321','name' => 'Svalbard y Jan Mayen','abbreviation' => 'SJM','country_lan' => 'Svalbard y Jan Mayen', 'is_deleted' => '0'],
          	['id_country' => '223','code' => '322','name' => 'Tailandia','abbreviation' => 'THA','country_lan' => 'Tailandia', 'is_deleted' => '0'],
          	['id_country' => '224','code' => '323','name' => 'Taiwán (Provincia de China)','abbreviation' => 'TWN','country_lan' => 'Taiwán (Provincia de China)', 'is_deleted' => '0'],
          	['id_country' => '225','code' => '324','name' => 'Tanzania, República Unida de','abbreviation' => 'TZA','country_lan' => 'Tanzania, República Unida de', 'is_deleted' => '0'],
          	['id_country' => '226','code' => '325','name' => 'Tayikistán','abbreviation' => 'TJK','country_lan' => 'Tayikistán', 'is_deleted' => '0'],
          	['id_country' => '227','code' => '326','name' => 'Territorio Británico del Océano Índico (el)','abbreviation' => 'IOT','country_lan' => 'Territorio Británico del Océano Índico (el)', 'is_deleted' => '0'],
          	['id_country' => '228','code' => '327','name' => 'Territorios Australes Franceses (los)','abbreviation' => 'ATF','country_lan' => 'Territorios Australes Franceses (los)', 'is_deleted' => '0'],
          	['id_country' => '229','code' => '328','name' => 'Timor-Leste','abbreviation' => 'TLS','country_lan' => 'Timor-Leste', 'is_deleted' => '0'],
          	['id_country' => '230','code' => '329','name' => 'Togo','abbreviation' => 'TGO','country_lan' => 'Togo', 'is_deleted' => '0'],
          	['id_country' => '231','code' => '330','name' => 'Tokelau','abbreviation' => 'TKL','country_lan' => 'Tokelau', 'is_deleted' => '0'],
          	['id_country' => '232','code' => '331','name' => 'Tonga','abbreviation' => 'TON','country_lan' => 'Tonga', 'is_deleted' => '0'],
          	['id_country' => '233','code' => '332','name' => 'Trinidad y Tobago','abbreviation' => 'TTO','country_lan' => 'Trinidad y Tobago', 'is_deleted' => '0'],
          	['id_country' => '234','code' => '333','name' => 'Túnez','abbreviation' => 'TUN','country_lan' => 'Túnez', 'is_deleted' => '0'],
          	['id_country' => '235','code' => '334','name' => 'Turkmenistán','abbreviation' => 'TKM','country_lan' => 'Turkmenistán', 'is_deleted' => '0'],
          	['id_country' => '236','code' => '335','name' => 'Turquía','abbreviation' => 'TUR','country_lan' => 'Turquía', 'is_deleted' => '0'],
          	['id_country' => '237','code' => '336','name' => 'Tuvalu','abbreviation' => 'TUV','country_lan' => 'Tuvalu', 'is_deleted' => '0'],
          	['id_country' => '238','code' => '337','name' => 'Ucrania','abbreviation' => 'UKR','country_lan' => 'Ucrania', 'is_deleted' => '0'],
          	['id_country' => '239','code' => '338','name' => 'Uganda','abbreviation' => 'UGA','country_lan' => 'Uganda', 'is_deleted' => '0'],
          	['id_country' => '240','code' => '339','name' => 'Uruguay','abbreviation' => 'URY','country_lan' => 'Uruguay', 'is_deleted' => '0'],
          	['id_country' => '241','code' => '340','name' => 'Uzbekistán','abbreviation' => 'UZB','country_lan' => 'Uzbekistán', 'is_deleted' => '0'],
          	['id_country' => '242','code' => '341','name' => 'Vanuatu','abbreviation' => 'VUT','country_lan' => 'Vanuatu', 'is_deleted' => '0'],
          	['id_country' => '243','code' => '342','name' => 'Santa Sede[Estado de la Ciudad del Vaticano] (la)','abbreviation' => 'VAT','country_lan' => 'Santa Sede[Estado de la Ciudad del Vaticano] (la)', 'is_deleted' => '0'],
          	['id_country' => '244','code' => '343','name' => 'Venezuela, República Bolivariana de','abbreviation' => 'VEN','country_lan' => 'Venezuela, República Bolivariana de', 'is_deleted' => '0'],
          	['id_country' => '245','code' => '344','name' => 'Viet Nam','abbreviation' => 'VNM','country_lan' => 'Viet Nam', 'is_deleted' => '0'],
          	['id_country' => '246','code' => '345','name' => 'Wallis y Futuna','abbreviation' => 'WLF','country_lan' => 'Wallis y Futuna', 'is_deleted' => '0'],
          	['id_country' => '247','code' => '346','name' => 'Yemen','abbreviation' => 'YEM','country_lan' => 'Yemen', 'is_deleted' => '0'],
          	['id_country' => '248','code' => '347','name' => 'Yibuti','abbreviation' => 'DJI','country_lan' => 'Yibuti', 'is_deleted' => '0'],
          	['id_country' => '249','code' => '348','name' => 'Zambia','abbreviation' => 'ZMB','country_lan' => 'Zambia', 'is_deleted' => '0'],
          	['id_country' => '250','code' => '349','name' => 'Zimbabue','abbreviation' => 'ZWE','country_lan' => 'Zimbabue', 'is_deleted' => '0'],
          	['id_country' => '251','code' => '350','name' => 'Países no declarados','abbreviation' => 'ZZZ','country_lan' => 'Países no declarados', 'is_deleted' => '0'],
          ]);

          Schema::connection($this->sConnection)->create('erps_country_states', function (blueprint $table) {
          	$table->increments('id_state');
          	$table->char('code', 10)->unique();
          	$table->char('name', 100);
          	$table->char('abbreviation', 50);
          	$table->char('state_lan', 100);
          	$table->boolean('is_deleted');
          	$table->integer('country_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('country_id')->references('id_country')->on('erps_countries')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erps_country_states')->insert([
          	['id_state' => '1','code' => 'NA','abbreviation' => 'Na.','name' => 'N/A','state_lan' => 'N/A', 'is_deleted' => '0','country_id' => '1'],
          	['id_state' => '2','code' => 'AGU','abbreviation' => 'Ags.','name' => 'Aguascalientes','state_lan' => 'Aguascalientes', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '3','code' => 'BCN','abbreviation' => 'B.C.','name' => 'Baja California','state_lan' => 'Baja California', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '4','code' => 'BCS','abbreviation' => 'B.C.S.','name' => 'Baja California Sur','state_lan' => 'Baja California Sur', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '5','code' => 'CAM','abbreviation' => 'Camp.','name' => 'Campeche','state_lan' => 'Campeche', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '6','code' => 'CHP','abbreviation' => 'Chis.','name' => 'Chiapas','state_lan' => 'Chiapas', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '7','code' => 'CHH','abbreviation' => 'Chih.','name' => 'Chihuahua','state_lan' => 'Chihuahua', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '8','code' => 'COA','abbreviation' => 'Coah.','name' => 'Coahuila','state_lan' => 'Coahuila', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '9','code' => 'COL','abbreviation' => 'Col.','name' => 'Colima','state_lan' => 'Colima', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '10','code' => 'DIF','abbreviation' => 'CDMX','name' => 'Ciudad de México','state_lan' => 'Ciudad de México', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '11','code' => 'DUR','abbreviation' => 'Dgo.','name' => 'Durango','state_lan' => 'Durango', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '12','code' => 'GUA','abbreviation' => 'Gto.','name' => 'Guanajuato','state_lan' => 'Guanajuato', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '13','code' => 'GRO','abbreviation' => 'Gro.','name' => 'Guerrero','state_lan' => 'Guerrero', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '14','code' => 'HID','abbreviation' => 'Hgo.','name' => 'Hidalgo','state_lan' => 'Hidalgo', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '15','code' => 'JAL','abbreviation' => 'Jal.','name' => 'Jalisco','state_lan' => 'Jalisco', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '16','code' => 'MEX','abbreviation' => 'Méx.','name' => 'Estado de México','state_lan' => 'Estado de México', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '17','code' => 'MIC','abbreviation' => 'Mich.','name' => 'Michoacán','state_lan' => 'Michoacán', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '18','code' => 'MOR','abbreviation' => 'Mor.','name' => 'Morelos','state_lan' => 'Morelos', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '19','code' => 'NAY','abbreviation' => 'Nay.','name' => 'Nayarit','state_lan' => 'Nayarit', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '20','code' => 'NLE','abbreviation' => 'N.L.','name' => 'Nuevo León','state_lan' => 'Nuevo León', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '21','code' => 'OAX','abbreviation' => 'Oax.','name' => 'Oaxaca','state_lan' => 'Oaxaca', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '22','code' => 'PUE','abbreviation' => 'Pue.','name' => 'Puebla','state_lan' => 'Puebla', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '23','code' => 'QUE','abbreviation' => 'Qro.','name' => 'Querétaro','state_lan' => 'Querétaro', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '24','code' => 'ROO','abbreviation' => 'Q.R.','name' => 'Quintana Roo','state_lan' => 'Quintana Roo', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '25','code' => 'SLP','abbreviation' => 'S.L.P.','name' => 'San Luis Potosí','state_lan' => 'San Luis Potosí', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '26','code' => 'SIN','abbreviation' => 'Sin.','name' => 'Sinaloa','state_lan' => 'Sinaloa', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '27','code' => 'SON','abbreviation' => 'Son.','name' => 'Sonora','state_lan' => 'Sonora', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '28','code' => 'TAB','abbreviation' => 'Tab.','name' => 'Tabasco','state_lan' => 'Tabasco', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '29','code' => 'TAM','abbreviation' => 'Tamps.','name' => 'Tamaulipas','state_lan' => 'Tamaulipas', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '30','code' => 'TLA','abbreviation' => 'Tlax.','name' => 'Tlaxcala','state_lan' => 'Tlaxcala', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '31','code' => 'VER','abbreviation' => 'Ver.','name' => 'Veracruz','state_lan' => 'Veracruz', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '32','code' => 'YUC','abbreviation' => 'Yuc.','name' => 'Yucatán','state_lan' => 'Yucatán', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '33','code' => 'ZAC','abbreviation' => 'Zac.','name' => 'Zacatecas','state_lan' => 'Zacatecas', 'is_deleted' => '0','country_id' => '152'],
          	['id_state' => '34','code' => 'AL','abbreviation' => 'AL','name' => 'Alabama','state_lan' => 'Alabama', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '35','code' => 'AK','abbreviation' => 'AK','name' => 'Alaska','state_lan' => 'Alaska', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '36','code' => 'AZ','abbreviation' => 'AZ','name' => 'Arizona','state_lan' => 'Arizona', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '37','code' => 'AR','abbreviation' => 'AR','name' => 'Arkansas','state_lan' => 'Arkansas', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '38','code' => 'CA','abbreviation' => 'CA','name' => 'California','state_lan' => 'California', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '39','code' => 'NC','abbreviation' => 'NC','name' => 'Carolina del Norte','state_lan' => 'North Carolina', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '40','code' => 'SC','abbreviation' => 'SC','name' => 'Carolina del Sur','state_lan' => 'South Carolina', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '41','code' => 'CO','abbreviation' => 'CO','name' => 'Colorado','state_lan' => 'Colorado', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '42','code' => 'CT','abbreviation' => 'CT','name' => 'Connecticut','state_lan' => 'Connecticut', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '43','code' => 'ND','abbreviation' => 'ND','name' => 'Dakota del Norte','state_lan' => 'North Dakota', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '44','code' => 'SD','abbreviation' => 'SD','name' => 'Dakota del Sur','state_lan' => 'South Dakota', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '45','code' => 'DE','abbreviation' => 'DE','name' => 'Delaware','state_lan' => 'Delaware', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '46','code' => 'FL','abbreviation' => 'FL','name' => 'Florida','state_lan' => 'Florida', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '47','code' => 'GA','abbreviation' => 'GA','name' => 'Georgia','state_lan' => 'Georgia', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '48','code' => 'HI','abbreviation' => 'HI','name' => 'Hawái','state_lan' => 'Hawaii', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '49','code' => 'ID','abbreviation' => 'ID','name' => 'Idaho','state_lan' => 'Idaho', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '50','code' => 'IL','abbreviation' => 'IL','name' => 'Illinois','state_lan' => 'Illinois', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '51','code' => 'IN','abbreviation' => 'IN','name' => 'Indiana','state_lan' => 'Indiana', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '52','code' => 'IA','abbreviation' => 'IA','name' => 'Iowa','state_lan' => 'Iowa', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '53','code' => 'KS','abbreviation' => 'KS','name' => 'Kansas','state_lan' => 'Kansas', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '54','code' => 'KY','abbreviation' => 'KY','name' => 'Kentucky','state_lan' => 'Kentucky', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '55','code' => 'LA','abbreviation' => 'LA','name' => 'Luisiana','state_lan' => 'Louisiana', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '56','code' => 'ME','abbreviation' => 'ME','name' => 'Maine','state_lan' => 'Maine', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '57','code' => 'MD','abbreviation' => 'MD','name' => 'Maryland','state_lan' => 'Maryland', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '58','code' => 'MA','abbreviation' => 'MA','name' => 'Massachusetts','state_lan' => 'Massachusetts', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '59','code' => 'MI','abbreviation' => 'MI','name' => 'Míchigan','state_lan' => 'Michigan', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '60','code' => 'MN','abbreviation' => 'MN','name' => 'Minnesota','state_lan' => 'Minnesota', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '61','code' => 'MS','abbreviation' => 'MS','name' => 'Misisipi','state_lan' => 'Mississippi', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '62','code' => 'MO','abbreviation' => 'MO','name' => 'Misuri','state_lan' => 'Missouri', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '63','code' => 'MT','abbreviation' => 'MT','name' => 'Montana','state_lan' => 'Montana', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '64','code' => 'NE','abbreviation' => 'NE','name' => 'Nebraska','state_lan' => 'Nebraska', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '65','code' => 'NV','abbreviation' => 'NV','name' => 'Nevada','state_lan' => 'Nevada', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '66','code' => 'NJ','abbreviation' => 'NJ','name' => 'Nueva Jersey','state_lan' => 'New Jersey', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '67','code' => 'NY','abbreviation' => 'NY','name' => 'Nueva York','state_lan' => 'New York', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '68','code' => 'NH','abbreviation' => 'NH','name' => 'Nuevo Hampshire','state_lan' => 'New Hampshire', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '69','code' => 'NM','abbreviation' => 'NM','name' => 'Nuevo México','state_lan' => 'New Mexico', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '70','code' => 'OH','abbreviation' => 'OH','name' => 'Ohio','state_lan' => 'Ohio', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '71','code' => 'OK','abbreviation' => 'OK','name' => 'Oklahoma','state_lan' => 'Oklahoma', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '72','code' => 'OR','abbreviation' => 'OR','name' => 'Oregón','state_lan' => 'Oregon', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '73','code' => 'PA','abbreviation' => 'PA','name' => 'Pensilvania','state_lan' => 'Pennsylvania', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '74','code' => 'RI','abbreviation' => 'RI','name' => 'Rhode Island','state_lan' => 'Rhode Island', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '75','code' => 'TN','abbreviation' => 'TN','name' => 'Tennessee','state_lan' => 'Tennessee', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '76','code' => 'TX','abbreviation' => 'TX','name' => 'Texas','state_lan' => 'Texas', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '77','code' => 'UT','abbreviation' => 'UT','name' => 'Utah','state_lan' => 'Utah', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '78','code' => 'VT','abbreviation' => 'VT','name' => 'Vermont','state_lan' => 'Vermont', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '79','code' => 'VA','abbreviation' => 'VA','name' => 'Virginia','state_lan' => 'Virginia', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '80','code' => 'WV','abbreviation' => 'WV','name' => 'Virginia Occidental','state_lan' => 'West Virginia', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '81','code' => 'WA','abbreviation' => 'WA','name' => 'Washington','state_lan' => 'Washington', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '82','code' => 'WI','abbreviation' => 'WI','name' => 'Wisconsin','state_lan' => 'Wisconsin', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '83','code' => 'WY','abbreviation' => 'WY','name' => 'Wyoming','state_lan' => 'Wyoming', 'is_deleted' => '0','country_id' => '67'],
          	['id_state' => '84','code' => 'ON','abbreviation' => 'ON','name' => 'Ontario ','state_lan' => 'Ontario', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '85','code' => 'QC','abbreviation' => 'QC','name' => 'Quebec','state_lan' => 'Quebec', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '86','code' => 'NS','abbreviation' => 'NS','name' => 'Nueva Escocia','state_lan' => 'Nova Scotia', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '87','code' => 'NB','abbreviation' => 'NB','name' => 'Nuevo Brunswick','state_lan' => 'New Brunswick', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '88','code' => 'MB','abbreviation' => 'MB','name' => 'Manitoba','state_lan' => 'Manitoba', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '89','code' => 'BC','abbreviation' => 'BC','name' => 'Columbia Británica','state_lan' => 'British Columbia', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '90','code' => 'PE','abbreviation' => 'PE','name' => 'Isla del Príncipe Eduardo','state_lan' => 'Prince Edward Island', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '91','code' => 'SK','abbreviation' => 'SK','name' => 'Saskatchewan','state_lan' => 'Saskatchewan', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '92','code' => 'AB','abbreviation' => 'AB','name' => 'Alberta','state_lan' => 'Alberta', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '93','code' => 'NL','abbreviation' => 'NL','name' => 'Terranova y Labrador','state_lan' => 'Newfoundland and Labrador', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '94','code' => 'NT','abbreviation' => 'NT','name' => 'Territorios del Noroeste','state_lan' => 'Northwest Territories', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '95','code' => 'YT','abbreviation' => 'YT','name' => 'Yukón','state_lan' => 'Yukon', 'is_deleted' => '0','country_id' => '41'],
          	['id_state' => '96','code' => 'UN','abbreviation' => 'NU','name' => 'Nunavut','state_lan' => 'Nunavut', 'is_deleted' => '0','country_id' => '41'],
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

          Schema::connection($this->sConnection)->drop('erps_country_states');
          Schema::connection($this->sConnection)->drop('erps_countries');
        }
    }
}
