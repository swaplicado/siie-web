<?php namespace App\Http\Requests\ERP;

use App\Http\Requests\Request;
use App\ERP\SItem;
use App\ERP\SItemGender;
use App\ERP\SUnit;

class SItemRequest extends Request
{
    protected $id = 0;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // $this->id = $this->route('id');
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

        $id_item = $this->route()->parameters()['items'];

        return [
            'code' => 'required|unique:siie.'.$item->getTable().',code,'.$id_item.',id_item',
                      // unique:users,username,123,user_id',
            'name' => 'required',
            'item_gender_id' => 'required|exists:siie.'.$gender->getTable().',id_item_gender',
            'unit_id' => 'required|exists:siie.'.$unit->getTable().',id_unit',
        ];
    }
    // /**
    //  * Get the validation rules that apply to the request.
    //  * RESPALDO
    //  * @return array
    //  */
    // public function rules()
    // {
    //     $item = new SItem();
    //     $gender = new SItemGender();
    //     $unit = new SUnit();
    //
    //     return [
    //         'code' => 'required|unique:siie.'.$item->getTable(),
    //         'name' => 'required',
    //         'item_gender_id' => 'required|exists:siie.'.$gender->getTable().',id_item_gender',
    //         'unit_id' => 'required|exists:siie.'.$unit->getTable().',id_unit',
    //     ];
    // }
}
