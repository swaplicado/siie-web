@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menu')
@endsection

@section('title', trans('userinterface.titles.LIST_ASSIGNAMENTS'))

@section('content')
	<?php $sRoute='admin.userPermissions' ?>
	@section('create')
		@include('templates.form.create')
	@endsection
	<table data-toggle="table" class="table table-striped">
		<thead>
			<th data-sortable="true">{{ trans('userinterface.labels.USER') }}</th>
			<th data-sortable="true">{{ trans('userinterface.labels.PERMISSION') }}</th>
      <th data-sortable="true">{{ trans('userinterface.labels.PRIVILEGE') }}</th>
      <th>Acciones</th>
		</thead>
		<tbody>
			@foreach($userPermissions as $userPermission)
				<tr>
					<td>{{ $userPermission->user->username }}</td>
					<td>{{ $userPermission->permission->name }}</td>
					<td>{{ $userPermission->privilege->name }}</td>
					<td>
						<?php
								$oRegistry = $userPermission;
								$iRegistryId = $userPermission->id_usr_per;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
									\Config::get('scsys.OPTIONS.DESTROY'),
									\Config::get('scsys.OPTIONS.ACTIVATE'),
								];
						?>
						@include('templates.list.options')
					</td>
				</tr>
			@endforeach
		</tbody>
	</table>
	<div class="container">
    <div class="row">
        <div class="col-xs-12 col-sm-6 col-md-4 col-sm-offset-3 col-md-offset-4">
            <div class="panel panel-default">
                <!-- Default panel contents -->
                <div class="panel-heading">Material Design Switch Demos</div>
								<div style="overflow: auto; height:300px;">
									<!-- List group -->
									{{-- @foreach ($permissions as $permission) --}}
											<li class="list-group-item">
													Bootstrap Switch Success
													<div class="material-switch pull-right">
															<input id="is_name" type="checkbox" value="1" />
															<label for="is_name" class="label-success"></label>
													</div>
											</li>
									{{-- @endforeach --}}
								</div>
            </div>
        </div>
    </div>
</div>
	{!! $userPermissions->render() !!}
@endsection
