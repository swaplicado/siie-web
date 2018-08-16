<?php namespace App\SCore;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Database\Config;
use App\SUtils\SGuiUtils;

use App\ERP\SYear;
use App\ERP\SItem;

use App\WMS\SPallet;

/**
 *
 */
class SPalletsCore {

    public static function processPallets($oRow = null, $aPallets = [])
    {
       $lErrors = array();

       foreach ($aPallets as $iPallet) {
          $oPallet = SPallet::find($iPallet);

          if ($oPallet == null) {
             array_push($lErrors, "La tarima ".$iPallet." no existe");
             continue;
          }

          if ($oPallet->item_id != $oRow->iItemId || $oPallet->unit_id != $oRow->iUnitId) {
             array_push($lErrors, "La tarima ".$iPallet." no es del material/producto seleccionado");
          }

          if (! SPalletsCore::validatePalletStock($iPallet, $oPallet->item_id, $oPallet->unit_id)) {
            array_push($lErrors, "La tarima ".$iPallet." no está vacía");
          }
       }

       return $lErrors;
    }

    private static function validatePalletStock($iPallet = 0, $iItem = 0, $iUnit = 0)
    {
      $sSelect = 'sum(ws.input) as inputs,
                   sum(ws.output) as outputs,
                   (sum(ws.input) - sum(ws.output)) as stock,
                   AVG(ws.cost_unit) as cost_unit,
                   ei.code as item_code,
                   ei.name as item,
                   eu.code as unit_code,
                   ei.is_lot,
                   ei.id_item,
                   eu.id_unit,
                   ws.lot_id,
                   wl.lot,
                   ws.pallet_id,
                   ws.location_id
                   ';

      $aParameters = array();
      $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] = $sSelect;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');
      $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = $iPallet;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = $iItem;
      $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = $iUnit;

      $lStockGral = session('stock')->getStockResult($aParameters);

      $lStockGral = $lStockGral->groupBy('id_branch')
                         ->groupBy('id_whs')
                         ->groupBy('id_whs_location')
                         ->having('stock', '>', '0')
                         ->get();

      return sizeof($lStockGral) == 0;
    }
}
