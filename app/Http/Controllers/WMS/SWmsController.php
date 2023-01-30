<?php namespace App\Http\Controllers\WMS;

use App\Http\Controllers\Controller;
use App\SUtils\SImportUtils;
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
     * @return \Illuminate\View\View
     */
    public function home()
    {
        return view('wms.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('wms.index');
    }
}
