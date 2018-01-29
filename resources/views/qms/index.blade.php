@extends('templates.home.modules')

@section('title', trans('qms.MODULE'))

@section('content')

  <div class="row">
    @include('templates.home.rapidaccess')
    <?php echo createBlock(asset('images/wms/box.gif'), route('qms.segregations.index', [trans('wms.STK_SEG_QLTY'),
                                                                \Config::get('scqms.SEGREGATION_TYPE.QUALITY'), \Config::get('scqms.QMS_VIEW.BY_STATUS')]),
          trans('qms.QRY_BY_STATUS'), "warning3",
          trans('qms.QRY_BY_STATUS_T'));?>
    <?php echo createBlock(asset('images/qms/boxtime.png'), route('qms.segregations.index', [trans('wms.STK_SEG_QLTY'),
                                                                \Config::get('scqms.SEGREGATION_TYPE.QUALITY'), \Config::get('scqms.QMS_VIEW.INSPECTION')]),
          trans('qms.IN_INSPECTION'), "warning3",
          trans('qms.IN_INSPECTION_T'));?>
  </div>
  <div class="row">
    <?php echo createBlock(asset('images/qms/inspection.gif'), route('qms.segregations.index', [trans('wms.STK_SEG_QLTY'),
                                                                \Config::get('scqms.SEGREGATION_TYPE.QUALITY'), \Config::get('scqms.QMS_VIEW.CLASSIFY')]),
          trans('qms.CLASSIFICATION'), "warning3",
          trans('qms.CLASSIFICATION_T'));?>
    <?php echo createBlock(asset('images/qms/lots.gif'), "#",
          trans('qms.LOTS'), "warning3",
          trans('qms.LOTS_T'));?>
  </div>
  <div class="row">
    <?php echo createBlock(asset('images/wms/barcode.gif'), "#",
          trans('wms.LBL_GENERATION'), "warning3",
          trans('wms.LBL_GENERATION_T'));?>
    <?php echo createBlock(asset('images/wms/reports.gif'), "#",
          trans('wms.REPORTS'), "warning3",
          trans('wms.REPORTS_T'));?>
  </div>

@endsection
