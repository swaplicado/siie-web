@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menu')
@endsection


	<?php
        $aux = $user;
        $sRoute = 'admin.userBranches.update';
	?>

	@section('title', 'Acceso a sucursales')

	<?php $sRoute2='admin.userBranches.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('username', trans('userinterface.labels.NAME').'*') !!}
			{!! Form::text('username',
				isset($user) ? $user->username : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.NAME'), 'required', 'readonly']) !!}
		</div>

		<div class="form-group">
			<div class="row">
				<div class="col-md-1"></div>
				@foreach ($branches as $branch)
					<div class="col-md-2">
							{!! Form::checkbox($branch->id_branch, 1, App\SUtils\SValidation::canAccessToBranch($user, $branch->id_branch)) !!}{!! Form::label($branch->name) !!}
					</div>
				@endforeach
			</div>
		</div>


@endsection
