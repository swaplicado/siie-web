@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

<?php
    $sRoute='qms.results.store';
?>

@section('title', trans('qms.titles.CAPTURE_RESULTS').' '.$title)

    
<?php $sRoute2 = 'qms.results.index' ?>

@section('content')
    <div class="row">
        <div class="form-group">
            <div class="col-md-1  col-md-offset-1">
                <label for="lot">Lote</label>
            </div>
            <div class="col-md-4">
                <label type="text" class="form-control">{{ $oLot->lot }}</label>
            </div>
            <div class="col-md-1">
                <label for="exp_date">Venc.</label>
            </div>
            <div class="col-md-4">
                <label type="date" class="form-control">{{ (new \Carbon\Carbon($oLot->dt_expiry))->format('d-m-Y') }}</label>
            </div>
        </div>
        {!! Form::hidden('idlot', $oLot->id_lot, ['id' => 'idlot']) !!}
    </div>
    <div class="row">
        <div class="form-group">
            <div class="col-md-1 col-md-offset-1">
                <label for="item">Item</label>
            </div>
            <div class="col-md-9">
                <label type="text" class="form-control">{{ $oItem->code.' - '.$oItem->name }}</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <div class="col-md-1 col-md-offset-1">
                <label for="family">Familia</label>
            </div>
            <div class="col-md-9">
                <label type="text" class="form-control">{{ $oItem->gender->group->family->name }}</label>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <table id="capture_table_id" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <th>i</th>
                        <th>Análisis</th>
                        <th>Norma</th>
                        <th>Tipo</th>
                        <th>Min</th>
                        <th>Max</th>
                        <th>Resultado</th>
                        <th>Modif</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $i = 0;
                    ?>
                    @foreach ($lAnalysis as $oAnalysis)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>{{ $oAnalysis->code.'-'.$oAnalysis->name }}</td>
                            <td>{{ $oAnalysis->standard }}</td>
                            <td>{{ $oAnalysis->_typecode }}</td>
                            <td>{{ $oAnalysis->min_value }}</td>
                            <td>{{ $oAnalysis->max_value }}</td>
                            <td><input type="number" id="dresult_{{ $oAnalysis->id_analysis }}"
                                    name="dresult_{{ $oAnalysis->id_analysis }}"
                                    class="form-control input-sm"
                                    style="text-align: right;"
                                    value="{{ $oAnalysis->_result }}">
                            </td>
                            <td>{{ $oAnalysis->_mod_user }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/qms/lots_results/tables.js')}}"></script>
@endsection

@section('footer')
    @include('templates.footer')
@endsection