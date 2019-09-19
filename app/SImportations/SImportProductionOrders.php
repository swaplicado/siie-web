<?php namespace App\SImportations;

use App\ERP\SItem;
use App\ERP\SUnit;
use App\MMS\Formulas\SFormula;
use App\MMS\SProductionOrder;
use App\WMS\SWmsLot;
use App\QMS\SQDocument;
use phpDocumentor\Reflection\Types\Integer;

/**
 * this class import the data of item families from siie
 */
class SImportProductionOrders
{
    protected $webhost        = 'localhost';
    protected $webusername    = 'root';
    protected $webpassword    = 'msroot';
    protected $webdbname      = 'erp_sc';
    protected $webcon         = '';

    /**
     * receive the name of host to connect
     * can be a IP or name of host
     *
     * @param string $sHost
     */
    function __construct($sHost, $sDbName)
    {
        $this->webdbname = $sDbName;
        $this->webcon = mysqli_connect($sHost, $this->webusername, $this->webpassword, $this->webdbname);
        $this->webcon->set_charset("utf8");
        if (mysqli_connect_errno()) {
            echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
        }
    }

    /**
     * read the data  from siie, transform it, and saves it in the database
     *
     * @return integer number of records imported
     */
    public function importOrders()
    {
        // $json = '{
        //     "name" : { "first" : "John", "last" : "Backus" },
        //     "contribs" : [ "Fortran", "ALGOL", "Backus-Naur Form", "FP" ],
        //     "awards" : [
        //       {
        //         "award" : "W.W. McDowell Award",
        //         "year" : 1967,
        //         "by" : "IEEE Computer Society"
        //       }, {
        //         "award" : "Draper Prize",
        //         "year" : 1993,
        //         "by" : "National Academy of Engineering"
        //       }
        //     ]
        //   }';
        // $bson = \MongoDB\BSON\fromJSON($json);
        // $value = \MongoDB\BSON\toPHP($bson);

        // $qDocument = SQDocument::create(json_decode($json, true));
        // $qDocument->save();

        $oImportation = SImportUtils::getImportationObject(\Config::get('scsys.IMPORTATIONS.PO'));
        $ids = array();

        $sql1 = "SELECT 
                    fid_ord_n
                FROM
                    mfg_ord
                WHERE
                    (ts_new >= '".$oImportation->last_importation."'
                        OR ts_edit >= '".$oImportation->last_importation."'
                        OR ts_del >= '".$oImportation->last_importation."')
                        AND fid_ord_n IS NOT NULL
                ORDER BY fid_ord_n ASC;";

        $result1 = $this->webcon->query($sql1);

        if ($result1->num_rows > 0) {
            // output data of each row
            while ($row1 = $result1->fetch_assoc()) {
                if (! in_array($row1['fid_ord_n'], $ids)) {
                    $ids[] = $row1['fid_ord_n'];
                }
            }
        }

        $sql2 = "SELECT 
                    id_ord
                FROM
                    mfg_ord
                WHERE
                    (ts_new >= '".$oImportation->last_importation."'
                        OR ts_edit >= '".$oImportation->last_importation."'
                        OR ts_del >= '".$oImportation->last_importation."')
                ORDER BY id_ord ASC;";

        $result2 = $this->webcon->query($sql2);

        // $ids = array();
        if ($result2->num_rows > 0) {
            // output data of each row
            while ($row2 = $result2->fetch_assoc()) {
                if (! in_array($row2['id_ord'], $ids)) {
                    $ids[] = $row2['id_ord'];
                }
            }
        }

        if (! sizeof($ids) > 0) {
            return 0;
        }

        $lWebFormulas = SFormula::lists('id_formula', 'external_id');

        $lPOsAux = SProductionOrder::lists('id_order', 'external_id');
        $lWebItems = SItem::lists('id_item', 'external_id');
        $lWebUnits = SUnit::lists('id_unit', 'external_id');

        $lPOs = array();
        foreach ($lPOsAux as $key => $value) {
            $lPOs[''.$key] = $value;
        }

        $counter = 0;
        $iNumberOfElements = 25;

        while (sizeof($ids) > 0) {
            $subArray = array_splice($ids, 0, $iNumberOfElements);

            $in_ids = implode(",", $subArray);

            $sql = "SELECT 
                        *,
                        COALESCE((SELECT 
                            CONCAT(COALESCE(lot, ''),
                            '__',
                            COALESCE(dt_exp_n, ''))
                        FROM
                            erp_sc.trn_lot
                        WHERE
                            id_lot = mo.fid_lot_n
                                AND id_item = mo.fid_lot_item_nr
                                AND id_unit = mo.fid_lot_unit_nr
                        ORDER BY ts_new DESC
                        LIMIT 1), '') AS _lot
                    FROM
                        erp_sc.mfg_ord mo
                    WHERE
                        id_ord IN (".$in_ids.")
                    ORDER BY id_ord ASC";
            
            $result = $this->webcon->query($sql);

            if ($result->num_rows > 0) {
                // output data of each row
                while ($row = $result->fetch_assoc()) {
                    $key = $row["id_year"].$row["id_ord"];

                    if (! array_key_exists($key, $lPOs)) {
                        $oNewPO = SImportProductionOrders::siieToSiieWeb(0, $row, $lWebFormulas, $lWebItems, $lWebUnits);
                        $oNewPO->save();
                        
                    }
                    else {
                        $oUpdPO = SImportProductionOrders::siieToSiieWeb($key, $row, $lWebFormulas, $lWebItems, $lWebUnits);
                        $oUpdPO->save();
                    }

                    $counter++;
                }
            }
        }
        
        $this->webcon->close();

        SImportUtils::saveImportation($oImportation);

        return $counter;
    }

