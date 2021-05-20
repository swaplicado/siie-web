@extends('templates.home.modules')

@section('title', trans('wms.MODULE'))

@section('content')

  <div class="row">
    @include('templates.home.rapidaccess')
    <?php echo createBlock(asset('images/wms/box.gif'), route('wms.stock.index', [\Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE'), trans('wms.WHS_STK')]), trans('wms.QRY_INVENTORY'), "success3", trans('wms.QRY_INVENTORY_T'));?>
    <?php echo createBlock(asset('images/wms/movsan.gif'), route('wms.movs.index'), trans('wms.MOV_WAREHOUSES'), "success3", trans('wms.MOV_WAREHOUSES_T'));?>
  </div>
  <div class="row">
    <?php echo createBlock(asset('images/wms/monta.gif'), route('wms.docs.index',
                                                    [\Config::get('scsiie.DOC_CAT.SALES'),
                                                    \Config::get('scsiie.DOC_CLS.ORDER'),
                                                    \Config::get('scsiie.DOC_TYPE.ORDER'),
                                                    \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                    \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                    trans('userinterface.titles.LIST_INVS_SAL_BY_SUPP')]),
                                                    trans('wms.DOC_ASSORTMENT'), "success3", trans('wms.DOC_ASSORTMENT_T'));?>
    <?php echo createBlock(asset('images/wms/movss.gif'), route('wms.docs.index',
                                                    [\Config::get('scsiie.DOC_CAT.SALES'),
                                                    \Config::get('scsiie.DOC_CLS.ADJUST'),
                                                    \Config::get('scsiie.DOC_TYPE.CREDIT_NOTE'),
                                                    \Config::get('scwms.DOC_VIEW.NORMAL'),
                                                    \Config::get('scwms.DOC_VIEW_S.BY_SUPP'),
                                                    trans('userinterface.titles.LIST_CN_SAL_BY_SUPP')]),
                                                    trans('wms.DOC_RETURNS'), "success3", trans('wms.DOC_RETURNS_T'));?>
  </div>
  <div class="row">
      <?php echo createBlock(asset('images/mms/production-line.gif'),
            \App\SUtils\SValidation::hasPermissionByType(\Config::get('scperm.TP_PERMISSION.BRANCH'),
                          \Config::get('scperm.PERMISSION.MMS_PROD_ORDERS_ASSIGNAMENTS'))
                          ?
                            route('siie.pos.index', 1) 
                          : '#',
                        trans('mms.DELIVERY_FP'), "primary3", trans('mms.DELIVERY_FP_T'));?>
    <?php echo createBlock(asset('images/wms/reports.gif'), route('wms.codes.consult'), trans('wms.REPORTS'), "success3", trans('wms.REPORTS_T'));?>
  </div>

@endsection
