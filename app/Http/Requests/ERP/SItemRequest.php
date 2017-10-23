<?php namespace App\Http\Requests\ERP;

use App\Http\Requests\Request;
use App\ERP\SItem;
use App\ERP\SItemGender;
use App\ERP\SUnit;

class SItemRequest extends Request
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
        $item = new SItem();
        $gender = new SItemGender();
        $unit = new SUnit();

        return [
            'code' => 'required|unique:siie.'.$item->getTable(),
            'name' => 'required',
            'gender_id' => 'required|exists:siie.'.$gender->getTable().',id_item_gender',
            'unit_id' => 'required|exists:siie.'.$unit->getTable().',id_unit',
        ];
    }
}
