@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $v_created_by))
  <li>
    <a href="{{ route('siie.address.index', $v_id) }}" title="{{ trans('userinterface.tooltips.ADDRESS') }}">
      <i class="glyphicon glyphicon-home btn-sm"></i>&nbsp;{{ trans('userinterface.buttons.ADDRESS') }}
    </a>
  </li>
@endif
