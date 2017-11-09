<?php namespace App\Http\Controllers\WMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\SUtils\SUtil;
use App\SUtils\SMenu;
use App\SUtils\SProcess;

class SWmsController extends Controller
{
    private $oCurrentUserPermission;
    private $iFilter;

    public function __construct()
    {
       SProcess::constructor($this, \Config::get('scperm.PERMISSION.WMS'), \Config::get('scsys.MODULES.WMS'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('wms.index');
    }

    public function index()
    {
        return view('wms.index');
    }
}
