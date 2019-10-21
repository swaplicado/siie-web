@extends('templates.home.modules')

@section('title',  trans('wms.REPORTS'))

@section('content')
    <div id="reportsApp">
        <div class="row">
            <div class="col-md-4">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#phReport">{{ trans('qms.REPORT_PH') }}</button>
            </div>
        </div>
    </div>
    @include('qms.reports.modal_rep_ph')
@endsection

@section('js')

<script>
    $(".chosen-select").chosen({ width: "100%" }); 
</script>

@endsection