<?php namespace App\SCore;

use App\WMS\Segregation\SSegregation;
use App\WMS\Segregation\SSegregationRow;
use App\WMS\Segregation\SSegregationLotRow;
use App\WMS\SWarehouse;

class SMovSegregation
{
    public static function processSegregationMove($lSavedMovements = [], $iOperation = 0)
    {
        // if the movement is a transfer
        if (sizeof($lSavedMovements) != 2) {
            return;
        }
        // the movement is a transfer
        if ($lSavedMovements[0]->mvt_whs_type_id != \Config::get('scwms.MVT_TP_IN_TRA') &&
                $lSavedMovements[1]->mvt_whs_type_id != \Config::get('scwms.MVT_TP_IN_TRA')) {
            return;
        }
        // if the movement is not a external movement of input or output
        if ($lSavedMovements[0]->whs_id != session('transit_whs')->id_whs && $lSavedMovements[1]->whs_id != session('transit_whs')->id_whs) {
            return;
        }

        if ($lSavedMovements[0]->mvt_whs_class_id == \Config::get('scwms.MVT_CLS_IN')) {
            $oInputMov = $lSavedMovements[0];
            $oOutputMov = $lSavedMovements[1];
        }
        else {
            $oInputMov = $lSavedMovements[1];
            $oOutputMov = $lSavedMovements[0];
        }

        \DB::connection('company')->transaction(function() use ($oInputMov, $oOutputMov, $iOperation) {
            if ($iOperation == \Config::get('scwms.OPERATION_TYPE.EDITION')) {
                SSegregation::where('reference_id', $oInputMov->id_mvt)->delete();
            }

            $oSegregation = new SSegregation();

            $oSegregation->dt_date = session('work_date')->format('Y/m/d');
            $oSegregation->is_deleted = false;
            $oSegregation->segregation_type_id = \Config::get('scqms.SEGREGATION_TYPE.INSPECTED');
            $oSegregation->reference_id = $oInputMov->id_mvt;
            $oSegregation->created_by_id = \Auth::user()->id;
            $oSegregation->updated_by_id = \Auth::user()->id;

            $oSegregation->save();

            $lRows = array();
            foreach ($oOutputMov->rows as $oRow) {
                if ($oRow->is_deleted) {
                    continue;
                }
                
                foreach ($oRow->lotRows as $oLotRow) {
                    if ($oLotRow->is_deleted) {
                        continue;
                    }

                    $dQuantity = 0;
                    $dSegregated = SMovSegregation::getSegregated($oOutputMov->whs_id, $oRow->pallet_id, 
                                                                    $oLotRow->lot_id, $oRow->item_id, $oRow->unit_id, 
                                                                    $oOutputMov->year_id, session('work_date')->format('Y/m/d'));

                    if ($dSegregated == 0) {
                        continue;
                    }
                    if ($dSegregated >= $oLotRow->quantity) {
                        $dQuantity = $oLotRow->quantity;
                    }
                    else {
                        $dQuantity = $dSegregated;
                    }

                    $aRows = SMovSegregation::createRows($oOutputMov->branch_id, $oOutputMov->whs_id, $oInputMov->whs_id, $oRow->pallet_id, 
                                                $oLotRow->lot_id, $oRow->item_id, $oRow->unit_id, $oOutputMov->year_id, $dQuantity);

                    $oSegregation->rows()->saveMany($aRows);

                    array_push($lRows, $aRows);
                }
            }

            if (sizeof($lRows) == 0) {
                $oSegregation->delete();
            }
        });
        
    }

    private static function getSegregated($iWhs = 0, $iPallet = 0, $iLot = 0, $iItem = 0, $iUnit = 0, $year_id = 0, $sDate = "")
    {
        $sSelect = "SUM(IF(wsr.segregation_mvt_type_id = ".\Config::get('scqms.SEGREGATION.INCREMENT').", wsr.quantity, 0)) AS increment,
                    SUM(IF(wsr.segregation_mvt_type_id = ".\Config::get('scqms.SEGREGATION.DECREMENT').", wsr.quantity, 0)) AS decrement,
                    SUM(IF(wsr.segregation_mvt_type_id = ".\Config::get('scqms.SEGREGATION.INCREMENT').", wsr.quantity, 0)) 
                    - SUM(IF(wsr.segregation_mvt_type_id = ".\Config::get('scqms.SEGREGATION.DECREMENT').", wsr.quantity, 0)) AS segregated";

        $query = \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('wms_segregations AS ws')
                            ->join('wms_segregation_rows AS wsr', 'ws.id_segregation', '=', 'wsr.segregation_id')
                            ->join('qmss_segregation_events AS qse', 'wsr.segregation_event_id', '=', 'qse.id_segregation_event');

        $query = $query->where('ws.is_deleted', false)
                        ->where('wsr.is_deleted', false)
                        ->where('ws.dt_date', '<=', $sDate)
                        ->where('wsr.year_id', $year_id)
                        ->where('wsr.whs_id', $iWhs)
                        ->where('wsr.pallet_id', $iPallet)
                        ->where('wsr.lot_id', $iLot)
                        ->where('wsr.item_id', $iItem)
                        ->where('wsr.unit_id', $iUnit)
                        ->select(\DB::raw($sSelect));

        $query = $query->groupBy('wsr.whs_id')
                        ->groupBy('wsr.lot_id')
                        ->groupBy('wsr.pallet_id')
                        ->groupBy('wsr.item_id')
                        ->groupBy('wsr.unit_id');

        $qSegregated = $query->get();

        if (sizeof($qSegregated) > 0)  {
            return $qSegregated[0]->segregated;
        }
        
        return 0;
    }

    private static function createRows($iBranch = 0, $iSrcWhs = 0, $iDesWhs = 0, $iPallet = 0, $iLot = 0, $iItem = 0, $iUnit = 0, $iYear = 0, $dQuantity = 0)
    {
        $oReleaseRow = new SSegregationRow();

        $oReleaseRow->quantity = $dQuantity;
        $oReleaseRow->is_deleted = false;
        $oReleaseRow->segregation_mvt_type_id = \Config::get('scqms.SEGREGATION.DECREMENT');
        $oReleaseRow->segregation_event_id = \Config::get('scqms.PARTIALRELEASE');
        $oReleaseRow->branch_id = $iBranch;
        $oReleaseRow->whs_id = $iSrcWhs;
        $oReleaseRow->pallet_id = $iPallet;
        $oReleaseRow->lot_id = $iLot;
        $oReleaseRow->year_id = $iYear;
        $oReleaseRow->item_id = $iItem;
        $oReleaseRow->unit_id = $iUnit;
        $oReleaseRow->created_by_id = \Auth::user()->id;
        $oReleaseRow->updated_by_id = \Auth::user()->id;
        $oReleaseRow->notes = "";

        $oWhs = SWarehouse::find($iDesWhs);

        if ($oWhs->is_quality) {
            return [$oReleaseRow];
        }

        $oSegRow = clone $oReleaseRow;
        $oSegRow->segregation_mvt_type_id = \Config::get('scqms.SEGREGATION.INCREMENT');
        $oSegRow->segregation_event_id = \Config::get('scqms.BYINSPECTING');
        $oSegRow->whs_id = $iDesWhs;

        return [$oReleaseRow, $oSegRow];
    }
}
