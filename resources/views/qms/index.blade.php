@extends('templates.home.modules')

@section('title', trans('qms.MODULE'))

@section('content')

  <div class="row">
    @include('templates.home.rapidaccess')
    <?php echo createBlock(asset('images/wms/box.gif'),
          route('qms.segregations.index',
          [
            trans('qms.VIEW_INS_PALLET'),
            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
            \Config::get('scqms.TYPE_VIEW.BY_PALLET')
          ]),
          trans('qms.QRY_BY_STATUS'), "warning3",
          trans('qms.QRY_BY_STATUS_T'));?>
    <?php echo createBlock(asset('images/qms/boxtime.png'),
          route('qms.segregations.index',
          [
            trans('qms.VIEW_INS_PALLET'),
            \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
            \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
            \Config::get('scqms.TYPE_VIEW.BY_PALLET')
          ]),
          trans('qms.IN_INSPECTION'), "warning3",
          trans('qms.IN_INSPECTION_T'));?>
  </div>
  <div class="row">
    <?php echo createBlock(asset('images/qms/inspection.gif'),
                                      route('qms.segregations.index',
                                      [
                                        trans('qms.VIEW_INS_PALLET'),
                                        \Config::get('scqms.SEGREGATION_TYPE.INSPECTED'),
                                        \Config::get('scqms.QMS_VIEW.INSPECTIONCLASSIFY'),
                                        \Config::get('scqms.TYPE_VIEW.BY_PALLET')
                                      ]),
          trans('qms.CLASSIFICATION'), "warning3",
          trans('qms.CLASSIFICATION_T'));?>
    <?php echo createBlock(asset('images/qms/lots.gif'), "#",
          trans('qms.LOTS'), "warning3",
          trans('qms.LOTS_T'));?>
  </div>
  <div class="row">
    <?php echo createBlock(asset('images/wms/barcode.gif'), "codes/start",
                trans('wms.LBL_GENERATION'), "warning3",
                trans('wms.LBL_GENERATION_T'));?>
    <?php echo createBlock(asset('images/wms/reports.gif'), "#",
          trans('wms.REPORTS'), "warning3",
          trans('wms.REPORTS_T'));?>
  </div>

@endsection
