<?php namespace App\Http\Requests\WMS;

use App\Http\Requests\Request;
use App\WMS\SWarehouse;
use App\WMS\SLocation;

class SLocRequest extends Request
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
      $location = new SLocation();

        return [
            'code' => 'required',
            'name' => 'required',
            'whs_id' => 'required|exists:siie.'.$whs->getTable().',id_whs',
          ];
    }
}
