@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $v_created_by))
  <li>
    <a href="{{ route('siie.branches.create', $v_id) }}" title="{{ trans('userinterface.tooltips.EDIT') }}">
      <i class="glyphicon glyphicon-home"></i>&nbsp;{{ trans('userinterface.buttons.BRANCH') }}
    </a>
  </li>
@endif
