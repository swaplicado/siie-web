<?php

namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SUtils\SProcess;
use App\ERP\SSignature;
use App\ERP\SAuthorization;
use App\User;

class SAuthorizationsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       $this->oCurrentUserPermission = SProcess::constructor($this, \Config::get('scperm.PERMISSION.ERP_SIGNATURE_AUTHORIZATION'), \Config::get('scsys.MODULES.QMS'));

       $this->iFilter = \Config::get('scsys.FILTER.ACTIVES');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lUsers = User::where('is_deleted', false)
                            ->orderBy('username', 'ASC')
                            ->select(['username', 'id'])
                            ->where('is_deleted', false)
                            ->whereNotIn('username', ['admin', 'adminswap', 
                                                        'saporis', 'contraloria',
                                                        'swap',
                                                        'consultas', 'manager'])
                            ->get();

        foreach ($lUsers as $user) {
            $lAuths = \DB::connection(session('db_configuration')->getConnCompany())
                        ->table('erp_sign_autorizations AS esa')
                        ->where('esa.user_id', $user->id)
                        ->where('is_deleted', false)
                        ->lists('signature_type_id');

            if (sizeof($lAuths) > 0) {
                $user->auths = $lAuths;
            }
            else {
                $user->auths = array();
            }
        }

        $lSignatures =  \DB::connection(session('db_configuration')->getConnCompany())
                            ->table('erps_signature_types AS est')
                            ->selectRaw('CONCAT(code,"-",type_name) AS stype, id_signature_type')
                            ->where('id_signature_type', '!=', 1)
                            ->orderBy('code', 'ASC')
                            ->get();

        return view('siie.signatures_auth.index')
                        ->with('lSignatures', $lSignatures)
                        ->with('lUsers', $lUsers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $req = $request;
        $authorizations = $request->authorizations;
        $idUsr = $request->user;

        SAuthorization::where('user_id', $idUsr)->update(['is_deleted' => true]);

        if ($authorizations != null) {
            foreach ($authorizations as $authType) {
                $oAuth = SAuthorization::where('user_id', $idUsr)
                                ->where('signature_type_id', $authType)
                                ->orderBy('id_authorization', 'DESC')
                                ->first();

                if ($oAuth == null) {
                    $oAuth = new SAuthorization();

                    $oAuth->is_deleted = false;
                    $oAuth->user_id = $idUsr;
                    $oAuth->signature_type_id = $authType;
                    $oAuth->created_by_id = \Auth::user()->id;
                    $oAuth->updated_by_id = \Auth::user()->id;

                    $oAuth->save();
                }
                else {
                    $oAuth->is_deleted = false;
                    $oAuth->updated_by_id = \Auth::user()->id;

                    $oAuth->save();
                }
            }
        }

        return redirect()->route('siie.signauths.index');
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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
