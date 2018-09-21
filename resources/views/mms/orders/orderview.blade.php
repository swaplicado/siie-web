<!-- Modal -->
<div id="seePO" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{{ trans('mms.labels.PRODUCTION_ORDER') }}</h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('br', trans('userinterface.labels.BRANCH')) !!}
              {!! Form::label('branch', 'MORELIA MICH.', ['class'=>'form-control', 'id' => 'branch']) !!}
            </div>
          </div>
          <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('l_plan', trans('mms.labels.PRODUCTION_PLAN')) !!}
              {!! Form::label('plan', '00001 SEMANA 38 I', ['class'=>'form-control', 'id' => 'plan']) !!}
            </div>
          </div>
          <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('l_floor', trans('mms.labels.FLOOR')) !!}
              {!! Form::label('floor', 'P01 P. MORELIA', ['class'=>'form-control', 'id' => 'floor']) !!}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('date', trans('userinterface.labels.DATE_ORDER')) !!}
              {!! Form::label('dt_order', '19-09-2018', ['class'=>'form-control', 'id' => 'dt_order']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('ty', trans('mms.labels.PRODUCTION_ORDER_TYPE')) !!}
              {!! Form::label('po_type', 'PREPARACIÓN', ['class'=>'form-control', 'id' => 'po_type']) !!}
            </div>
          </div>
          <div class="col-md-7">
            <div class="form-group">
              {!! Form::label('prod', trans('mms.labels.PRODUCT')) !!}
              {!! Form::label('product', 'PRO0030 - CONCENTRADOS MASA HUT CHESSE AEROSOL-V1', ['class'=>'form-control', 'id' => 'product']) !!}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('qt', trans('wms.labels.QTY')) !!}
              {!! Form::label('qty', '3456789.00000', ['class'=>'form-control',
                                                          'style' => 'text-align: rigth;',
                                                          'id' => 'qty']) !!}
            </div>
          </div>
          <div class="col-md-1">
            <div class="form-group">
              {!! Form::label('un', trans('wms.labels.UN')) !!}
              {!! Form::label('unit', 'kg', ['class'=>'form-control', 'id' => 'unit']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('fol', trans('wms.labels.FOLIO')) !!}
              {!! Form::label('folio', '00006', ['class'=>'form-control', 'id' => 'folio']) !!}
            </div>
          </div>
          <div class="col-md-8">
            <div class="form-group">
              {!! Form::label('ide', trans('mms.labels.IDENTIFIER')) !!}
              {!! Form::label('identifier', 'OP CONCENTRADO GLASEADO', ['class'=>'form-control', 'id' => 'identifier']) !!}
            </div>
          </div>
          <div class="col-md-2">
            <div class="form-group">
              {!! Form::label('fath', trans('mms.labels.PO_FATHER')) !!}
              {!! Form::label('father', '00002', ['class'=>'form-control', 'id' => 'father']) !!}
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <table id="po_charges" class="table table-striped table-bordered no-wrap table-condensed" cellspacing="0" width="100%">
        		    <thead>
        		        <tr class="titlerow">
        		            <th data-priority="1">{{ trans('userinterface.labels.CODE') }}</th>
        		            <th>{{ trans('wms.labels.MAT_PROD') }}</th>
        								<th data-priority="1">Necesidad</th>
        								<th data-priority="1">Cargado</th>
        								<th>Consumido</th>
        								<th>{{ trans('wms.labels.UN') }}</th>
        		            <th>{{ trans('wms.labels.LOTS') }}</th>
        		        </tr>
        		    </thead>
        		    <tbody>
                </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
      </div>
    </div>

  </div>
</div>
