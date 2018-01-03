@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menu')
@endsection

@section('title', trans('userinterface.titles.LIST_USERS'))

@section('content')
	<?php $sRoute='siie.userwhs' ?>

	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th data-sortable="true">{{ trans('userinterface.labels.NAME') }}</th>
		</thead>

		<tbody>
			@foreach($users as $user)
				<tr>
					<td>{{ $user->user->username }}</td>
					<td>
						<a href="{{ route('siie.userwhs.edit', $user->user->id) }}" class="btn btn-warning"><span class="glyphicon glyphicon-piggy-bank" aria-hidden="true"></span></a>
					</td>

				</tr>
			@endforeach
		</tbody>
	</table>

@endsection
