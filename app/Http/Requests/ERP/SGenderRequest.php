<?php namespace App\Http\Requests\ERP;

use App\Http\Requests\Request;

class SGenderRequest extends Request
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
            'name' => 'required',
            'item_group_id' => 'required|exists:siie.erpu_item_groups,id_item_group',
            'item_class_id' => 'required|exists:siie.erps_item_classes,id_class',
            'item_type_id' => 'required|exists:siie.erps_item_types,id_item_type',
        ];
    }
}
