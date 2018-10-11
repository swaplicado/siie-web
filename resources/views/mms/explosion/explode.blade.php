@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', $sTitle)
@section('titlepanel', $sTitle)

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('content')
  {!! Form::open(['route' => 'mms.explosion.show', 'method' => 'GET']) !!}
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('branch', trans('wms.labels.UNIT')	) !!}
          {!! Form::label('branch', session('branch')->code.'-'.session('branch')->name,
                          ['id' => 'branch', 'class' => 'form-control input-sm']) !!}
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('dt_date', trans('userinterface.labels.DATE').'*') !!}
          {!! Form::date('dt_date', session('work_date'),
                                            ['class'=>'form-control input-sm']) !!}
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
          <div class="form-group">
            {!! Form::label('warehouses', trans('wms.labels.WAREHOUSES').'*'	) !!}
            {!! Form::select('warehouses', $lWarehouses, null,
                      ['class'=>'form-control chzn-select',
                      'data-placeholder' => trans('wms.placeholders.SELECT_WAREHOUSES'),
                      'required',
                      'multiple']) !!}
            {!! Form::hidden('warehouses_array', -1, ['id' => 'warehouses_array']) !!}
          </div>
      </div>
      <div class="col-md-6">
          <div class="row">
              <div class="col-md-6">
                  {!! Form::label('radio', trans('mms.labels.EXPLODE_BY_ORDER')) !!}
                  {!! Form::radio('explosion_by', '1', true, ['id' => 'by_order',
                                                              'onChange' => 'explosionByChange()',
                                                              'class' => 'form-control input-sm']) !!}
              </div>
              <div class="col-md-6">
                  {!! Form::label('radio', trans('mms.labels.EXPLODE_BY_PLAN')) !!}
                  {!! Form::radio('explosion_by', '2', false, ['id' => 'by_plan',
                                                              'onChange' => 'explosionByChange()',
                                                              'class' => 'form-control input-sm']) !!}
              </div>
          </div>
          <div class="row" id="div_plan"  style="display: none;">
            <div class="col-md-12">
              <div class="form-group">
                {{-- {!! Form::label('production_plan', trans('mms.placeholders.SELECT_PROD_PLAN').'*') !!} --}}
                {!! Form::select('production_plan', $lPlanes, null,
                          ['class'=>'form-control select-one',
                          'required',
                          'id' => 'production_plan',
                          'placeholder' => trans('mms.placeholders.SELECT_PROD_PLAN')]) !!}
              </div>
            </div>
          </div>
          <br>
          <div class="row" id="div_order">
            <div class="col-md-12">
              <div class="form-group">
                {{-- {!! Form::label('production_order', trans('mms.placeholders.SELECT_PROD_ORDER').'*') !!} --}}
                {!! Form::select('production_order', $lOrders, null,
                          ['class'=>'form-control select-one',
                          'required',
                          'id' => 'production_order',
                          'placeholder' => trans('mms.placeholders.SELECT_PROD_ORDER')]) !!}
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-12">
              <div class="form-group">
                {!! Form::label('explode_sub', trans('mms.labels.EXPLODE_SUB')) !!}
                {!! Form::checkbox('explode_sub', 1, false, ['class' => 'form-control']) !!}
              </div>
            </div>
          </div>
      </div>
    </div>
    <div class="row">
        <div class="col-md-3 col-md-offset-9">
          <div class="form-group" align="right">
            {!! Form::submit(trans('actions.EXPLODE'), ['class' => 'btn btn-primary', 'onClick' => 'setWarehouses()']) !!}
            <input type="button" name="{{ trans('actions.CANCEL') }}" value="{{ trans('actions.CANCEL') }}"
                    class="btn btn-danger" onClick="location.href='{{ route('mms.formulas.index') }}'"/>
          </div>
        </div>
    </div>
  {!! Form::close() !!}
@endsection

@section('js')
  <script src="{{ asset('js/mms/explosion/SExplosionCore.js') }}" charset="utf-8"></script>
  <script type="text/javascript">
      $('.chzn-select').chosen();

      function setWarehouses() {
        var selectedValues = [];
        $(".chzn-select :selected").each(function() {
          selectedValues.push($(this).attr('value'));
        });

        console.log(selectedValues);
        document.getElementById('warehouses_array').value = JSON.stringify(selectedValues);
      }
  </script>
@endsection
