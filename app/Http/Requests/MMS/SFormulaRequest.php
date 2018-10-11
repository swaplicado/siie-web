<?php namespace App\Http\Requests\MMS;

use App\Http\Requests\Request;
use App\ERP\SBranch;
use App\ERP\SItem;

class SFormulaRequest extends Request
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
      $branch = new SBranch();
      $item = new SItem();

      // $user = User::find($this->users);

      switch($this->method())
      {
          case 'POST':
              return [
                  // 'branch_id' => 'required|exists:siie.'.$branch->getTable().',id_branch',
                  // 'dt_start' => 'required',
                  'dt_date' => 'required',
                  // 'product' => 'required|exists:siie.'.$item->getTable().',id_item',
                  'item_id' => 'required|exists:siie.'.$item->getTable().',id_item',
                  'identifier' => 'required',
                  'quantity' => 'required|min:0',
                  // 'cost' => 'required|min:0',
              ];

          case 'PUT':
              return [
                  // 'dt_start' => 'required',
                  'dt_date' => 'required',
                  'identifier' => 'required',
                  'quantity' => 'required|min:0',
                  // 'cost' => 'required|min:0',
              ];

          default:
                  break;
      }
    }
}
