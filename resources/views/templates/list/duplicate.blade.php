@if (\Route::has($sRoute.'.copy'))
  <li><a href="{{ route($sRoute.'.copy', $v_id) }}"
        style="visibility: {{ App\SUtils\SValidation::isRendered(\Config::get('scsys.OPERATION.CREATE'), $actualUserPermission, $v_created_by) }};">
        <i class="glyphicon glyphicon-duplicate btn-sm"></i>&nbsp;{{ trans('userinterface.buttons.DUPLICATE') }}
      </a>
  </li>
@endif
