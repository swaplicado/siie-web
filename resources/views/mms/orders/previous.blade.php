@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $order->created_by_id))
  {{-- <a href="{{ route('mms.orders.previous', $orden->id_order) }}" title="{{ trans('mms.tooltips.PREVIOUS_STATUS') }}" --}}
  <a href="#" onclick="previous({{ json_encode($order) }})"  title="{{ trans('mms.tooltips.NEXT_STATUS') }}"
    class="btn btn-primary btn-xs">
    <span class="glyphicon glyphicon-triangle-left" aria-hidden = "true"/>
  </a>
@endif
