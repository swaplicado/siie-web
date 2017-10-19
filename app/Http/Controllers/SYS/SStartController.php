<?php namespace App\Http\Controllers\SYS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\SUtils\SConnectionUtils;
use App\SYS\SCompany;
use App\SYS\SConfiguration;
use App\ERP\SPartner;
use App\SYS\SUserCompany;

class SStartController extends Controller
{

    public function __construct()
    {
        session()->forget('company');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $lUserCompany = SUtil::getUserCompany(\Auth::user());

        return view('start.select')
                            ->with('lUserCompany', $lUserCompany);
    }

    /**
     *
     */
    public function getIn(Request $request)
    {
        $iCompanyId =  $_COOKIE['iCompanyId'];
        $oCompany = SCompany::find($iCompanyId);
        $oConfiguration = SConfiguration::find(1);

        session(['company' => $oCompany]);
        session(['configuration' => $oConfiguration]);

        $sConnection = 'siie';
        $bDefault = true;
        $sHost = $oCompany->host;
        $sDataBase = $oCompany->database_name;
        $sUser = $oCompany->database_user;
        $sPassword = $oCompany->password;

        SConnectionUtils::reconnectDataBase($sConnection, $bDefault, $sHost, $sDataBase, $sUser, $sPassword);

        $oPartner = SPartner::find($oConfiguration->partner_id);
        session(['partner' => $oPartner]);

        return SStartController::selectModule();
    }

    /**
     *
     */
    public function selectModule()
    {
        return view('start.modules');
    }
}