    /**
     * Transform a siie object to siie-web object
     *
     * @param  Object $oSiieFormula
     * @return SProductionOrder
     */
    private static function siieToSiieWeb($iExternalId, $oSiiePO = '', $lFormulas = [], $lWebItems = [], $lWebUnits = [])
    {
        if ($iExternalId == 0) {
            $oSiieWebPO = new SProductionOrder();
            
            $oSiieWebPO->created_by_id = 1;
            $oSiieWebPO->created_at = $oSiiePO["ts_new"];
        }
        else {
            $oSiieWebPO = SProductionOrder::where('external_id', $iExternalId)
                            ->first();         
        }

        $oSiieWebPO->folio = $oSiiePO["num"];
        $oSiieWebPO->identifier = $oSiiePO["ref"];
        $oSiieWebPO->date = $oSiiePO["dt"];
        $oSiieWebPO->charges = $oSiiePO["chgs"];
        $oSiieWebPO->external_id = $oSiiePO["id_year"].$oSiiePO["id_ord"];
        $oSiieWebPO->is_deleted = $oSiiePO["b_del"];
        $oSiieWebPO->plan_id = 1;
        $oSiieWebPO->branch_id = 3287;
        $oSiieWebPO->floor_id = 2;
        $oSiieWebPO->type_id = $oSiiePO["fid_tp_ord"];
        $oSiieWebPO->status_id = $oSiiePO["fid_st_ord"];
        $oSiieWebPO->item_id = $lWebItems[$oSiiePO["fid_item_r"]];
        $oSiieWebPO->unit_id = $lWebUnits[$oSiiePO["fid_unit_r"]];

        if ($oSiiePO["fid_ord_year_n"] != null) {
            $oSiieWebPO->lot_id = SImportProductionOrders::getLotID($oSiiePO["_lot"], 
                                                                    $oSiiePO["fid_lot_item_nr"], 
                                                                    $oSiiePO["fid_lot_unit_nr"],
                                                                    $lWebItems,
                                                                    $lWebUnits
                                                                );

            $f_id = SProductionOrder::select('id_order')
                                        ->where('external_id', $oSiiePO["fid_ord_year_n"].$oSiiePO["fid_ord_n"])
                                        ->first();

            $oSiieWebPO->father_order_id = $f_id['id_order'];
        }
        else {
            $oSiieWebPO->lot_id = 1;
            $oSiieWebPO->father_order_id = 1;
        }

        $oSiieWebPO->formula_id = $lFormulas[$oSiiePO["fid_bom"]];
        $oSiieWebPO->updated_by_id = 1;
        $oSiieWebPO->updated_at = $oSiiePO["ts_edit"];

        return $oSiieWebPO;
    }

    private static function getLotID(String $lot = "", $siieItem = 0, $siieUnit = 0, $lWebItems = [], $lWebUnits = [])
    {
        if (strlen($lot) == 0) {
            return 1;
        }

        if (!$siieItem > 0 || !$siieUnit > 0) {
            return 1;
        }

        $aLot = explode("__", $lot);

        $oLot = SWmsLot::where('lot', $aLot[0])
                        ->where('item_id', $lWebItems[$siieItem])
                        ->where('unit_id', $lWebUnits[$siieUnit])
                        ->orderBy('created_at', 'DESC')
                        ->first();

        if ($oLot == null) {
            $oLot = new SWmsLot();

            $oLot->lot = $aLot[0];
            $oLot->dt_expiry = $aLot[1];
            $oLot->is_deleted = 0;
            $oLot->item_id = $lWebItems[$siieItem];
            $oLot->unit_id = $lWebUnits[$siieUnit];
            $oLot->created_by_id = 1;
            $oLot->updated_by_id = 1;

            $oLot->save();
        }

        return $oLot->id_lot;
    }
}
