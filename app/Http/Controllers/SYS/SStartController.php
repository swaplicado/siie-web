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
use App\ERP\SBranch;
use App\WMS\SWarehouse;
use App\SCore\SStockManagment;
use App\SCore\SSegregationCore;
use App\SPadLocks\SRecordLock;

class SStartController extends Controller
{

    public function __construct()
    {

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
        $oLocks = new SRecordLock();

        session(['utils' => $oUtils]);
        session(['db_configuration' => $oDbConfig]);
        session(['lock' => $oLocks]);

        $lUserCompany = SUtil::getUserCompany(\Auth::user());

        if (sizeof($lUserCompany) < 1 && ! session('utils')->isSuperUser(\Auth::user())) {
         return redirect()->route('notauthorizedsys');
        }

        return view('start.select')
                            ->with('lUserCompany', $lUserCompany);
    }

    public function branchwhs(){
      SConnectionUtils::reconnectCompany();
      $branch = SBranch::select('id_branch','name')
                      ->where('partner_id',session('partner')->id_partner)
                      ->get();

      return view('start.branchwhs')
                ->with('branch',$branch);
      // return SStartController::selectModule();
    }

    public function branch(Request $request)
    {


        SConnectionUtils::reconnectCompany();
        $BranchId =  $_COOKIE['BranchId'];
        $oBranch = SBranch::find($BranchId);

        // $oConfiguration = SConfiguration::find(1);

        session(['branch' => $oBranch]);
        // session(['configuration' => $oConfiguration]);

        return SStartController::selectwhs();
    }

    public function selectwhs(){

        SConnectionUtils::reconnectCompany();

      $whs = SWarehouse::select('id_whs','name')
                      ->where('branch_id',session('branch')->id_branch)
                      ->get();

      return view('start.whs')
                ->with('whs',$whs);
    }

    public function whs(){

      SConnectionUtils::reconnectCompany();
      $WarehouseId =  $_COOKIE['WarehouseId'];
      $oWarehouse = SWarehouse::find($WarehouseId);

      // $oConfiguration = SConfiguration::find(1);

      session(['whs' => $oWarehouse]);
      // session(['configuration' => $oConfiguration]);

      return SStartController::selectModule();
    }

    /**
     *
     */
    public function getIn(Request $request)
    {
        $iCompanyId =  $_COOKIE['iCompanyId'];
        $oCompany = SCompany::find($iCompanyId);
        // $oConfiguration = SConfiguration::find(1);

        session(['company' => $oCompany]);
        // session(['configuration' => $oConfiguration]);

        $sConnection = 'siie';
        $bDefault = true;
        $sHost = $oCompany->host;
        $sDataBase = $oCompany->database_name;
        $sUser = $oCompany->database_user;
        $sPassword = $oCompany->password;

        SConnectionUtils::reconnectDataBase($sConnection, $bDefault, $sHost, $sDataBase, $sUser, $sPassword);

        $oErpConfigurationPartner = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.PARTNER_ID'));
        $oDecAmount = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DECIMALS_AMT'));
        $oDecQuantity = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DECIMALS_QTY'));
        $oLocationEn = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DECIMALS_QTY'));
        $olockTime = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.LOCK_TIME'));
        $oDbImport = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DB_IMPORT'));
        $oDbHost = SErpConfiguration::find(\Config::get('scsiie.CONFIGURATION.DB_HOST'));

        $oPartner = SPartner::find($oErpConfigurationPartner->val_int);
        $oStock = new SStockManagment();
        $oSegregations = new SSegregationCore();

        session(['partner' => $oPartner]);
        session(['decimals_amt' => $oDecAmount->val_int]);
        session(['decimals_qty' => $oDecQuantity->val_int]);
        session(['location_enabled' => $oLocationEn->val_boolean]);
        session(['lock_time' => $olockTime->val_int]);
        session(['db_import' => $oDbImport->val_text]);
        //session(['db_host' => $oDbHost->val_text]);
        session(['stock' => $oStock]);
        session(['segregation' => $oSegregations]);

        return SStartController::branchwhs();
    }

    /**
     *
     */
    public function selectModule()
    {
        return view('start.modules');
    }
}
