@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $orden->created_by_id))
  <a href="{{ route('mms.orders.previous', $orden->id_order) }}" title="{{ trans('mms.tooltips.PREVIOUS_STATUS') }}"
    class="btn btn-primary btn-xs">
    <span class="glyphicon glyphicon-triangle-left" aria-hidden = "true"/>
  </a>
@endif
