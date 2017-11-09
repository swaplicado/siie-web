<?php namespace App\Http\Controllers\SYS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SValidation;
use App\SUtils\SUtil;
use App\SUtils\SSessionUtils;
use App\SUtils\SConnectionUtils;
use App\SYS\SCompany;
use App\SYS\SConfiguration;
use App\ERP\SErpConfiguration;
use App\Database\Config;
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
        $oUtils = new SSessionUtils();
        $oDbConfig = new Config();

        session(['session_utils' => $oUtils]);
        session(['db_configuration' => $oDbConfig]);

        $lUserCompany = SUtil::getUserCompany(\Auth::user());

        if (sizeof($lUserCompany) < 1 && ! session('session_utils')->isSuperUser(\Auth::user())) {
         return redirect()->route('notauthorizedsys');
        }

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

        $oErpConfigurationPartner = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PARTNER_ID'));
        $oPartner = SPartner::find($oErpConfigurationPartner->val_int);
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
