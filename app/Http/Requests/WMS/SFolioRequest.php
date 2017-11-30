<?php namespace App\Http\Requests\WMS;

use App\Http\Requests\Request;
use App\ERP\SBranch;
use App\WMS\SWarehouse;
use App\WMS\SLocation;
use App\WMS\SMvtClass;
use App\WMS\SMvtType;

class SFolioRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      $cls = new SMvtClass();
      $tp = new SMvtType();
      $branch = new SBranch();
      $whs = new SWarehouse();
      $loc = new SLocation();


        return [
            'folio_start' => 'required',
            'mvt_class_id' => 'required|exists:siie.'.$cls->getTable().',id_mvt_class',
            'mvt_type_id' => 'required|exists:siie.'.$tp->getTable().',id_mvt_type',
            // 'aux_branch_id' => 'required|unique_with:siie.'.$branch->getTable().',|exists:siie.'.$branch->getTable().',id_branch',
            // 'aux_warehouse_id' => 'required|exists:siie.'.$whs->getTable().',id_whs',
            // 'aux_location_id' => 'required|exists:siie.'.$loc->getTable().',id_whs_location',
        ];
    }
}
