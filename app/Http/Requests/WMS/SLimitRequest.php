<?php namespace App\Http\Requests\WMS;

use App\Http\Requests\Request;
use App\ERP\SBranch;
use App\WMS\SWarehouse;
use App\WMS\SLocation;

class SLimitRequest extends Request
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
      // $branch = new SBranch();
      // $whs = new SWarehouse();
      // $loc = new SLocation();


        return [
            'max' => 'required|numeric|min:0',
            'min' => 'required|numeric|min:0',
            // 'aux_branch_id' => 'required|unique_with:siie.'.$branch->getTable().',|exists:siie.'.$branch->getTable().',id_branch',
            // 'aux_warehouse_id' => 'required|exists:siie.'.$whs->getTable().',id_whs',
            // 'aux_location_id' => 'required|exists:siie.'.$loc->getTable().',id_whs_location',
        ];
    }
}
