@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('qms.ANALYSIS_BY_LOT'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('qms.ANALYSIS_BY_LOT'))

@section('content')
    @section('filters')
        {!! Form::open(['route' => 'qms.results.index',
        'method' => 'GET', 'class' => 'navbar-form pull-right']) !!}
        <div class="form-group">
            <div class="input-group">
            <span class="input-group-btn">
                {!! Form::text('filterDate', $sFilterDate, ['class' => 'form-control', 'id' => 'filterDate']); !!}
            </span>
            <span class="input-group-btn">
                <button id="searchbtn" type="submit" class="form-control">
                <span class="glyphicon glyphicon-search"></span>
                </button>
            </span>
            </div>
        </div>
    {!! Form::close() !!}
    @endsection
    <table id="lots_results_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1">{{ trans('wms.labels.LOT')  }}</th>
                <th>{{ trans('wms.labels.EXPIRATION') }}</th>
                <th style="text-align: center;">{{ trans('wms.labels.MAT_PROD') }}</th>
                <th style="text-align: center;">{{ trans('wms.labels.UN')  }}</th>
                <th data-priority="1" style="text-align: center;">FQ</th>
                <th data-priority="1" style="text-align: center;">MB</th>
                <th data-priority="1" style="text-align: center;">-</th>
                <th><span class="glyphicon glyphicon-list-alt"></span></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lSegregatedLots as $oSegLot)
                <tr>
                    <td>{{ $oSegLot->lot }}</td>
                    <td>{{ $oSegLot->dt_expiry }}</td>
                    <td>{{ $oSegLot->_item }}</td>
                    <td>{{ $oSegLot->_unit }}</td>
                    <td>
                        <a href="{{ route('qms.results.create', [$oSegLot->id_lot, \Config::get('scqms.ANALYSIS_TYPE.FQ')]) }}"
                            class="btn btn-info btn-xs"
                            title="{{ trans('qms.titles.CAPTURE_RESULTS').' '.trans('qms.labels.PHYSIOCHEMICALS') }}">
                            <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
                        </a>
					</td>
                    <td>
                        <a href="{{ route('qms.results.create', [$oSegLot->id_lot, \Config::get('scqms.ANALYSIS_TYPE.MB')]) }}"
                            class="btn btn-success btn-xs"
                            title="{{ trans('qms.titles.CAPTURE_RESULTS').' '.trans('qms.labels.MICROBIOLOGICALS') }}">
                            <span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span>
                        </a>
					</td>
                    <td>
                        <a class="btn btn-default btn-xs"
                            onclick='onPrint( {{ $oSegLot->id_lot }}, "{{ date("Y-m-d") }}")'
                            title="{{ trans('actions.PRINT') }}">
                            <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                        </a>
                    </td>
                    <td>{{ $oSegLot->_nresults }}</td>
                </tr>
            @endforeach
    </table>
    @include('qms.lots_results.prevprint')
@endsection

@section('js')
  <script src="{{ asset('js/qms/lots_results/tables.js')}}"></script>
  <script src="{{ asset('js/qms/lots_results/SModal.js')}}"></script>
  <script src="{{ asset('moment/moment.js') }}"></script>
  <script src="{{ asset('daterangepicker/daterangepicker.js') }}"></script>

  <script>
        $(function() {
          $('input[id="filterDate"]').daterangepicker({
            locale: {
                   format: 'DD/MM/YYYY'
               }
          });
        });
    </script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
