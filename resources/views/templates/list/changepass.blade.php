@if (\Route::has($sRoute.'.changepass'))
  <li><a href="{{ route($sRoute.'.changepass', $v_id) }}">
        <i class="glyphicon glyphicon-lock btn-sm"></i>&nbsp;{{ trans('userinterface.buttons.PASS') }}
      </a>
  </li>
@endif
