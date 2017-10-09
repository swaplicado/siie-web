@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menu')
@endsection

@section('title', trans('userinterface.titles.LIST_USERS'))

@section('content')
	<?php $sRoute='admin.usraccess' ?>

	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th data-sortable="true">{{ trans('userinterface.labels.NAME') }}</th>
			@foreach($companies as $company)
				<th data-sortable="true">{{ $company->name }}</th>
			@endforeach
		</thead>
		<tbody>
			@foreach($users as $user)
				<tr>
					<td>{{ $user->username }}</td>

							@foreach($companies as $company)
							 <td>
									<?php
										$hasAccess = false;
									?>
									@foreach($user->userCompanies as $acces)
											@if ($company->id_company == $acces->company_id && $user->id == $acces->user_id)
												<?php
													$hasAccess = true;
												?>
										  @endif
									@endforeach
									@if ($hasAccess)
											<span class="label label-success">{{ trans('siie.ACCESS.PERMITTED') }}</span>
									@else
											<span class="label label-danger">{{ trans('siie.ACCESS.RESTRICTED') }}</span>
									@endif
							 </td>
							@endforeach

					<td>
						<?php
								$oRegistry = $user;
								$iRegistryId = $user->id;
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
		{!! $users->render() !!}
@endsection
