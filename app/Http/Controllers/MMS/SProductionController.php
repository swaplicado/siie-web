<?php namespace App\Http\Controllers\MMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SProcess;

use App\SUtils\SUtil;
use App\SUtils\SMenu;

class SProductionController extends Controller
{
    public function __construct()
    {
       SProcess::constructor($this, \Config::get('scperm.PERMISSION.MMS'), \Config::get('scsys.MODULES.MMS'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        return view('mms.index');
    }
}
