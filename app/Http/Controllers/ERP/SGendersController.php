<?php namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Http\Requests\ERP\SGenderRequest;
use Laracasts\Flash\Flash;
use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SValidation;
use App\ERP\SItemGender;
use App\ERP\SItemGroup;
use App\ERP\SItemClass;
use App\ERP\SItemType;
use App\SUtils\SProcess;

class SGendersController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
      $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.ITEM_CONFIG'), \Config::get('scsys.MODULES.ERP'));

      $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
      $this->iFilter = $request->filter == null ? \Config::get('scsys.FILTER.ACTIVES') : $request->filter;

      $lGenders = SItemGender::Search($request->name, $this->iFilter)->orderBy('name', 'ASC')->paginate(20);

      foreach ($lGenders as $gender) {
        $gender->group;
      }

      return view('siie.genders.index')
          ->with('genders', $lGenders)
          ->with('actualUserPermission', $this->oCurrentUserPermission)
          ->with('iFilter', $this->iFilter);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (SValidation::canCreate($this->oCurrentUserPermission->privilege_id))
          {
            $lGroups = SItemGroup::orderBy('name', 'ASC')->lists('name', 'id_item_group');
            $lClasses = SItemClass::orderBy('name', 'ASC')->lists('name', 'id_class');
            $lItemTypes = SItemType::orderBy('name', 'ASC')->lists('name', 'id_item_type');

            return view('siie.genders.createEdit')
                                    ->with('groups', $lGroups)
                                    ->with('classes', $lClasses)
                                    ->with('types', $lItemTypes);
          }
          else
          {
             return redirect()->route('notauthorized');
          }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SGenderRequest $request)
    {
      $gender = new SItemGender($request->all());

      $gender->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
      $gender->updated_by_id = \Auth::user()->id;
      $gender->created_by_id = \Auth::user()->id;
      $gender->save();

      Flash::success(trans('messages.REG_CREATED'))->important();

      return redirect()->route('siie.genders.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $gender = SItemGender::find($id);

        if (SValidation::canEdit($this->oCurrentUserPermission->privilege_id) || SValidation::canAuthorEdit($this->oCurrentUserPermission->privilege_id, $gender->created_by_id))
        {
            $lGroups = SItemGroup::orderBy('name', 'ASC')->lists('name', 'id_item_group');
            $lClasses = SItemClass::orderBy('name', 'ASC')->lists('name', 'id_class');
            $lItemTypes = SItemType::orderBy('name', 'ASC')->lists('name', 'id_item_type');

            return view('siie.genders.createEdit')
                                    ->with('groups', $lGroups)
                                    ->with('classes', $lClasses)
                                    ->with('types', $lItemTypes)
                                    ->with('gender', $gender);
        }
        else
        {
            return redirect()->route('notauthorized');
        }
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
        $gender = SItemGender::find($id);
        $gender->fill($request->all());
        $gender->updated_by_id = \Auth::user()->id;
        $gender->save();

        Flash::warning(trans('messages.REG_EDITED'))->important();

        return redirect()->route('siie.genders.index');
    }

    /**
     * Inactive the registry setting the flag is_deleted to true
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     */
    public function copy(Request $request, $id)
    {
        $gender = SItemGender::find($id);

        $genderCopy = clone $gender;
        $genderCopy->id_item_gender = 0;

        return view('siie.genders.createEdit')->with('group', $genderCopy)
                                              ->with('bIsCopy', true);
    }

    public function activate(Request $request, $id)
    {
        $gender = SItemGender::find($id);

        $gender->fill($request->all());
        $gender->is_deleted = \Config::get('scsys.STATUS.ACTIVE');
        $gender->updated_by_id = \Auth::user()->id;

        $gender->save();

        Flash::success(trans('messages.REG_ACTIVATED'))->important();

        return redirect()->route('siie.genders.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if (SValidation::canDestroy($this->oCurrentUserPermission->privilege_id))
        {
          $gender = SItemGender::find($id);
          $gender->fill($request->all());
          $gender->is_deleted = \Config::get('scsys.STATUS.DEL');
          $gender->updated_by_id = \Auth::user()->id;

          $gender->save();
          #$user->delete();

          Flash::error(trans('messages.REG_DELETED'))->important();
          return redirect()->route('siie.genders.index');
        }
        else
        {
          return redirect()->route('notauthorized');
        }
    }

    public function children(Request $request, $id)
    {
      if ($request->ajax())
      {
        $types = SItemType::getTypes($id);
        return response()->json($types);
      }
    }
}
