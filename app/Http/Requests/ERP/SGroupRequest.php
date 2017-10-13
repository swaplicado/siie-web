<?php namespace App\Http\Requests\ERP;

use App\Http\Requests\Request;

class SGroupRequest extends Request
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
            'item_family_id' => 'required|exists:siie.erpu_item_families,id_item_family',
        ];
    }
}
