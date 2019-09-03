<?php

namespace App\Http\Controllers\QMS;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\QMS\SQDocElement;
use App\QMS\SElementField;
use App\SUtils\SConnectionUtils;

class SDocElementsController extends Controller
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

        if (sizeof($request->fields) == 0) {
            $oElement = new SQDocElement($request->all());
            
            $oElement->is_deleted = false;
            $oElement->created_by_id = \Auth::user()->id;
            $oElement->updated_by_id = \Auth::user()->id;
    
            $oElement->save();
        }
        else {
            $fields = json_decode($request->fields);
            $oElement = SQDocElement::find($request->element);

            $lFields = array();
            foreach ($fields as $field) {
                if ($field->id_field > 0) {
                    $oField = SElementField::find($field->id_field);
                }
                else {
                    $oField = new SElementField();
                    $oField->is_deleted = false;
                }

                $oField->field_name = $field->field_name;

                $lFields[] = $oField;
            }

            $oElement->fields()->saveMany($lFields);

            $oElement->fields;
        }

        return $oElement;
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
