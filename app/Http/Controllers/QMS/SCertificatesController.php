<?php

namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\SUtils\SConnectionUtils;

use App\QMS\SQDocument;
use App\QMS\SQMongoDoc;

class SCertificatesController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function print($idDoc)
    {
        SConnectionUtils::reconnectCompany();

        $oDoc = SQDocument::find($idDoc);
        $oMongoDoc = SQMongoDoc::where('lot_id', $oDoc->lot_id)->get();

        return view('qms.certificates.cert');
    }
}
