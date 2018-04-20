<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use Laracasts\Flash\Flash;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ERP\SYear;
use App\ERP\SMonth;
use App\SUtils\SUtil;
use App\SUtils\SValidation;
use App\SUtils\SMenu;
use App\SUtils\SProcess;

class SMonthsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
      $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.CENTRAL_CONFIG'), \Config::get('scsys.MODULES.ERP'));

      $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $iYearId = 0)
    {
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;
      $oYear = SYear::find($iYearId);
      $lMonths = SMonth::Search($this->iFilter, $iYearId)
                                              ->orderBy('month', 'ASC')
                                              ->paginate(12);

      return view('siie.months.index')
          ->with('months', $lMonths)
          ->with('actualUserPermission', $this->oCurrentUserPermission)
          ->with('iYear', $oYear->year)
          ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $oMonth = SMonth::find($id);

        if (! (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $oMonth->created_by_id)))
        {
          return redirect()->route('notauthorized');
        }

        return view('siie.months.createEdit')->with('month', $oMonth)
                                        ->with('iFilter', $this->iFilter);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     public function update(Request $request, $id)
     {
       $oMonth = SMonth::find($id);

       $oMonth->fill($request->all());
       $oMonth->updated_by_id = \Auth::user()->id;

       $oMonth->save();

       Flash::success(trans('messages.REG_EDITED'))->important();

       return redirect()->route('siie.months.index', $oMonth->year_id);
     }
}
