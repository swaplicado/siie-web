@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('mms.labels.FORMULAS_DETAIL'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('mms.labels.FORMULAS_DETAIL'))

@section('content')

  <?php $sRoute="mms.formulas"?>

  @section('filters')
    {!! Form::open(['route' => $sRoute.'.indexdetail',
      'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
      <div class="form-group">
        <div class="input-group">
          {!! Form::text('name', null, ['class' => 'form-control',
                                        'placeholder' => trans('userinterface.placeholders.SEARCH'),
                                        'aria-describedby' => 'search']) !!}
          @include('templates.list.search')
          <span class="input-group-btn">
            <button id="searchbtn" type="submit" class="form-control">
              <span class="glyphicon glyphicon-search"></span>
            </button>
          </span>
        </div>
      </div>
      {!! Form::close() !!}
    @endsection

  @section('create')
    @include('templates.form.create')
  @endsection
  <div class="row">
    <table
    {{-- id="formulas_detail_table"  --}}
    class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1" style="text-align: center;">{{ trans('siie.labels.CODE') }}</th>
                <th>{{ trans('mms.labels.INGREDIENT') }}</th>
                <th data-priority="1" style="text-align: center;">{{ trans('siie.labels.QUANTITY') }}</th>
                <th data-priority="1" style="text-align: center;">{{ trans('wms.labels.UN') }}</th>
                <th data-priority="1">{{ trans('mms.labels.ID_FORMULA') }}</th>
                <th data-priority="1" style="text-align: center;">V.</th>
                <th style="text-align: center;">{{ trans('siie.labels.DATE') }}</th>
                <th data-priority="1" style="text-align: center;">{{ trans('siie.labels.KEY') }}</th>
                <th>{{ trans('mms.labels.MAT_PROD_FORM') }}</th>
                <th data-priority="1" style="text-align: center;">{{ trans('wms.labels.UN') }}</th>
                <th style="text-align: center;">{{ trans('siie.labels.STATUS') }}</th>
                <th style="text-align: center;">{{ trans('siie.labels.OPTIONS') }}</th>
            </tr>
        </thead>
        <tbody>
          @foreach ($formulas as $formula)
            <tr>
                <td style="text-align: center;">{{ $formula->item_code_row }}</td>
                <td>{{ $formula->item_row }}</td>
                <td align="right">{{ session('utils')->formatNumber($formula->quantity, \Config::get('scsiie.FRMT.QTY')) }}</td>
                <td style="text-align: center;">{{ $formula->unit_code_row }}</td>
                <td>{{ $formula->identifier }}</td>
                <td style="text-align: center;">{{ $formula->version }}</td>
                <td style="text-align: center;">{{ $formula->dt_date }}</td>
                <td style="text-align: center;">{{ $formula->item_code }}</td>
                <td>{{ $formula->item }}</td>
                <td style="text-align: center;">{{ $formula->unit_code }}</td>
                <td style="text-align: center;">
      						@if (! $formula->is_deleted)
      								<span class="label label-success">{{ trans('userinterface.labels.ACTIVE') }}</span>
      						@else
      								<span class="label label-danger">{{ trans('userinterface.labels.INACTIVE') }}</span>
      						@endif
      					</td>
                <td style="text-align: center;">
      						<?php
      								$oRegistry = $formula;
      								$iRegistryId = $formula->id_formula;
      								$loptions = [
      									\Config::get('scsys.OPTIONS.EDIT'),
      									\Config::get('scsys.OPTIONS.DESTROY'),
      									\Config::get('scsys.OPTIONS.ACTIVATE'),
      									\Config::get('scsys.OPTIONS.COPY'),
      								];
      						?>
      						@include('templates.list.options')
                  <a href="{{ route('mms.formulas.create', [$formula->id_formula]) }}" title="Nueva Versión"
                    class="btn btn-primary btn-xs">
                    <span class="glyphicon glyphicon-expand" aria-hidden = "true"/>
                  </a>
      					</td>
            </tr>
          @endforeach
        </tbody>
    </table>
    {!! $formulas->render() !!}
  </div>
@endsection

@section('js')
  <script src="{{ asset('js/formulas/table.js')}}"></script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
