<?php namespace App\SUtils;

use Carbon\Carbon;
use App\SUtils\SGuiUtils;
use App\SCore\SProductionCore;

use App\WMS\SWmsLot;
use App\WMS\SPallet;
use App\WMS\SLocation;
use App\WMS\SWarehouse;
use App\WMS\SLimit;
use App\WMS\SItemContainer;
use App\ERP\SYear;

/**
 * this class contains functions of utils
 * used by movemennts
 */
class SMovsUtils {

  /**
   * get the elements permitted for the warehouse based in rules of storage
   * and stock
   *
   * @param  integer $iWhsSrc   Source Warehouse
   * @param  integer $iWhsDes   Destiny warehouse
   * @param  integer $iElementType Element type can be:
   *                                        \Config::get('scwms.ELEMENTS_TYPE.ITEMS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.LOTS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.PALLETS')
   * @param  integer $iMvtType   Movement Type
   * @return array  a list result of query requested to server
   */
  public static function getElementsToWarehouse($iWhsSrc = 0, $iWhsDes = 0,
                                                  $iElementType = 0, $iSrcPO = 0,
                                                    $iDesPO = 0, $iMvtType = 0,
                                                    $iMvtSubType = 0)
  {
    // initialize the select for the query
    $sSelect = 'ei.id_item,
                eu.id_unit,
                ei.code AS item_code,
                ei.name AS item_name,
                eu.code AS unit_code,
                ei.is_lot,
                ei.is_bulk,
                ei.without_rotation,
                \'0\' as available_stock';

    // if is lot or pallet add other fields
    switch ($iElementType) {
      case \Config::get('scwms.ELEMENTS_TYPE.ITEMS'):
        break;
      case \Config::get('scwms.ELEMENTS_TYPE.LOTS'):
        $sSelect = $sSelect.',
                    wl.lot,
                    wl.id_lot,
                    wl.dt_expiry';
        break;
      case \Config::get('scwms.ELEMENTS_TYPE.PALLETS'):
        $sSelect = $sSelect.',
                    wp.id_pallet AS pallet,
                    wp.id_pallet';
        break;

      default:
        break;
    }

    // if the destiny warehouse is zero, means that the movement is input
    // just return the elements filtered by the configuration of storage
    if ($iWhsDes != '0' && ($iWhsSrc == '0' || $iWhsSrc == session('transit_whs')->id_whs)) {
      $lItemContainers = SMovsUtils::getContainerConfiguration($iWhsDes);

      $lElements = SMovsUtils::getFilteredElements($lItemContainers, $sSelect, $iElementType);
      $lElements = SProductionCore::filterForProduction($lElements, $iMvtType, $iMvtSubType,
                                                          $iSrcPO, $iDesPO);

      $lElementsToReturn = array();
      if ($iMvtType == \Config::get('scwms.MVT_IN_DLVRY_PP')
            || $iMvtType == \Config::get('scwms.MVT_OUT_DLVRY_FP')) {

          $lElements = $lElements->get();
          $lElementsToReturn = SProductionCore::prepareReturn($lElements, $iMvtType,
                                                              $iSrcPO, $iDesPO, $iElementType);
      }
      else {
        $lElements = $lElements->get();
      }

      $lElementsToReturn = $lElements;
    }

    //if the movement is output filter the elements by the configuration and
    //by the stock
    if ($iWhsSrc != '0' && $iWhsSrc != session('transit_whs')->id_whs) {
      if ($iWhsDes != '0') {
        $lItemContainers = SMovsUtils::getContainerConfiguration($iWhsDes);
      }
      else {
        $lItemContainers = array();
      }

      $sSelect = $sSelect.',
                            (COALESCE(
                            SUM(ws.input) -
                            SUM(ws.output)
                            , 0)) AS stock';

      $sSelect = SMovsUtils::addSegregated($sSelect, $iWhsSrc, $iElementType);

      $bWithStock = SProductionCore::filterProductionWithStock($iMvtType);

      $lElements = SMovsUtils::getElementsWithStock($lItemContainers, $sSelect,
                                                        $iWhsSrc, $iElementType, $bWithStock);

      $lElements = SProductionCore::filterForProduction($lElements, $iMvtType, $iMvtSubType,
                                                          $iSrcPO, $iDesPO);
      $lElements = $lElements->get();

      $lElementsToReturn = array();
      // Filter the elements with stock available greater than zero
      foreach ($lElements as $oItem) {
        if ($oItem->stock > $oItem->segregated) {
          $oItem->available_stock = session('utils')->
                                      formatNumber($oItem->stock - $oItem->segregated, \Config::get('scsiie.FRMT.QTY'));
          array_push($lElementsToReturn, $oItem);
        }
      }
    }

    return $lElementsToReturn;
  }

