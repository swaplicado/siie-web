<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Database\OTF;
use App\Database\Config;
use App\SUtils\SUtil;

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
          SUtil::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

          Schema::connection($this->sConnection)->create('erps_countries', function (blueprint $table) {
          	$table->increments('id_country');
          	$table->integer('key');
          	$table->char('code', 50)->unique();
          	$table->char('abbreviation', 50);
          	$table->char('name', 100);
          	$table->char('cty_lan', 100);
          	$table->boolean('is_deleted');
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });


          DB::connection($this->sConnection)->table('erps_countries')->insert([
          	['id_country' => '1','key' => '101','code' => 'AFG','abbreviation' => 'AFG','name' => 'Afganistán','cty_lan' => 'Afghanistan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '2','key' => '102','code' => 'ALA','abbreviation' => 'ALA','name' => 'Islas Åland','cty_lan' => 'Aland Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '3','key' => '103','code' => 'ALB','abbreviation' => 'ALB','name' => 'Albania','cty_lan' => 'Albania', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '4','key' => '104','code' => 'DEU','abbreviation' => 'DEU','name' => 'Alemania','cty_lan' => 'Germany', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '5','key' => '105','code' => 'AND','abbreviation' => 'AND','name' => 'Andorra','cty_lan' => 'Andorra', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '6','key' => '106','code' => 'AGO','abbreviation' => 'AGO','name' => 'Angola','cty_lan' => 'Angola', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '7','key' => '107','code' => 'AIA','abbreviation' => 'AIA','name' => 'Anguila','cty_lan' => 'Anguilla', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '8','key' => '108','code' => 'ATA','abbreviation' => 'ATA','name' => 'Antártida','cty_lan' => 'Antarctica', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '9','key' => '109','code' => 'ATG','abbreviation' => 'ATG','name' => 'Antigua y Barbuda','cty_lan' => 'Antigua and Barbuda', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '10','key' => '110','code' => 'SAU','abbreviation' => 'SAU','name' => 'Arabia Saudita','cty_lan' => 'Saudi Arabia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '11','key' => '111','code' => 'DZA','abbreviation' => 'DZA','name' => 'Argelia','cty_lan' => 'Algeria', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '12','key' => '112','code' => 'ARG','abbreviation' => 'ARG','name' => 'Argentina','cty_lan' => 'Argentina', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '13','key' => '113','code' => 'ARM','abbreviation' => 'ARM','name' => 'Armenia','cty_lan' => 'Armenia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '14','key' => '114','code' => 'ABW','abbreviation' => 'ABW','name' => 'Aruba','cty_lan' => 'Aruba', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '15','key' => '115','code' => 'AUS','abbreviation' => 'AUS','name' => 'Australia','cty_lan' => 'Australia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '16','key' => '116','code' => 'AUT','abbreviation' => 'AUT','name' => 'Austria','cty_lan' => 'Austria', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '17','key' => '117','code' => 'AZE','abbreviation' => 'AZE','name' => 'Azerbaiyán','cty_lan' => 'Azerbaijan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '18','key' => '118','code' => 'BHS','abbreviation' => 'BHS','name' => 'Bahamas (las)','cty_lan' => 'Bahamas', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '19','key' => '119','code' => 'BGD','abbreviation' => 'BGD','name' => 'Bangladés','cty_lan' => 'Bangladesh', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '20','key' => '120','code' => 'BRB','abbreviation' => 'BRB','name' => 'Barbados','cty_lan' => 'Barbados', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '21','key' => '121','code' => 'BHR','abbreviation' => 'BHR','name' => 'Baréin','cty_lan' => 'Bahrain', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '22','key' => '122','code' => 'BEL','abbreviation' => 'BEL','name' => 'Bélgica','cty_lan' => 'Belgium', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '23','key' => '123','code' => 'BLZ','abbreviation' => 'BLZ','name' => 'Belice','cty_lan' => 'Belize', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '24','key' => '124','code' => 'BEN','abbreviation' => 'BEN','name' => 'Benín','cty_lan' => 'Benin', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '25','key' => '125','code' => 'BMU','abbreviation' => 'BMU','name' => 'Bermudas','cty_lan' => 'Bermuda', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '26','key' => '126','code' => 'BLR','abbreviation' => 'BLR','name' => 'Bielorrusia','cty_lan' => 'Belarus', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '27','key' => '127','code' => 'MMR','abbreviation' => 'MMR','name' => 'Myanmar','cty_lan' => 'Myanmar', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '28','key' => '128','code' => 'BOL','abbreviation' => 'BOL','name' => 'Bolivia, Estado Plurinacional de','cty_lan' => 'Bolivia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '29','key' => '129','code' => 'BIH','abbreviation' => 'BIH','name' => 'Bosnia y Herzegovina','cty_lan' => 'Bosnia and Herzegovina', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '30','key' => '130','code' => 'BWA','abbreviation' => 'BWA','name' => 'Botsuana','cty_lan' => 'Botswana', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '31','key' => '131','code' => 'BRA','abbreviation' => 'BRA','name' => 'Brasil','cty_lan' => 'Brazil', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '32','key' => '132','code' => 'BRN','abbreviation' => 'BRN','name' => 'Brunéi Darussalam','cty_lan' => 'Brunei Darussalam', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '33','key' => '133','code' => 'BGR','abbreviation' => 'BGR','name' => 'Bulgaria','cty_lan' => 'Bulgaria', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '34','key' => '134','code' => 'BFA','abbreviation' => 'BFA','name' => 'Burkina Faso','cty_lan' => 'Burkina Faso', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '35','key' => '135','code' => 'BDI','abbreviation' => 'BDI','name' => 'Burundi','cty_lan' => 'Burundi', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '36','key' => '136','code' => 'BTN','abbreviation' => 'BTN','name' => 'Bután','cty_lan' => 'Bhutan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '37','key' => '137','code' => 'CPV','abbreviation' => 'CPV','name' => 'Cabo Verde','cty_lan' => 'Cape Verde', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '38','key' => '138','code' => 'KHM','abbreviation' => 'KHM','name' => 'Camboya','cty_lan' => 'Cambodia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '39','key' => '139','code' => 'CMR','abbreviation' => 'CMR','name' => 'Camerún','cty_lan' => 'Cameroon', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '40','key' => '140','code' => 'CAN','abbreviation' => 'CAN','name' => 'Canadá','cty_lan' => 'Canada', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '41','key' => '141','code' => 'QAT','abbreviation' => 'QAT','name' => 'Catar','cty_lan' => 'Qatar', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '42','key' => '142','code' => 'BES','abbreviation' => 'BES','name' => 'Bonaire, San Eustaquio y Saba','cty_lan' => 'Bonaire, San Eustaquio y Saba', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '43','key' => '143','code' => 'TCD','abbreviation' => 'TCD','name' => 'Chad','cty_lan' => 'Chad', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '44','key' => '144','code' => 'CHL','abbreviation' => 'CHL','name' => 'Chile','cty_lan' => 'Chile', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '45','key' => '145','code' => 'CHN','abbreviation' => 'CHN','name' => 'China','cty_lan' => 'China', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '46','key' => '146','code' => 'CYP','abbreviation' => 'CYP','name' => 'Chipre','cty_lan' => 'Cyprus', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '47','key' => '147','code' => 'COL','abbreviation' => 'COL','name' => 'Colombia','cty_lan' => 'Colombia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '48','key' => '148','code' => 'COM','abbreviation' => 'COM','name' => 'Comoras','cty_lan' => 'Comoros', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '49','key' => '149','code' => 'PRK','abbreviation' => 'PRK','name' => 'Corea (la República Democrática Popular de)','cty_lan' => 'Korea, Democratic Peoples Republic of', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '50','key' => '150','code' => 'KOR','abbreviation' => 'KOR','name' => 'Corea (la República de)','cty_lan' => 'Korea, Republic of', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '51','key' => '151','code' => 'CIV','abbreviation' => 'CIV','name' => 'Côte dIvoire','cty_lan' => 'Côte dIvoire', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '52','key' => '152','code' => 'CRI','abbreviation' => 'CRI','name' => 'Costa Rica','cty_lan' => 'Costa Rica', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '53','key' => '153','code' => 'HRV','abbreviation' => 'HRV','name' => 'Croacia','cty_lan' => 'Croatia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '54','key' => '154','code' => 'CUB','abbreviation' => 'CUB','name' => 'Cuba','cty_lan' => 'Cuba', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '55','key' => '155','code' => 'CUW','abbreviation' => 'CUW','name' => 'Curaçao','cty_lan' => 'Curaçao', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '56','key' => '156','code' => 'DNK','abbreviation' => 'DNK','name' => 'Dinamarca','cty_lan' => 'Denmark', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '57','key' => '157','code' => 'DMA','abbreviation' => 'DMA','name' => 'Dominica','cty_lan' => 'Dominica', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '58','key' => '158','code' => 'ECU','abbreviation' => 'ECU','name' => 'Ecuador','cty_lan' => 'Ecuador', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '59','key' => '159','code' => 'EGY','abbreviation' => 'EGY','name' => 'Egipto','cty_lan' => 'Egypt', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '60','key' => '160','code' => 'SLV','abbreviation' => 'SLV','name' => 'El Salvador','cty_lan' => 'El Salvador', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '61','key' => '161','code' => 'ARE','abbreviation' => 'ARE','name' => 'Emiratos Árabes Unidos (Los)','cty_lan' => 'United Arab Emirates', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '62','key' => '162','code' => 'ERI','abbreviation' => 'ERI','name' => 'Eritrea','cty_lan' => 'Eritrea', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '63','key' => '163','code' => 'SVK','abbreviation' => 'SVK','name' => 'Eslovaquia','cty_lan' => 'Slovakia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '64','key' => '164','code' => 'SVN','abbreviation' => 'SVN','name' => 'Eslovenia','cty_lan' => 'Slovenia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '65','key' => '165','code' => 'ESP','abbreviation' => 'ESP','name' => 'España','cty_lan' => 'Spain', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '66','key' => '166','code' => 'USA','abbreviation' => 'USA','name' => 'Estados Unidos (los)','cty_lan' => 'United States of America', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '67','key' => '167','code' => 'EST','abbreviation' => 'EST','name' => 'Estonia','cty_lan' => 'Estonia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '68','key' => '168','code' => 'ETH','abbreviation' => 'ETH','name' => 'Etiopía','cty_lan' => 'Ethiopia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '69','key' => '169','code' => 'PHL','abbreviation' => 'PHL','name' => 'Filipinas (las)','cty_lan' => 'Philippines', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '70','key' => '170','code' => 'FIN','abbreviation' => 'FIN','name' => 'Finlandia','cty_lan' => 'Finland', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '71','key' => '171','code' => 'FJI','abbreviation' => 'FJI','name' => 'Fiyi','cty_lan' => 'Fiji', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '72','key' => '172','code' => 'FRA','abbreviation' => 'FRA','name' => 'Francia','cty_lan' => 'France', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '73','key' => '173','code' => 'GAB','abbreviation' => 'GAB','name' => 'Gabón','cty_lan' => 'Gabon', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '74','key' => '174','code' => 'GMB','abbreviation' => 'GMB','name' => 'Gambia (La)','cty_lan' => 'Gambia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '75','key' => '175','code' => 'GEO','abbreviation' => 'GEO','name' => 'Georgia','cty_lan' => 'Georgia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '76','key' => '176','code' => 'GHA','abbreviation' => 'GHA','name' => 'Ghana','cty_lan' => 'Ghana', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '77','key' => '177','code' => 'GIB','abbreviation' => 'GIB','name' => 'Gibraltar','cty_lan' => 'Gibraltar', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '78','key' => '178','code' => 'GRD','abbreviation' => 'GRD','name' => 'Granada','cty_lan' => 'Grenada', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '79','key' => '179','code' => 'GRC','abbreviation' => 'GRC','name' => 'Grecia','cty_lan' => 'Greece', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '80','key' => '180','code' => 'GRL','abbreviation' => 'GRL','name' => 'Groenlandia','cty_lan' => 'Greenland', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '81','key' => '181','code' => 'GLP','abbreviation' => 'GLP','name' => 'Guadalupe','cty_lan' => 'Guadeloupe', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '82','key' => '182','code' => 'GUM','abbreviation' => 'GUM','name' => 'Guam','cty_lan' => 'Guam', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '83','key' => '183','code' => 'GTM','abbreviation' => 'GTM','name' => 'Guatemala','cty_lan' => 'Guatemala', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '84','key' => '184','code' => 'GUF','abbreviation' => 'GUF','name' => 'Guayana Francesa','cty_lan' => 'French Guiana', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '85','key' => '185','code' => 'GGY','abbreviation' => 'GGY','name' => 'Guernsey','cty_lan' => 'Guernsey', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '86','key' => '186','code' => 'GIN','abbreviation' => 'GIN','name' => 'Guinea','cty_lan' => 'Guinea', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '87','key' => '187','code' => 'GNB','abbreviation' => 'GNB','name' => 'Guinea-Bisáu','cty_lan' => 'Guinea-Bissau', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '88','key' => '188','code' => 'GNQ','abbreviation' => 'GNQ','name' => 'Guinea Ecuatorial','cty_lan' => 'Equatorial Guinea', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '89','key' => '189','code' => 'GUY','abbreviation' => 'GUY','name' => 'Guyana','cty_lan' => 'Guyana', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '90','key' => '190','code' => 'HTI','abbreviation' => 'HTI','name' => 'Haití','cty_lan' => 'Haiti', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '91','key' => '191','code' => 'HND','abbreviation' => 'HND','name' => 'Honduras','cty_lan' => 'Honduras', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '92','key' => '192','code' => 'HKG','abbreviation' => 'HKG','name' => 'Hong Kong','cty_lan' => 'Hong Kong, Special Administrative Region of China', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '93','key' => '193','code' => 'HUN','abbreviation' => 'HUN','name' => 'Hungría','cty_lan' => 'Hungary', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '94','key' => '194','code' => 'IND','abbreviation' => 'IND','name' => 'India','cty_lan' => 'India', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '95','key' => '195','code' => 'IDN','abbreviation' => 'IDN','name' => 'Indonesia','cty_lan' => 'Indonesia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '96','key' => '196','code' => 'IRQ','abbreviation' => 'IRQ','name' => 'Irak','cty_lan' => 'Iraq', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '97','key' => '197','code' => 'IRN','abbreviation' => 'IRN','name' => 'Irán (la República Islámica de)','cty_lan' => 'Iran, Islamic Republic of', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '98','key' => '198','code' => 'IRL','abbreviation' => 'IRL','name' => 'Irlanda','cty_lan' => 'Ireland', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '99','key' => '199','code' => 'BVT','abbreviation' => 'BVT','name' => 'Isla Bouvet','cty_lan' => 'Bouvet Island', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '100','key' => '200','code' => 'IMN','abbreviation' => 'IMN','name' => 'Isla de Man','cty_lan' => 'Isle of Man', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '101','key' => '201','code' => 'CXR','abbreviation' => 'CXR','name' => 'Isla de Navidad','cty_lan' => 'Christmas Island', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '102','key' => '202','code' => 'NFK','abbreviation' => 'NFK','name' => 'Isla Norfolk','cty_lan' => 'Norfolk Island', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '103','key' => '203','code' => 'ISL','abbreviation' => 'ISL','name' => 'Islandia','cty_lan' => 'Iceland', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '104','key' => '204','code' => 'CYM','abbreviation' => 'CYM','name' => 'Islas Caimán (las)','cty_lan' => 'Cayman Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '105','key' => '205','code' => 'CCK','abbreviation' => 'CCK','name' => 'Islas Cocos (Keeling)','cty_lan' => 'Cocos (Keeling) Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '106','key' => '206','code' => 'COK','abbreviation' => 'COK','name' => 'Islas Cook (las)','cty_lan' => 'Cook Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '107','key' => '207','code' => 'FRO','abbreviation' => 'FRO','name' => 'Islas Feroe (las)','cty_lan' => 'Faroe Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '108','key' => '208','code' => 'SGS','abbreviation' => 'SGS','name' => 'Georgia del sur y las islas sandwich del sur','cty_lan' => 'South Georgia and the South Sandwich Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '109','key' => '209','code' => 'HMD','abbreviation' => 'HMD','name' => 'Isla Heard e Islas McDonald','cty_lan' => 'Heard Island and Mcdonald Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '110','key' => '210','code' => 'FLK','abbreviation' => 'FLK','name' => 'Islas Malvinas [Falkland] (las)','cty_lan' => 'Falkland Islands (Malvinas)', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '111','key' => '211','code' => 'MNP','abbreviation' => 'MNP','name' => 'Islas Marianas del Norte (las)','cty_lan' => 'Northern Mariana Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '112','key' => '212','code' => 'MHL','abbreviation' => 'MHL','name' => 'Islas Marshall (las)','cty_lan' => 'Marshall Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '113','key' => '213','code' => 'PCN','abbreviation' => 'PCN','name' => 'Pitcairn','cty_lan' => 'Pitcairn', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '114','key' => '214','code' => 'SLB','abbreviation' => 'SLB','name' => 'Islas Salomón (las)','cty_lan' => 'Solomon Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '115','key' => '215','code' => 'TCA','abbreviation' => 'TCA','name' => 'Islas Turcas y Caicos (las)','cty_lan' => 'Turks and Caicos Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '116','key' => '216','code' => 'UMI','abbreviation' => 'UMI','name' => 'Islas de Ultramar Menores de Estados Unidos (las)','cty_lan' => 'United States Minor Outlying Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '117','key' => '217','code' => 'VGB','abbreviation' => 'VGB','name' => 'Islas Vírgenes (Británicas)','cty_lan' => 'British Virgin Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '118','key' => '218','code' => 'VIR','abbreviation' => 'VIR','name' => 'Islas Vírgenes (EE.UU.)','cty_lan' => 'Virgin Islands, US', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '119','key' => '219','code' => 'ISR','abbreviation' => 'ISR','name' => 'Israel','cty_lan' => 'Israel', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '120','key' => '220','code' => 'ITA','abbreviation' => 'ITA','name' => 'Italia','cty_lan' => 'Italy', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '121','key' => '221','code' => 'JAM','abbreviation' => 'JAM','name' => 'Jamaica','cty_lan' => 'Jamaica', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '122','key' => '222','code' => 'JPN','abbreviation' => 'JPN','name' => 'Japón','cty_lan' => 'Japan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '123','key' => '223','code' => 'JEY','abbreviation' => 'JEY','name' => 'Jersey','cty_lan' => 'Jersey', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '124','key' => '224','code' => 'JOR','abbreviation' => 'JOR','name' => 'Jordania','cty_lan' => 'Jordan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '125','key' => '225','code' => 'KAZ','abbreviation' => 'KAZ','name' => 'Kazajistán','cty_lan' => 'Kazakhstan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '126','key' => '226','code' => 'KEN','abbreviation' => 'KEN','name' => 'Kenia','cty_lan' => 'Kenya', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '127','key' => '227','code' => 'KGZ','abbreviation' => 'KGZ','name' => 'Kirguistán','cty_lan' => 'Kyrgyzstan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '128','key' => '228','code' => 'KIR','abbreviation' => 'KIR','name' => 'Kiribati','cty_lan' => 'Kiribati', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '129','key' => '229','code' => 'KWT','abbreviation' => 'KWT','name' => 'Kuwait','cty_lan' => 'Kuwait', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '130','key' => '230','code' => 'LAO','abbreviation' => 'LAO','name' => 'Lao, (la) República Democrática Popular','cty_lan' => 'Lao PDR', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '131','key' => '231','code' => 'LSO','abbreviation' => 'LSO','name' => 'Lesoto','cty_lan' => 'Lesotho', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '132','key' => '232','code' => 'LVA','abbreviation' => 'LVA','name' => 'Letonia','cty_lan' => 'Latvia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '133','key' => '233','code' => 'LBN','abbreviation' => 'LBN','name' => 'Líbano','cty_lan' => 'Lebanon', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '134','key' => '234','code' => 'LBR','abbreviation' => 'LBR','name' => 'Liberia','cty_lan' => 'Liberia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '135','key' => '235','code' => 'LBY','abbreviation' => 'LBY','name' => 'Libia','cty_lan' => 'Libya', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '136','key' => '236','code' => 'LIE','abbreviation' => 'LIE','name' => 'Liechtenstein','cty_lan' => 'Liechtenstein', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '137','key' => '237','code' => 'LTU','abbreviation' => 'LTU','name' => 'Lituania','cty_lan' => 'Lithuania', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '138','key' => '238','code' => 'LUX','abbreviation' => 'LUX','name' => 'Luxemburgo','cty_lan' => 'Luxembourg', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '139','key' => '239','code' => 'MAC','abbreviation' => 'MAC','name' => 'Macao','cty_lan' => 'Macao, Special Administrative Region of China', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '140','key' => '240','code' => 'MDG','abbreviation' => 'MDG','name' => 'Madagascar','cty_lan' => 'Madagascar', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '141','key' => '241','code' => 'MYS','abbreviation' => 'MYS','name' => 'Malasia','cty_lan' => 'Malaysia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '142','key' => '242','code' => 'MWI','abbreviation' => 'MWI','name' => 'Malaui','cty_lan' => 'Malawi', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '143','key' => '243','code' => 'MDV','abbreviation' => 'MDV','name' => 'Maldivas','cty_lan' => 'Maldives', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '144','key' => '244','code' => 'MLI','abbreviation' => 'MLI','name' => 'Malí','cty_lan' => 'Mali', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '145','key' => '245','code' => 'MLT','abbreviation' => 'MLT','name' => 'Malta','cty_lan' => 'Malta', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '146','key' => '246','code' => 'MAR','abbreviation' => 'MAR','name' => 'Marruecos','cty_lan' => 'Morocco', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '147','key' => '247','code' => 'MTQ','abbreviation' => 'MTQ','name' => 'Martinica','cty_lan' => 'Martinique', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '148','key' => '248','code' => 'MUS','abbreviation' => 'MUS','name' => 'Mauricio','cty_lan' => 'Mauritius', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '149','key' => '249','code' => 'MRT','abbreviation' => 'MRT','name' => 'Mauritania','cty_lan' => 'Mauritania', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '150','key' => '250','code' => 'MYT','abbreviation' => 'MYT','name' => 'Mayotte','cty_lan' => 'Mayotte', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '151','key' => '251','code' => 'MEX','abbreviation' => 'MEX','name' => 'México','cty_lan' => 'Mexico', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '152','key' => '252','code' => 'FSM','abbreviation' => 'FSM','name' => 'Micronesia (los Estados Federados de)','cty_lan' => 'Micronesia, Federated States of', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '153','key' => '253','code' => 'MDA','abbreviation' => 'MDA','name' => 'Moldavia (la República de)','cty_lan' => 'Moldova', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '154','key' => '254','code' => 'MCO','abbreviation' => 'MCO','name' => 'Mónaco','cty_lan' => 'Monaco', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '155','key' => '255','code' => 'MNG','abbreviation' => 'MNG','name' => 'Mongolia','cty_lan' => 'Mongolia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '156','key' => '256','code' => 'MNE','abbreviation' => 'MNE','name' => 'Montenegro','cty_lan' => 'Montenegro', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '157','key' => '257','code' => 'MSR','abbreviation' => 'MSR','name' => 'Montserrat','cty_lan' => 'Montserrat', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '158','key' => '258','code' => 'MOZ','abbreviation' => 'MOZ','name' => 'Mozambique','cty_lan' => 'Mozambique', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '159','key' => '259','code' => 'NAM','abbreviation' => 'NAM','name' => 'Namibia','cty_lan' => 'Namibia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '160','key' => '260','code' => 'NRU','abbreviation' => 'NRU','name' => 'Nauru','cty_lan' => 'Nauru', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '161','key' => '261','code' => 'NPL','abbreviation' => 'NPL','name' => 'Nepal','cty_lan' => 'Nepal', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '162','key' => '262','code' => 'NIC','abbreviation' => 'NIC','name' => 'Nicaragua','cty_lan' => 'Nicaragua', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '163','key' => '263','code' => 'NER','abbreviation' => 'NER','name' => 'Níger (el)','cty_lan' => 'Niger', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '164','key' => '264','code' => 'NGA','abbreviation' => 'NGA','name' => 'Nigeria','cty_lan' => 'Nigeria', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '165','key' => '265','code' => 'NIU','abbreviation' => 'NIU','name' => 'Niue','cty_lan' => 'Niue', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '166','key' => '266','code' => 'NOR','abbreviation' => 'NOR','name' => 'Noruega','cty_lan' => 'Norway', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '167','key' => '267','code' => 'NCL','abbreviation' => 'NCL','name' => 'Nueva Caledonia','cty_lan' => 'New Caledonia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '168','key' => '268','code' => 'NZL','abbreviation' => 'NZL','name' => 'Nueva Zelanda','cty_lan' => 'New Zealand', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '169','key' => '269','code' => 'OMN','abbreviation' => 'OMN','name' => 'Omán','cty_lan' => 'Oman', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '170','key' => '270','code' => 'NLD','abbreviation' => 'NLD','name' => 'Países Bajos (los)','cty_lan' => 'Netherlands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '171','key' => '271','code' => 'PAK','abbreviation' => 'PAK','name' => 'Pakistán','cty_lan' => 'Pakistan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '172','key' => '272','code' => 'PLW','abbreviation' => 'PLW','name' => 'Palaos','cty_lan' => 'Palau', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '173','key' => '273','code' => 'PSE','abbreviation' => 'PSE','name' => 'Palestina, Estado de','cty_lan' => 'Palestinian Territory, Occupied', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '174','key' => '274','code' => 'PAN','abbreviation' => 'PAN','name' => 'Panamá','cty_lan' => 'Panama', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '175','key' => '275','code' => 'PNG','abbreviation' => 'PNG','name' => 'Papúa Nueva Guinea','cty_lan' => 'Papua New Guinea', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '176','key' => '276','code' => 'PRY','abbreviation' => 'PRY','name' => 'Paraguay','cty_lan' => 'Paraguay', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '177','key' => '277','code' => 'PER','abbreviation' => 'PER','name' => 'Perú','cty_lan' => 'Peru', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '178','key' => '278','code' => 'PYF','abbreviation' => 'PYF','name' => 'Polinesia Francesa','cty_lan' => 'French Polynesia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '179','key' => '279','code' => 'POL','abbreviation' => 'POL','name' => 'Polonia','cty_lan' => 'Poland', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '180','key' => '280','code' => 'PRT','abbreviation' => 'PRT','name' => 'Portugal','cty_lan' => 'Portugal', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '181','key' => '281','code' => 'PRI','abbreviation' => 'PRI','name' => 'Puerto Rico','cty_lan' => 'Puerto Rico', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '182','key' => '282','code' => 'GBR','abbreviation' => 'GBR','name' => 'Reino Unido (el)','cty_lan' => 'United Kingdom', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '183','key' => '283','code' => 'CAF','abbreviation' => 'CAF','name' => 'República Centroafricana (la)','cty_lan' => 'Central African Republic', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '184','key' => '284','code' => 'CZE','abbreviation' => 'CZE','name' => 'República Checa (la)','cty_lan' => 'Czech Republic', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '185','key' => '285','code' => 'MKD','abbreviation' => 'MKD','name' => 'Macedonia (la antigua República Yugoslava de)','cty_lan' => 'Macedonia, Republic of', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '186','key' => '286','code' => 'COG','abbreviation' => 'COG','name' => 'Congo','cty_lan' => 'Congo (Brazzaville)', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '187','key' => '287','code' => 'COD','abbreviation' => 'COD','name' => 'Congo (la República Democrática del)','cty_lan' => 'Congo, Democratic Republic of the', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '188','key' => '288','code' => 'DOM','abbreviation' => 'DOM','name' => 'República Dominicana (la)','cty_lan' => 'Dominican Republic', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '189','key' => '289','code' => 'REU','abbreviation' => 'REU','name' => 'Reunión','cty_lan' => 'Réunion', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '190','key' => '290','code' => 'RWA','abbreviation' => 'RWA','name' => 'Ruanda','cty_lan' => 'Rwanda', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '191','key' => '291','code' => 'ROU','abbreviation' => 'ROU','name' => 'Rumania','cty_lan' => 'Romania', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '192','key' => '292','code' => 'RUS','abbreviation' => 'RUS','name' => 'Rusia, (la) Federación de','cty_lan' => 'Russian Federation', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '193','key' => '293','code' => 'ESH','abbreviation' => 'ESH','name' => 'Sahara Occidental','cty_lan' => 'Western Sahara', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '194','key' => '294','code' => 'WSM','abbreviation' => 'WSM','name' => 'Samoa','cty_lan' => 'Samoa', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '195','key' => '295','code' => 'ASM','abbreviation' => 'ASM','name' => 'Samoa Americana','cty_lan' => 'American Samoa', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '196','key' => '296','code' => 'BLM','abbreviation' => 'BLM','name' => 'San Bartolomé','cty_lan' => 'Saint-Barthélemy', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '197','key' => '297','code' => 'KNA','abbreviation' => 'KNA','name' => 'San Cristóbal y Nieves','cty_lan' => 'Saint Kitts and Nevis', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '198','key' => '298','code' => 'SMR','abbreviation' => 'SMR','name' => 'San Marino','cty_lan' => 'San Marino', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '199','key' => '299','code' => 'MAF','abbreviation' => 'MAF','name' => 'San Martín (parte francesa)','cty_lan' => 'Saint-Martin (French part)', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '200','key' => '300','code' => 'SPM','abbreviation' => 'SPM','name' => 'San Pedro y Miquelón','cty_lan' => 'Saint Pierre and Miquelon', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '201','key' => '301','code' => 'VCT','abbreviation' => 'VCT','name' => 'San Vicente y las Granadinas','cty_lan' => 'Saint Vincent and Grenadines', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '202','key' => '302','code' => 'SHN','abbreviation' => 'SHN','name' => 'Santa Helena, Ascensión y Tristán de Acuña','cty_lan' => 'Saint Helena', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '203','key' => '303','code' => 'LCA','abbreviation' => 'LCA','name' => 'Santa Lucía','cty_lan' => 'Saint Lucia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '204','key' => '304','code' => 'STP','abbreviation' => 'STP','name' => 'Santo Tomé y Príncipe','cty_lan' => 'Sao Tome and Principe', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '205','key' => '305','code' => 'SEN','abbreviation' => 'SEN','name' => 'Senegal','cty_lan' => 'Senegal', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '206','key' => '306','code' => 'SRB','abbreviation' => 'SRB','name' => 'Serbia','cty_lan' => 'Serbia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '207','key' => '307','code' => 'SYC','abbreviation' => 'SYC','name' => 'Seychelles','cty_lan' => 'Seychelles', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '208','key' => '308','code' => 'SLE','abbreviation' => 'SLE','name' => 'Sierra leona','cty_lan' => 'Sierra Leone', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '209','key' => '309','code' => 'SGP','abbreviation' => 'SGP','name' => 'Singapur','cty_lan' => 'Singapore', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '210','key' => '310','code' => 'SXM','abbreviation' => 'SXM','name' => 'Sint Maarten (parte holandesa)','cty_lan' => 'Sint Maarten (parte holandesa)', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '211','key' => '311','code' => 'SYR','abbreviation' => 'SYR','name' => 'Siria, (la) República Árabe','cty_lan' => 'Syrian Arab Republic (Syria)', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '212','key' => '312','code' => 'SOM','abbreviation' => 'SOM','name' => 'Somalia','cty_lan' => 'Somalia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '213','key' => '313','code' => 'LKA','abbreviation' => 'LKA','name' => 'Sri Lanka','cty_lan' => 'Sri Lanka', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '214','key' => '314','code' => 'SWZ','abbreviation' => 'SWZ','name' => 'Suazilandia','cty_lan' => 'Swaziland', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '215','key' => '315','code' => 'ZAF','abbreviation' => 'ZAF','name' => 'Sudáfrica','cty_lan' => 'South Africa', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '216','key' => '316','code' => 'SDN','abbreviation' => 'SDN','name' => 'Sudán (el)','cty_lan' => 'Sudan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '217','key' => '317','code' => 'SSD','abbreviation' => 'SSD','name' => 'Sudán del Sur','cty_lan' => 'South Sudan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '218','key' => '318','code' => 'SWE','abbreviation' => 'SWE','name' => 'Suecia','cty_lan' => 'Sweden', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '219','key' => '319','code' => 'CHE','abbreviation' => 'CHE','name' => 'Suiza','cty_lan' => 'Switzerland', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '220','key' => '320','code' => 'SUR','abbreviation' => 'SUR','name' => 'Surinam','cty_lan' => 'Suriname', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '221','key' => '321','code' => 'SJM','abbreviation' => 'SJM','name' => 'Svalbard y Jan Mayen','cty_lan' => 'Svalbard and Jan Mayen Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '222','key' => '322','code' => 'THA','abbreviation' => 'THA','name' => 'Tailandia','cty_lan' => 'Thailand', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '223','key' => '323','code' => 'TWN','abbreviation' => 'TWN','name' => 'Taiwán (Provincia de China)','cty_lan' => 'Taiwan, Republic of China', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '224','key' => '324','code' => 'TZA','abbreviation' => 'TZA','name' => 'Tanzania, República Unida de','cty_lan' => 'Tanzania, United Republic of', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '225','key' => '325','code' => 'TJK','abbreviation' => 'TJK','name' => 'Tayikistán','cty_lan' => 'Tajikistan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '226','key' => '326','code' => 'IOT','abbreviation' => 'IOT','name' => 'Territorio Británico del Océano Índico (el)','cty_lan' => 'British Indian Ocean Territory', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '227','key' => '327','code' => 'ATF','abbreviation' => 'ATF','name' => 'Territorios Australes Franceses (los)','cty_lan' => 'French Southern Territories', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '228','key' => '328','code' => 'TLS','abbreviation' => 'TLS','name' => 'Timor-Leste','cty_lan' => 'Timor-Leste', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '229','key' => '329','code' => 'TGO','abbreviation' => 'TGO','name' => 'Togo','cty_lan' => 'Togo', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '230','key' => '330','code' => 'TKL','abbreviation' => 'TKL','name' => 'Tokelau','cty_lan' => 'Tokelau', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '231','key' => '331','code' => 'TON','abbreviation' => 'TON','name' => 'Tonga','cty_lan' => 'Tonga', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '232','key' => '332','code' => 'TTO','abbreviation' => 'TTO','name' => 'Trinidad y Tobago','cty_lan' => 'Trinidad and Tobago', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '233','key' => '333','code' => 'TUN','abbreviation' => 'TUN','name' => 'Túnez','cty_lan' => 'Tunisia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '234','key' => '334','code' => 'TKM','abbreviation' => 'TKM','name' => 'Turkmenistán','cty_lan' => 'Turkmenistan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '235','key' => '335','code' => 'TUR','abbreviation' => 'TUR','name' => 'Turquía','cty_lan' => 'Turkey', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '236','key' => '336','code' => 'TUV','abbreviation' => 'TUV','name' => 'Tuvalu','cty_lan' => 'Tuvalu', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '237','key' => '337','code' => 'UKR','abbreviation' => 'UKR','name' => 'Ucrania','cty_lan' => 'Ukraine', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '238','key' => '338','code' => 'UGA','abbreviation' => 'UGA','name' => 'Uganda','cty_lan' => 'Uganda', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '239','key' => '339','code' => 'URY','abbreviation' => 'URY','name' => 'Uruguay','cty_lan' => 'Uruguay', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '240','key' => '340','code' => 'UZB','abbreviation' => 'UZB','name' => 'Uzbekistán','cty_lan' => 'Uzbekistan', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '241','key' => '341','code' => 'VUT','abbreviation' => 'VUT','name' => 'Vanuatu','cty_lan' => 'Vanuatu', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '242','key' => '342','code' => 'VAT','abbreviation' => 'VAT','name' => 'Santa Sede[Estado de la Ciudad del Vaticano] (la)','cty_lan' => 'Holy See (Vatican City State)', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '243','key' => '343','code' => 'VEN','abbreviation' => 'VEN','name' => 'Venezuela, República Bolivariana de','cty_lan' => 'Venezuela (Bolivarian Republic of)', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '244','key' => '344','code' => 'VNM','abbreviation' => 'VNM','name' => 'Viet Nam','cty_lan' => 'Viet Nam', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '245','key' => '345','code' => 'WLF','abbreviation' => 'WLF','name' => 'Wallis y Futuna','cty_lan' => 'Wallis and Futuna Islands', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '246','key' => '346','code' => 'YEM','abbreviation' => 'YEM','name' => 'Yemen','cty_lan' => 'Yemen', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '247','key' => '347','code' => 'DJI','abbreviation' => 'DJI','name' => 'Yibuti','cty_lan' => 'Djibouti', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '248','key' => '348','code' => 'ZMB','abbreviation' => 'ZMB','name' => 'Zambia','cty_lan' => 'Zambia', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '249','key' => '349','code' => 'ZWE','abbreviation' => 'ZWE','name' => 'Zimbabue','cty_lan' => 'Zimbabwe', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_country' => '250','key' => '350','code' => 'ZZZ','abbreviation' => 'ZZZ','name' => 'Países no declarados','cty_lan' => 'Países no declarados', 'is_deleted' => '0', 'created_by_id' => '1', 'updated_by_id' => '1'],
          ]);

          Schema::connection($this->sConnection)->create('erps_country_states', function (blueprint $table) {
          	$table->increments('id_state');
          	$table->char('code', 50)->unique();
          	$table->char('abbreviation', 50);
          	$table->char('name', 100);
          	$table->char('state', 100);
          	$table->boolean('is_deleted');
            $table->integer('country_id')->unsigned();
          	$table->integer('created_by_id')->unsigned();
          	$table->integer('updated_by_id')->unsigned();
          	$table->timestamps();

          	$table->foreign('country_id')->references('id_country')->on('erps_countries')->onDelete('cascade');
          	$table->foreign('created_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          	$table->foreign('updated_by_id')->references('id')->on(DB::connection(Config::getConnSys())->getDatabaseName().'.'.'users')->onDelete('cascade');
          });

          DB::connection($this->sConnection)->table('erps_country_states')->insert([
          	['id_state' => '1','code' => 'AGU','abbreviation' => 'Ags.','name' => 'Aguascalientes','state' => 'Aguascalientes', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '2','code' => 'BCN','abbreviation' => 'B.C.','name' => 'Baja California','state' => 'Baja California', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '3','code' => 'BCS','abbreviation' => 'B.C.S.','name' => 'Baja California Sur','state' => 'Baja California Sur', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '4','code' => 'CAM','abbreviation' => 'Camp.','name' => 'Campeche','state' => 'Campeche', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '5','code' => 'CHP','abbreviation' => 'Chis.','name' => 'Chiapas','state' => 'Chiapas', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '6','code' => 'CHH','abbreviation' => 'Chih.','name' => 'Chihuahua','state' => 'Chihuahua', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '7','code' => 'COA','abbreviation' => 'Coah.','name' => 'Coahuila','state' => 'Coahuila', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '8','code' => 'COL','abbreviation' => 'Col.','name' => 'Colima','state' => 'Colima', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '9','code' => 'DIF','abbreviation' => 'CDMX','name' => 'Ciudad de México','state' => 'Ciudad de México', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '10','code' => 'DUR','abbreviation' => 'Dgo.','name' => 'Durango','state' => 'Durango', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '11','code' => 'GUA','abbreviation' => 'Gto.','name' => 'Guanajuato','state' => 'Guanajuato', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '12','code' => 'GRO','abbreviation' => 'Gro.','name' => 'Guerrero','state' => 'Guerrero', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '13','code' => 'HID','abbreviation' => 'Hgo.','name' => 'Hidalgo','state' => 'Hidalgo', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '14','code' => 'JAL','abbreviation' => 'Jal.','name' => 'Jalisco','state' => 'Jalisco', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '15','code' => 'MEX','abbreviation' => 'Méx.','name' => 'Estado de México','state' => 'Estado de México', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '16','code' => 'MIC','abbreviation' => 'Mich.','name' => 'Michoacán','state' => 'Michoacán', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '17','code' => 'MOR','abbreviation' => 'Mor.','name' => 'Morelos','state' => 'Morelos', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '18','code' => 'NAY','abbreviation' => 'Nay.','name' => 'Nayarit','state' => 'Nayarit', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '19','code' => 'NLE','abbreviation' => 'N.L.','name' => 'Nuevo León','state' => 'Nuevo León', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '20','code' => 'OAX','abbreviation' => 'Oax.','name' => 'Oaxaca','state' => 'Oaxaca', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '21','code' => 'PUE','abbreviation' => 'Pue.','name' => 'Puebla','state' => 'Puebla', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '22','code' => 'QUE','abbreviation' => 'Qro.','name' => 'Querétaro','state' => 'Querétaro', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '23','code' => 'ROO','abbreviation' => 'Q.R.','name' => 'Quintana Roo','state' => 'Quintana Roo', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '24','code' => 'SLP','abbreviation' => 'S.L.P.','name' => 'San Luis Potosí','state' => 'San Luis Potosí', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '25','code' => 'SIN','abbreviation' => 'Sin.','name' => 'Sinaloa','state' => 'Sinaloa', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '26','code' => 'SON','abbreviation' => 'Son.','name' => 'Sonora','state' => 'Sonora', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '27','code' => 'TAB','abbreviation' => 'Tab.','name' => 'Tabasco','state' => 'Tabasco', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '28','code' => 'TAM','abbreviation' => 'Tamps.','name' => 'Tamaulipas','state' => 'Tamaulipas', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '29','code' => 'TLA','abbreviation' => 'Tlax.','name' => 'Tlaxcala','state' => 'Tlaxcala', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '30','code' => 'VER','abbreviation' => 'Ver.','name' => 'Veracruz','state' => 'Veracruz', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '31','code' => 'YUC','abbreviation' => 'Yuc.','name' => 'Yucatán','state' => 'Yucatán', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '32','code' => 'ZAC','abbreviation' => 'Zac.','name' => 'Zacatecas','state' => 'Zacatecas', 'is_deleted' => '0','country_id' => '151', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '33','code' => 'AL','abbreviation' => 'AL','name' => 'Alabama','state' => 'Alabama', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '34','code' => 'AK','abbreviation' => 'AK','name' => 'Alaska','state' => 'Alaska', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '35','code' => 'AZ','abbreviation' => 'AZ','name' => 'Arizona','state' => 'Arizona', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '36','code' => 'AR','abbreviation' => 'AR','name' => 'Arkansas','state' => 'Arkansas', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '37','code' => 'CA','abbreviation' => 'CA','name' => 'California','state' => 'California', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '38','code' => 'NC','abbreviation' => 'NC','name' => 'Carolina del Norte','state' => 'North Carolina', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '39','code' => 'SC','abbreviation' => 'SC','name' => 'Carolina del Sur','state' => 'South Carolina', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '40','code' => 'CO','abbreviation' => 'CO','name' => 'Colorado','state' => 'Colorado', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '41','code' => 'CT','abbreviation' => 'CT','name' => 'Connecticut','state' => 'Connecticut', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '42','code' => 'ND','abbreviation' => 'ND','name' => 'Dakota del Norte','state' => 'North Dakota', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '43','code' => 'SD','abbreviation' => 'SD','name' => 'Dakota del Sur','state' => 'South Dakota', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '44','code' => 'DE','abbreviation' => 'DE','name' => 'Delaware','state' => 'Delaware', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '45','code' => 'FL','abbreviation' => 'FL','name' => 'Florida','state' => 'Florida', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '46','code' => 'GA','abbreviation' => 'GA','name' => 'Georgia','state' => 'Georgia', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '47','code' => 'HI','abbreviation' => 'HI','name' => 'Hawái','state' => 'Hawaii', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '48','code' => 'ID','abbreviation' => 'ID','name' => 'Idaho','state' => 'Idaho', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '49','code' => 'IL','abbreviation' => 'IL','name' => 'Illinois','state' => 'Illinois', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '50','code' => 'IN','abbreviation' => 'IN','name' => 'Indiana','state' => 'Indiana', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '51','code' => 'IA','abbreviation' => 'IA','name' => 'Iowa','state' => 'Iowa', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '52','code' => 'KS','abbreviation' => 'KS','name' => 'Kansas','state' => 'Kansas', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '53','code' => 'KY','abbreviation' => 'KY','name' => 'Kentucky','state' => 'Kentucky', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '54','code' => 'LA','abbreviation' => 'LA','name' => 'Luisiana','state' => 'Louisiana', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '55','code' => 'ME','abbreviation' => 'ME','name' => 'Maine','state' => 'Maine', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '56','code' => 'MD','abbreviation' => 'MD','name' => 'Maryland','state' => 'Maryland', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '57','code' => 'MA','abbreviation' => 'MA','name' => 'Massachusetts','state' => 'Massachusetts', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '58','code' => 'MI','abbreviation' => 'MI','name' => 'Míchigan','state' => 'Michigan', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '59','code' => 'MN','abbreviation' => 'MN','name' => 'Minnesota','state' => 'Minnesota', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '60','code' => 'MS','abbreviation' => 'MS','name' => 'Misisipi','state' => 'Mississippi', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '61','code' => 'MO','abbreviation' => 'MO','name' => 'Misuri','state' => 'Missouri', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '62','code' => 'MT','abbreviation' => 'MT','name' => 'Montana','state' => 'Montana', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '63','code' => 'NE','abbreviation' => 'NE','name' => 'Nebraska','state' => 'Nebraska', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '64','code' => 'NV','abbreviation' => 'NV','name' => 'Nevada','state' => 'Nevada', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '65','code' => 'NJ','abbreviation' => 'NJ','name' => 'Nueva Jersey','state' => 'New Jersey', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '66','code' => 'NY','abbreviation' => 'NY','name' => 'Nueva York','state' => 'New York', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '67','code' => 'NH','abbreviation' => 'NH','name' => 'Nuevo Hampshire','state' => 'New Hampshire', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '68','code' => 'NM','abbreviation' => 'NM','name' => 'Nuevo México','state' => 'New Mexico', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '69','code' => 'OH','abbreviation' => 'OH','name' => 'Ohio','state' => 'Ohio', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '70','code' => 'OK','abbreviation' => 'OK','name' => 'Oklahoma','state' => 'Oklahoma', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '71','code' => 'OR','abbreviation' => 'OR','name' => 'Oregón','state' => 'Oregon', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '72','code' => 'PA','abbreviation' => 'PA','name' => 'Pensilvania','state' => 'Pennsylvania', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '73','code' => 'RI','abbreviation' => 'RI','name' => 'Rhode Island','state' => 'Rhode Island', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '74','code' => 'TN','abbreviation' => 'TN','name' => 'Tennessee','state' => 'Tennessee', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '75','code' => 'TX','abbreviation' => 'TX','name' => 'Texas','state' => 'Texas', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '76','code' => 'UT','abbreviation' => 'UT','name' => 'Utah','state' => 'Utah', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '77','code' => 'VT','abbreviation' => 'VT','name' => 'Vermont','state' => 'Vermont', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '78','code' => 'VA','abbreviation' => 'VA','name' => 'Virginia','state' => 'Virginia', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '79','code' => 'WV','abbreviation' => 'WV','name' => 'Virginia Occidental','state' => 'West Virginia', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '80','code' => 'WA','abbreviation' => 'WA','name' => 'Washington','state' => 'Washington', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '81','code' => 'WI','abbreviation' => 'WI','name' => 'Wisconsin','state' => 'Wisconsin', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '82','code' => 'WY','abbreviation' => 'WY','name' => 'Wyoming','state' => 'Wyoming', 'is_deleted' => '0','country_id' => '66', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '83','code' => 'ON','abbreviation' => 'ON','name' => 'Ontario ','state' => 'Ontario', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '84','code' => 'QC','abbreviation' => 'QC','name' => 'Quebec','state' => 'Quebec', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '85','code' => 'NS','abbreviation' => 'NS','name' => 'Nueva Escocia','state' => 'Nova Scotia', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '86','code' => 'NB','abbreviation' => 'NB','name' => 'Nuevo Brunswick','state' => 'New Brunswick', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '87','code' => 'MB','abbreviation' => 'MB','name' => 'Manitoba','state' => 'Manitoba', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '88','code' => 'BC','abbreviation' => 'BC','name' => 'Columbia Británica','state' => 'British Columbia', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '89','code' => 'PE','abbreviation' => 'PE','name' => 'Isla del Príncipe Eduardo','state' => 'Prince Edward Island', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '90','code' => 'SK','abbreviation' => 'SK','name' => 'Saskatchewan','state' => 'Saskatchewan', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '91','code' => 'AB','abbreviation' => 'AB','name' => 'Alberta','state' => 'Alberta', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '92','code' => 'NL','abbreviation' => 'NL','name' => 'Terranova y Labrador','state' => 'Newfoundland and Labrador', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '93','code' => 'NT','abbreviation' => 'NT','name' => 'Territorios del Noroeste','state' => 'Northwest Territories', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '94','code' => 'YT','abbreviation' => 'YT','name' => 'Yukón','state' => 'Yukon', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
          	['id_state' => '95','code' => 'UN','abbreviation' => 'NU','name' => 'Nunavut','state' => 'Nunavut', 'is_deleted' => '0','country_id' => '40', 'created_by_id' => '1', 'updated_by_id' => '1'],
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
          SUtil::reconnectDataBase($this->sConnection, $this->bDefault, $this->sHost, $this->sDataBase, $this->sUser, $this->sPassword);

          Schema::connection($this->sConnection)->drop('erps_country_states');
          Schema::connection($this->sConnection)->drop('erps_countries');
        }
    }
}
