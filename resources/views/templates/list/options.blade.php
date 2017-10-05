<?php
		$v_id = $iRegistryId;
		$v_route_edit = $sRoute.'.edit';
		$v_route_destroy = $sRoute.'.destroy';
		$v_created_by = $oRegistry->created_by_id;
?>

@include('templates.list.edit')
@include('templates.list.destroy')

<div class="btn-group">
	<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown">
		<span  class="caret"></span>
		<span class="sr-only">Toggle Dropdown</span>
	</button>
	@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $v_created_by))
		<ul class="dropdown-menu" role="menu">
			@include('templates.list.activate')
			<li class="divider"></li>
			@include('templates.list.duplicate')
		</ul>
	@endif
</div>
