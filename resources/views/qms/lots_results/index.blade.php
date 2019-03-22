@extends('templates.basic_form')

@section('head')
  @include('templates.head.shead')
@endsection

@section('title', trans('qms.SEGREGATED_LOTS'))

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@section('titlepanel', trans('qms.SEGREGATED_LOTS'))

@section('content')
    <table id="lots_results_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        <thead>
            <tr class="titlerow">
                <th data-priority="1">Lote</th>
                <th data-priority="1" style="text-align: center;">Item</th>
                <th data-priority="1" style="text-align: center;">Cantidad</th>
                <th data-priority="1" style="text-align: center;">Un</th>
                <th data-priority="1" style="text-align: center;">Estatus</th>
                <th data-priority="1" style="text-align: center;">FQ</th>
                <th data-priority="1" style="text-align: center;">MB</th>
                <th data-priority="1" style="text-align: center;">-</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($lSegregatedLots as $oSegLot)
                <tr>
                    <td>{{ $oSegLot->lot }}</td>
                    <td>{{ $oSegLot->_item }}</td>
                    <td>{{ $oSegLot->_seg }}</td>
                    <td>{{ $oSegLot->_unit }}</td>
                    <td>{{ $oSegLot->_evtname }}</td>
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
                        <a href="{{ route('qms.results.print', [$oSegLot->id_lot]) }}"
                            class="btn btn-default btn-xs"
                            target="_blank"
                            title="{{ trans('actions.PRINT') }}">
                            <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                        </a>
					</td>
                </tr>
            @endforeach
    </table>
    @include('qms.lots_results.capture')
@endsection

@section('js')
  <script src="{{ asset('js/qms/lots_results/tables.js')}}"></script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection
