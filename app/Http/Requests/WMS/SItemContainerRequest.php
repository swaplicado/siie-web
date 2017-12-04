<?php namespace App\Http\Requests\WMS;

use App\Http\Requests\Request;
use App\ERP\SBranch;
use App\WMS\SWarehouse;
use App\WMS\SLocation;

class SItemContainerRequest extends Request
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
        return [
            'item_link_type_id' => 'required',
            'item_link_id' => 'required',
        ];
    }
}
