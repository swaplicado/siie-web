@extends('templates.list.mainlist')

@section('menu')
	@include('templates.menu.menu')
@endsection

@section('title', trans('userinterface.titles.LIST_USERS').' sucursales')

@section('content')
	<?php $sRoute='admin.userBranches' ?>

	<table data-toggle="table" class="table table-condensed">
		<thead>
			<th data-sortable="true">{{ trans('userinterface.labels.NAME') }}</th>
			@foreach($branches as $branch)
				<th data-sortable="true">{{ $branch->name }}</th>
			@endforeach
		</thead>

		<tbody>
			@foreach($users as $user)
				<tr>
					<td>{{ $user->user->username }}</td>

							@foreach($branches as $branch)
							 <td>
									<?php
										$hasAccess = false;
									?>
									@foreach($user->user->userBranches as $acces)
											@if ($branch->id_branch == $acces->branch_id && $user->user_id == $acces->user_id)
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
