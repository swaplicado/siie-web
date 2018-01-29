@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menu')
@endsection

@section('title', trans('userinterface.titles.LIST_USERS'))

@section('content')
	<?php $sRoute='admin.userwhs' ?>

	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th data-sortable="true">{{ trans('userinterface.labels.NAME') }}</th>
		</thead>

		<tbody>
			@foreach($users as $user)
				<tr>
					<td>{{ $user->user->username }}</td>
					<td>
						<a href="{{ route('admin.userwhs.edit', $user->user->id) }}" class="btn btn-info"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>
					</td>

				</tr>
			@endforeach
		</tbody>
	</table>

@endsection
