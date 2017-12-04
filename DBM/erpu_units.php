<?php

/**
 * Connect to database Origin
 */
$webhost        = '192.168.1.233';
$webusername    = 'root';
$webpassword    = 'msroot';
$webdbname      = 'erp';
$webcon         = mysqli_connect($webhost, $webusername, $webpassword, $webdbname);
$webcon->set_charset("utf8");
if (mysqli_connect_errno())
{
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
}

/**
 * Queries data origin.
 */
      $colsOrigin = array(
        'symbol',
        'unit',
        // 'unit_base_equiv',
        'id_unit',
        'b_del',
        'fid_tp_unit'
      );
      $tableOrigin = "ITMU_UNIT";
$allColsOrigin = "";
// Armar el conjunto de columnas que formaran la query
foreach ($colsOrigin as $col => $value) {
    $allColsOrigin = $allColsOrigin . $value . ", ";
}

/**
 * Queries for reading
 */
 //concatenar la consulta con las columnas ingresadas anteriormente
 $strSQL = mysqli_query($webcon, 'SELECT ' . substr($allColsOrigin, 0, -2) . ' FROM ' . $tableOrigin);

/**
 * Connect to database destiny
 */
$mobhost        = 'localhost';
$mobusername    = 'root';
$mobpassword    = 'msroot';
$mobdbname      = 'siie_aeth';
$mobcon         = mysqli_connect($mobhost, $mobusername, $mobpassword, $mobdbname);
$mobcon->set_charset("utf8");
if (mysqli_connect_errno())
{
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
}

/**
 * Queries data destiny.
 */
      $colsDest = array(
        'code',
        'name',
        'external_id',
        'is_deleted',
        'base_unit_id_opt',
        'base_unit_equivalence_opt',
        'created_by_id',
        'updated_by_id'
      );
      $tableDest = "erpu_units";

      $allColsDest = "";
      // Armar el conjunto de columnas que formaran la query
      foreach ($colsDest as $col => $value) {
          $allColsDest = $allColsDest . $value . ", ";
      }

/**
 * Insert data from old database
 */

// RUN
$contador = 0;
echo "Destino: " . $allColsDest . " <br>";
echo "Origen: " . $allColsOrigin . " <br>";
while ($row = mysqli_fetch_array($strSQL))
{
    foreach($row as $key => $val)
    {
        $row[$key] = mysqli_real_escape_string($mobcon, $row[$key]);
    }
    $insertes = "";
    for ($i = 0; $i < (count($row)/2); $i++) {
        $format = "\"" . $row[$i] . "\"";
        $insertes = $insertes .  $format . ", ";
    }
    mysqli_query($mobcon, "INSERT IGNORE INTO " . $tableDest .  "(" . substr($allColsDest, 0, -2) .") VALUES (" . substr($insertes, 0, -2) .",1,1,1);") or die (mysqli_error($mobcon));
    //if (mysqli_affected_rows ($mobcon)==1){
    //}
    $contador += 1;
    echo "INSERT IGNORE INTO " . $tableDest .  "(" . substr($allColsDest, 0, -2) .") VALUES (" . substr($insertes, 0, -2) . ",1,1,1); <br>";

}

if ($contador>0) {
    echo "
    <br><br>
    <br><br>
    <div align='center'>
      <marquee direction='down' width='200' bgcolor='green' height='200' behavior='alternate' style='border:solid'>
        <marquee behavior='alternate'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TODO OK!<br>Se procesaron ".$contador. " valores.
        </marquee>
      </marquee>
    </div>
    ";
}
else{
    echo "
    <br><br>
    <br><br>
    <div align='center'>
      <marquee direction='down' width='200' bgcolor='red' height='200' behavior='alternate' style='border:solid'>
        <marquee behavior='alternate'>ERRORRRR!!!!!<br>Se procesaron ".$contador. " valores.
        </marquee>
      </marquee>
    </div>
    ";
}
mysqli_close($mobcon);
mysqli_close($webcon);
