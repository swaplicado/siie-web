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
use App\ERP\SUserBranch;
use App\ERP\SUserWhs;
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

      $lUserBranch = array();

      if (session('utils')->isSuperUser(\Auth::user()))
      {
        $lBranch = SBranch::where('is_deleted', 0)->where('partner_id', '=', session('partner')->id_partner )->paginate(10);

        $i = 0;
        foreach ($lBranch as $oBranch) {
          $oUserBranch = new SUserBranch();
          $oUserBranch->branch_id = $oBranch->id_branch;
          $lUserBranch[$i] = $oUserBranch;
          $i++;
        }

        foreach($lUserBranch as $UB) {
          $UB->branch;
        }

        return view('start.branchwhs')
                  ->with('branch',$lUserBranch)
                  ->with('flag',0);
      }
      else
      {
        // $lUserBranch = SUserBranch::where('user_id', '=', (\Auth::user()))
        //                             ->paginate(10);
        $lUserBranch = \DB::table('erpu_access_branch')
                            ->join('erpu_branches', 'branch_id', '=', 'erpu_branches.id_branch')
                            ->where('erpu_branches.partner_id',session('partner')->id_partner)
                            ->get();

        return view('start.branchwhs')
                  ->with('branch',$lUserBranch)
                  ->with('flag',1);
        //dd($lUserBranch);
      }


      //dd($lUserBranch);
      // return view('start.branchwhs')
      //           ->with('branch',$lUserBranch);
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


                $lUserWhs = array();

                if (session('utils')->isSuperUser(\Auth::user()))
                {
                  $lWhs = SWarehouse::where('is_deleted', 0)->where('branch_id', '=', session('branch')->id_branch )->paginate(10);

                  $i = 0;
                  foreach ($lWhs as $oWhs) {
                    $oUserWhs = new SUserWhs();
                    $oUserWhs->whs_id = $oWhs->id_whs;
                    $lUserWhs[$i] = $oUserWhs;
                    $i++;
                  }

                  foreach($lUserWhs as $UB) {
                    $UB->warehouses;
                  }

                  return view('start.whs')
                            ->with('whs',$lUserWhs)
                            ->with('flag',0);
                }
                else
                {
                  // $lUserBranch = SUserBranch::where('user_id', '=', (\Auth::user()))
                  //                             ->paginate(10);
                  $lUserWhs = \DB::table('erpu_access_whs')
                                      ->join('wmsu_whs', 'whs_id', '=', 'wmsu_whs.id_whs')
                                      ->where('wmsu_whs.branch_id',session('branch')->id_branch)
                                      ->get();

                  return view('start.whs')
                            ->with('whs',$lUserWhs)
                            ->with('flag',1);
    }
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

	      $sWorkDate =  $_COOKIE['tWorkDate'];
        $oWorkDate = Carbon::parse($sWorkDate);
        session(['work_date' => $oWorkDate]);

        $iYear = $oWorkDate->year;
        $oYear = SYear::where('year', $iYear)
                        ->where('is_deleted', false)
                        ->first();
        session(['work_year' => $oYear->id_year]);
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
