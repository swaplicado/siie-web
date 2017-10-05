<a href="{{ route($v_route_edit, $v_id) }}" data-toggle = "editar" title="{{ trans('userinterface.tooltips.EDIT') }}"
																						style="visibility: {{ App\SUtils\SValidation::isRendered(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $v_created_by) }};"
																						class="btn btn-info">
	<span class="glyphicon glyphicon-pencil" aria-hidden = "true"/>
</a>
