@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $v_created_by))
  <li>
    <a href="{{ route('siie.branches.create', $v_id) }}" title="{{ trans('userinterface.tooltips.BRANCH') }}">
      <i class="glyphicon glyphicon-home btn-sm"></i>&nbsp;{{ trans('userinterface.buttons.BRANCH') }}
    </a>
  </li>
@endif
