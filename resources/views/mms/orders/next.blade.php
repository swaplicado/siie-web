@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $order->created_by_id))
  {{-- <a href="{{ route('mms.orders.next', $orden->id_order) }}" title="{{ trans('mms.tooltips.NEXT_STATUS') }}" --}}
  <a href="#" onclick="next({{ json_encode($order) }})"  title="{{ trans('mms.tooltips.NEXT_STATUS') }}"
    class="btn btn-success btn-xs">
    <span class="glyphicon glyphicon-triangle-right" aria-hidden = "true"/>
  </a>
@endif
