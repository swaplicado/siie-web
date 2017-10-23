@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($gender))
	<?php
			$aux = $gender;
			if (isset($bIsCopy))
			{
				$sRoute = 'siie.genders.store';
			}
			else
			{
				$sRoute = 'siie.genders.update';
			}
	?>
	@section('title', trans('userinterface.titles.EDIT_GENDER'))
@else
	<?php
		$sRoute='siie.genders.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_GENDER'))
@endif
	<?php $sRoute2 = 'siie.genders.index' ?>

@section('content')

	<div class="row">
	  <div class="col-md-6">

			<div class="form-group">
				{!! Form::label('name', trans('userinterface.labels.GENDER').'*') !!}
				{!! Form::text('name',
					isset($gender) ? $gender->name : null , ['class'=>'form-control', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																																	'placeholder' => trans('userinterface.placeholders.GENDER'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('item_group_id', trans('userinterface.labels.GROUP')) !!}
				{!! Form::select('item_group_id', $groups, isset($gender) ?  $gender->item_group_id : null , ['class'=>'form-control select-one',
					'placeholder' => trans('userinterface.placeholders.SELECT_GROUP'), 'required']) !!}
			</div>
		</div>

	  <div class="col-md-6">

			<div class="form-group">
				{!! Form::label('item_class_id', trans('userinterface.labels.CLASS')) !!}
				{!! Form::select('item_class_id', $classes, isset($gender) ?  $gender->item_class_id : null, ['class'=>'form-control select-one',
											'placeholder' => trans('userinterface.placeholders.SELECT_CLASS'), 'required']) !!}
			</div>

			<div class="form-group">
				{!! Form::label('item_type', trans('userinterface.labels.TYPE')) !!}
				<select class="form-control select-one" name="item_type_id" id="item_type_id" value="{{ isset($gender) ?  $gender->item_type_id : null }}">
					@if (isset($gender))
						<option value="{{ $gender->item_type_id }}">{{ $gender->type->name }}</option>
					@else
							<option value="">{{ trans('userinterface.placeholders.SELECT_TYPE') }}</option>
					@endif
				</select>
			</div>

		</div>
	</div>


@endsection