  /**
   * get the configurations for the warehouse received
   *
   * @param  integer $iWarehouse id of warehouse
   *
   * @return array  list of SWarehouse with the configurations for the warehouse
   */
  public static function getContainerConfiguration($iWarehouse = 0)
  {
      $oWarehouse = SWarehouse::find($iWarehouse);
      $lItemContainers = SItemContainer::where('is_deleted', false)
                                        ->where(function ($query) use ($oWarehouse) {
                                              $query->where('container_type_id', \Config::get('scwms.CONTAINERS.WAREHOUSE'))
                                                    ->where('container_id', $oWarehouse->id_whs);
                                          })->orWhere(function ($query) use ($oWarehouse) {
                                              $query->where('container_type_id', \Config::get('scwms.CONTAINERS.BRANCH'))
                                                    ->where('container_id', $oWarehouse->branch_id);
                                          })->orWhere('container_type_id', \Config::get('scwms.CONTAINERS.COMPANY'))
                                          ->orderBy('item_link_type_id', 'ASC')
                                          ->get();

     return $lItemContainers;
  }

  /**
   * get the items from stock and make the "join" with the permitted items
   *
   * @param  array   $lItemContainers configurations of storage
   * @param  string  $sSelect  SELECT for the query
   * @param  integer $iElementType Element type can be:
   *                                        \Config::get('scwms.ELEMENTS_TYPE.ITEMS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.LOTS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.PALLETS')
   *
   * @return Query  result of query
   */
  public static function getFilteredElements($lItemContainers = [], $sSelect, $iElementType = 0)
  {
      $lElements = \DB::connection(session('db_configuration')->getConnCompany())
                ->table('erpu_items as ei')
                ->join('erpu_units as eu', 'ei.unit_id', '=', 'eu.id_unit')
                ->join('erpu_item_genders as eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                ->join('erpu_item_groups as eigr', 'eig.item_group_id', '=', 'eigr.id_item_group');

      $lElements = SMovsUtils::addJoins($lElements, $iElementType);

      $lElements = $lElements->select(\DB::raw($sSelect))
                  ->where('ei.is_deleted', false)
                  ->where('ei.is_inventory', true)
                  ->where('eig.item_class_id', '!=', \Config::get('scsiie.ITEM_CLS.SPENDING'));

      $lElements = SMovsUtils::filterWithConfiguration($lItemContainers, $lElements);

      $lElements = $lElements->orderBy('ei.code', 'ASC')
                        ->orderBy('ei.name', 'ASC')
                        ->orderBy('eu.id_unit', 'ASC');

      return $lElements;
  }

  /**
   * filter the items searched with the configurations of storage
   *
   * @param  array  $lItemContainers configurations
   * @param  Query $oQuery  query of items
   *
   * @return Query with filtered items
   */
  public static function filterWithConfiguration($lItemContainers = [], $oQuery = null)
  {
      $oQuery = $oQuery->where(function ($query) use ($lItemContainers) {
          $bAll = false;
          foreach ($lItemContainers as $oConfig) {
            switch ($oConfig->item_link_type_id) {
              case \Config::get('scsiie.ITEM_LINK.ALL'):
                $bAll = true;
                break;
              case \Config::get('scsiie.ITEM_LINK.CLASS'):
                $query = $query->orWhere('item_class_id', $oConfig->item_link_id);
                break;
              case \Config::get('scsiie.ITEM_LINK.TYPE'):
                $query = $query->orWhere('item_type_id', $oConfig->item_link_id);
                break;
              case \Config::get('scsiie.ITEM_LINK.FAMILY'):
                $query = $query->orWhere('item_family_id', $oConfig->item_link_id);
                break;
              case \Config::get('scsiie.ITEM_LINK.GROUP'):
                $query = $query->orWhere('item_group_id', $oConfig->item_link_id);
                break;
              case \Config::get('scsiie.ITEM_LINK.GENDER'):
                $query = $query->orWhere('id_item_gender', $oConfig->item_link_id);
                break;
              case \Config::get('scsiie.ITEM_LINK.ITEM'):
                $query = $query->orWhere('id_item', $oConfig->item_link_id);
                break;

              default:
                # code...
                break;
            }

            if ($bAll) {
              break;
            }
          }
        });

        return $oQuery;
  }

  /**
   * add the quantity segregated to query
   *
   * @param string  $sSelect       select for the query
   * @param integer $iWarehouseSrc id of source warehouse
   * @param  integer $iElementType Element type can be:
   *                                        \Config::get('scwms.ELEMENTS_TYPE.ITEMS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.LOTS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.PALLETS')
   *
   * @return string the completed select for the query
   */
  public static function addSegregated($sSelect = '', $iWarehouseSrc = 0, $iElementType = 0)
  {
    $aParameters = array();
    $aParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 'ei.id_item';
    $aParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 'ei.unit_id';
    $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWarehouseSrc;
    $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');

    switch ($iElementType) {
      case \Config::get('scwms.ELEMENTS_TYPE.LOTS'):
            $aParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'wl.id_lot';
            break;
      case \Config::get('scwms.ELEMENTS_TYPE.PALLETS'):
            $aParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'wp.id_pallet';
            break;
      default:
        # code...
        break;
    }

    $sub = session('stock')->getSubSegregated($aParameters);
    $sSelect = $sSelect.', ('.($sub->toSql()).') as segregated';
    // $sSelect = $sSelect.', "0" as segregated';

    return $sSelect;
  }

  /**
   * obtains the stock of items from the warehouse received
   * and filter it with the storage configurations
   *
   * @param  array   $lItemContainers array of item containers configurations
   * @param  string  $sSelect  select for the query
   * @param  integer $iWarehouseSrc Source warehouse
   * @param  integer $iElementType Element type can be:
   *                                        \Config::get('scwms.ELEMENTS_TYPE.ITEMS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.LOTS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.PALLETS')
   *
   * @return Query result of query
   */
  public static function getElementsWithStock($lItemContainers = [], $sSelect = '',
                                                $iWarehouseSrc = 0, $iElementType = 0,
                                                $bWithStock = true)
  {
      $lElements = \DB::connection(session('db_configuration')->getConnCompany())
                  ->table('wms_stock as ws')
                  ->join('erpu_items as ei', 'ws.item_id', '=', 'ei.id_item')
                  ->join('erpu_units as eu', 'ws.unit_id', '=', 'eu.id_unit')
                  ->join('erpu_item_genders as eig', 'ei.item_gender_id', '=', 'eig.id_item_gender')
                  ->join('erpu_item_groups as eigr', 'eig.item_group_id', '=', 'eigr.id_item_group');

      $lElements = SMovsUtils::addJoins($lElements, $iElementType);

      $lElements =  $lElements->select(\DB::raw($sSelect))
                  ->where('ei.is_deleted', false)
                  ->where('ei.is_inventory', true)
                  ->where('eig.item_class_id', '!=', \Config::get('scsiie.ITEM_CLS.SPENDING'))
                  ->where('ws.is_deleted', false)
                  ->where('ws.whs_id', $iWarehouseSrc);

      switch ($iElementType) {
        case \Config::get('scwms.ELEMENTS_TYPE.LOTS'):
              $lElements = $lElements->whereRaw('ws.lot_id = wl.id_lot')
                                      ->groupBy('ei.id_item')
                                      ->groupBy('ei.unit_id')
                                      ->groupBy('wl.id_lot');
              break;
        case \Config::get('scwms.ELEMENTS_TYPE.PALLETS'):
              $lElements = $lElements->whereRaw('ws.pallet_id = wp.id_pallet')
                                      ->groupBy('ei.id_item')
                                      ->groupBy('ei.unit_id')
                                      ->groupBy('wp.id_pallet');
              break;
        default:
              $lElements =  $lElements->groupBy('ei.id_item')
                                      ->groupBy('ei.unit_id');
          break;
      }

      $lElements = SMovsUtils::filterWithConfiguration($lItemContainers, $lElements);

      $lElements = $lElements->orderBy('ei.code', 'ASC')
      ->orderBy('ei.name', 'ASC')
      ->orderBy('ei.name', 'ASC');

      if ($bWithStock) {
        $lElements = $lElements->having('stock', '>', '0');
      }

      return $lElements;
  }

  /**
   * get the locations of the warehouse received
   * if the locations are not enabled in the system return only the
   * defualt location
   *
   * @param  integer $iWhs id of warehouse
   *
   * @return SLocationList list of SLocation
   */
  public static function getResWarehouseLocations($iWhs = 0)
  {
      $lLocations = SLocation::where('whs_id', $iWhs)
                                  ->where('is_deleted', false);

      if (! session('location_enabled')) {
          $lLocations = $lLocations->where('is_default', true);
      }

      return $lLocations;
  }

  /**
   * make the join with the lots or pallet table based on the element type
   *
   * @param Query   $lElements
   * @param  integer $iElementType Element type can be:
   *                                        \Config::get('scwms.ELEMENTS_TYPE.ITEMS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.LOTS')
   *                                        \Config::get('scwms.ELEMENTS_TYPE.PALLETS')
   *
   * @return Query result of query with the joins agregated
   */
  public static function addJoins($lElements = [], $iElementType = 0)
  {
    switch ($iElementType) {
      case \Config::get('scwms.ELEMENTS_TYPE.LOTS'):
            $lElements = $lElements->join('wms_lots AS wl',
                            function($join) {
                              $join->on('ei.id_item', '=', 'wl.item_id')
                                   ->on('ei.unit_id', '=', 'wl.unit_id');
                            });
            break;
      case \Config::get('scwms.ELEMENTS_TYPE.PALLETS'):
            $lElements = $lElements->join('wms_pallets AS wp',
                            function($join) {
                              $join->on('ei.id_item', '=', 'wp.item_id')
                                   ->on('ei.unit_id', '=', 'wp.unit_id');
                            });
            break;
      default:
        # code...
        break;
    }

    return $lElements;
  }

  /**
   * get the stock from warehouse received, if the second paratemer is not equal to zero
   * the query return the result except rows with the id of received movement
   *
   * @param  integer $iWhs id of warehouse
   * @param  integer $iMvt id of movement to except
   *
   * @return array result of query
   */
  public static function getStockFromWarehouse($iWhs = 0, $iMvt = 0, $iMvtType = 0, $iSrcPO = 0, $iDesPO = 0)
  {
      $aParameters = array();
      $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');
      $aParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = $iWhs;

      if ($iMvt != 0) {
        $aParameters[\Config::get('scwms.STOCK_PARAMS.ID_MVT')] = $iMvt;
      }

      $aParameters[\Config::get('scwms.STOCK_PARAMS.SSELECT')] =  'ws.whs_id,
                                     ws.location_id,
                                     ws.pallet_id,
                                     ws.lot_id,
                                     ws.item_id,
                                     ws.unit_id,
                                     wwl.code as location,
                                     IF (wp.id_pallet = 1, \'SIN TARIMA\', wp.id_pallet) as pallet,
                                     wl.lot,
                                     wl.dt_expiry,
                                     ei.code as item_code,
                                     ei.name as item,
                                     eu.code as unit,
                                     ei.is_lot,
                                     ei.is_bulk,
                                     ei.without_rotation,
                                     \'0\' as available_stock,
                                     sum(ws.input) as inputs,
                                     sum(ws.output) as outputs,
                                     (sum(ws.input) - sum(ws.output)) as stock';

      $aSegParameters = array();
      $aSegParameters[\Config::get('scwms.STOCK_PARAMS.ID_YEAR')] = session('work_year');
      $aSegParameters[\Config::get('scwms.STOCK_PARAMS.ITEM')] = 'ws.item_id';
      $aSegParameters[\Config::get('scwms.STOCK_PARAMS.UNIT')] = 'ws.unit_id';
      $aSegParameters[\Config::get('scwms.STOCK_PARAMS.LOT')] = 'ws.lot_id';
      $aSegParameters[\Config::get('scwms.STOCK_PARAMS.PALLET')] = 'ws.pallet_id';
      $aSegParameters[\Config::get('scwms.STOCK_PARAMS.LOCATION')] = 'ws.location_id';
      $aSegParameters[\Config::get('scwms.STOCK_PARAMS.WHS')] = 'ws.whs_id';

      $oStock = session('stock')->getStockResult($aParameters, $aSegParameters);

      // if (SGuiUtils::isProductionMovement($iMvtType)) {
      //   $oStock = $oStock->where('ws.prod_ord_id', $iSrcPO);
      // }

      $oStock = $oStock->groupBy('wwl.id_whs_location')
                        ->groupBy('wp.id_pallet')
                        ->groupBy('wl.id_lot')
                        ->groupBy('ws.item_id')
                        ->groupBy('ws.unit_id')
                        ->having('stock', '>', '0');

      $oStock = $oStock->get();

      foreach ($oStock as $key => $row) {
        $row->available_stock = $row->stock - $row->segregated;
      }

      return $oStock;
  }

}
