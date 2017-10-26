<?php namespace App\Http\Requests\WMS;

use App\Http\Requests\Request;
use App\WMS\SWarehouse;
use App\WMS\SWhsType;
use App\ERP\SBranch;

class SWhsRequest extends Request
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
      $whs = new SWarehouse();
      $branch = new SBranch();
      $whstype = new SWhsType();

        return [
            'code' => 'required|unique:siie.'.$whs->getTable(),
            'name' => 'required',
            'branch_id' => 'required|exists:siie.'.$branch->getTable().',id_branch',
            'whs_type_id' => 'required|exists:siie.'.$whstype->getTable().',id_whs_type',
        ];
    }
}
