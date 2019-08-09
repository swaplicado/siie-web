@extends('templates.home.modules')

@section('title', trans('mms.MODULE'))

@section('content')

  <div class="row">
    @include('templates.home.rapidaccess')
    <?php echo createBlock(asset('images/wms/box.gif'), route('wms.stock.index',
                                                      [\Config::get('scwms.STOCK_TYPE.STK_BY_WAREHOUSE'),
                                                      trans('wms.WHS_STK')]),
                                    trans('wms.QRY_INVENTORY'), "primary3", trans('wms.QRY_INVENTORY_T')); ?>

    <?php echo createBlock(asset('images/mms/ingred_list.gif'), route('mms.formulas.index'),
                                    trans('mms.FORMULAS'), "primary3", trans('mms.FORMULAS_T')); ?>
  </div>
  <div class="row">
    <?php echo createBlock(asset('images/wms/folder.gif'), route('mms.orders.index'),
                                    trans('mms.PROD_ORDER'), "primary3", trans('mms.PROD_ORDER_T'));?>

    <?php echo createBlock(asset('images/wms/whss.gif'), route('mms.explosion.index'),
                                    trans('mms.EXPL_MAT'), "primary3", trans('mms.EXPL_MAT_T'));?>
  </div>
  <div class="row">
    <?php echo createBlock(asset('images/mms/production-line.gif'),
                        \App\SUtils\SValidation::hasPermissionByType(
                                      \Config::get('scperm.TP_PERMISSION.BRANCH'),
                                      \Config::get('scperm.PERMISSION.MMS_PROD_ORDERS_ASSIGNAMENTS')
                                      )
                                      ?
                                        route('siie.pos.index') 
                                      : '#',
                                    trans('mms.DELIVERY_FP'), "primary3", trans('mms.DELIVERY_FP_T'));?>


    <?php echo createBlock(asset('images/wms/reports.gif'), route('siie.import.pos'),
                                    trans('wms.REPORTS'), "primary3", trans('wms.REPORTS_T'));?>
  </div>

@endsection
