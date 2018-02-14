<?php
		$v_id = $iRegistryId;
		$v_route_edit = $sRoute.'.edit';
		$v_route_destroy = $sRoute.'.destroy';
		$v_created_by = $oRegistry->created_by_id;
?>

@if (in_array(\Config::get('scsys.OPTIONS.EDIT'), $loptions) &&
		App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $v_created_by))
			@include('templates.list.edit')
@endif
@if (in_array(\Config::get('scsys.OPTIONS.DESTROY'), $loptions) &&
		App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.DESTROY'), $actualUserPermission, $v_created_by))
			@include('templates.list.destroy')
@endif

@if (sizeof($loptions) > 2)
	<div class="btn-group">
		<button type="button" class="btn btn-secondary dropdown-toggle btn-sm" data-toggle="dropdown">
			<span  class="caret"></span>
			<span class="sr-only">Toggle Dropdown</span>
		</button>
		@if (App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.EDIT'), $actualUserPermission, $v_created_by))
			<ul class="dropdown-menu" role="menu">
				@if (in_array(\Config::get('scsys.OPTIONS.ACTIVATE'), $loptions))
					@include('templates.list.activate')
				@endif
				<li class="divider"></li>
				@if (in_array(\Config::get('scsys.OPTIONS.COPY'), $loptions))
					@include('templates.list.duplicate')
				@endif
				@if (in_array(\Config::get('scsys.OPTIONS.MOD_PASS'), $loptions) &&
						App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.SUPER'), $actualUserPermission, $v_created_by))
					@include('templates.list.changepass')
				@endif
				@if (in_array(\Config::get('scsys.OPTIONS.MOD_PASS'), $loptions) &&
						App\SUtils\SValidation::isRenderedB(\Config::get('scsys.OPERATION.SUPER'), $actualUserPermission, $v_created_by))
					@include('templates.list.usraccess')
				@endif
				@if (in_array(\Config::get('scsys.OPTIONS.NEW_BRANCH'), $loptions))
					@include('templates.list.branch')
				@endif
				@if (in_array(\Config::get('scsys.OPTIONS.ADDRESS'), $loptions))
					@include('templates.list.address')
				@endif
			</ul>
		@endif
	</div>
@endif
