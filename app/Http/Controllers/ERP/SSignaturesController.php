<?php

namespace App\Http\Controllers\ERP;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SConnectionUtils;

use App\ERP\SSignature;
use App\QMS\SQDocument;

class SSignaturesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        SConnectionUtils::reconnectCompany();

        $oSignature = new SSignature($request->all());
        $id = $request->id;

        \DB::beginTransaction();
 
        try {

            if (password_verify($request->signature, \Auth::user()->password)) {
                $oSignature->signed_by_id = \Auth::user()->id;
                $oSignature->signed = true;
                $oSignature->is_deleted = false;
    
                $oSignature->save();
            }
            else {
                return 1;
            }
    
            /**
             * 
             */
            switch ($oSignature->signature_type_id) {
                case \Config::get('scsiie.SIGNATURE.ARGOX'):
                    $oDocument = SQDocument::find($id);
                    $oDocument->signature_argox_id = $oSignature->id_signature;
                    $oDocument->save();
                    break;
    
                case \Config::get('scsiie.SIGNATURE.CODING'):
                    $oDocument = SQDocument::find($id);
                    $oDocument->signature_coding_id = $oSignature->id_signature;
                    $oDocument->save();
                    break;
    
                case \Config::get('scsiie.SIGNATURE.MB'):
                    $oDocument = SQDocument::find($id);
                    $oDocument->signature_mb_id = $oSignature->id_signature;
                    $oDocument->save();
                    break;
                
                default:
                    throw new \Exception('Error, Opción desconocida.');
                    break;
            }
        
            \DB::commit();

        } catch (\Exception $e) {
            \DB::rollback();
            throw $e;
        } catch (\Throwable $e) {
            \DB::rollback();
            throw $e;
        }

        return $oSignature->id_signature;
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
