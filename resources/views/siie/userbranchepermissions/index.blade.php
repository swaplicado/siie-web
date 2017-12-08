@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menu')
@endsection

@section('title', 'Lista de permisos de usuario por sucursal')

@section('content')
	<?php $sRoute='siie.userpermissionbranche' ?>

	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th data-sortable="true">{{ trans('userinterface.labels.NAME') }}</th>
				<th data-sortable="true">ACCIONES</th>
		</thead>

		<tbody>
			@foreach($users as $user)
				<tr>
					<td>{{ $user->user->username }}</td>
					<td>
						<?php
								$oRegistry = $user;
								$iRegistryId = $user->user->id;
								$loptions = [
									\Config::get('scsys.OPTIONS.EDIT'),
								];
						?>
						@include('templates.list.options')
					</td>

				</tr>
			@endforeach
		</tbody>
	</table>

@endsection
