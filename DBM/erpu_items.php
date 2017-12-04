<?php
ini_set('max_execution_time', 0);
/**
 * Connect to database Origin
 */
 include 'connOrigin.php';

if (mysqli_connect_errno())
{
    echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
}

/**
 * Queries data origin.
 */
      $colsOrigin = array(
          'code',
          'name',
          'len',
          'surf',
          'vol',
          'mass',
          'id_item',
          'b_lot',
          'b_bulk',
          'b_del',
          'fid_igen',
          'fid_unit'
      );
      $tableOrigin = "ITMU_ITEM";
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
        'length',
        'surface',
        'volume',
        'mass',
        'external_id',
        'is_lot',
        'is_bulk',
        'is_deleted',
        'item_gender_id',
        'unit_id',
        'created_by_id',
        'updated_by_id'
      );
      $tableDest = "erpu_items";

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
    mysqli_query($mobcon, "INSERT IGNORE INTO " . $tableDest .  "(" . substr($allColsDest, 0, -2) .") VALUES (" . substr($insertes, 0, -2) .", 1, 1);") or die (mysqli_error($mobcon));
    //if (mysqli_affected_rows ($mobcon)==1){
    //}
    $contador += 1;
    echo "INSERT IGNORE INTO " . $tableDest .  "(" . substr($allColsDest, 0, -2) .") VALUES (" . substr($insertes, 0, -2) . ", 1, 1); <br>";

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
