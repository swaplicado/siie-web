<a href="{{ route($v_route_destroy, $v_id) }}" style="visibility: {{ App\SUtils\SValidation::isRendered(\Config::get('scsys.OPERATION.DEL'), $actualUserPermission, $v_created_by) }};"
															class="btn btn-danger btn-sm"
															data-toggle="confirmation-popout" data-popout="true"
															data-btn-ok-label="{{ trans('messages.options.MSG_YES') }}"
															data-btn-cancel-label="{{ trans('messages.options.MSG_NO') }}"
															data-singleton="true" data-title="{{ trans('messages.confirm.MSG_CONFIRM') }}">
	<span class="glyphicon glyphicon-trash" aria-hidden = "true"/>
</a>
