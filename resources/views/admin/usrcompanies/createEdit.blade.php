@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menu')
@endsection

@if(isset($user))
	<?php
			$aux = $user;
			if (isset($bIsCopy))
			{
				$sRoute = 'admin.usraccess.store';
			}
			else
			{
				$sRoute = 'admin.usraccess.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_USER'))
@else
	<?php
		$sRoute = 'admin.usraccess.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_USER'))
@endif
	<?php $sRoute2='admin.usraccess.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('username', trans('userinterface.labels.NAME').'*') !!}
			{!! Form::text('username',
				isset($user) ? $user->username : null , ['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.NAME'), 'required', 'readonly']) !!}
		</div>

		<div class="form-group">
			<div class="row">
				<div class="col-md-1"></div>
				@foreach ($companies as $company)
					<div class="col-md-1">
						<div class="row">
							{!! Form::label($company->name) !!}
						</div>
						<div class="row">
							{!! Form::checkbox($company->id_company, 1, App\SUtils\SValidation::canAccessToCompany($user, $company->id_company), ['class' => 'form-control']) !!}
						</div>
					</div>
				@endforeach
			</div>
		</div>


@endsection
