@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($lots))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'mms.floors.store';
			}
			else
			{
				$sRoute = 'mms.floors.update';
			}
			$aux = $floors;
	?>
	@section('title', trans('userinterface.titles.EDIT_FLOORS'))
@else
	<?php
		$sRoute='mms.floors.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_FLOORS'))
@endif
	<?php $sRoute2 = 'mms.floors.index' ?>

@section('content')

			<div class="form-group">
				<div class="col-md-12">
					<div class="form-group row">
						{!! Form::label('code', trans('userinterface.labels.CODE').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::text('code',
					    	isset($floors) ? $floors->code : null , ['required','class'=>'form-control',
									'maxlength' => '50', 'placeholder' => trans('userinterface.placeholders.CODE'), 'required']) !!}
						</div>
            {!! Form::label('name', trans('userinterface.labels.NAME').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::text('name',
					    	isset($floors) ? $floors->name : null , ['required','class'=>'form-control',
									'maxlength' => '50', 'placeholder' => trans('userinterface.placeholders.NAME'), 'required']) !!}
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<div class="form-group row">
						{!! Form::label('branch', trans('userinterface.labels.BRANCH').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::select('branch_id', $branches, 	isset($floors) ? $floors->branch->id_branch : null,
																['class'=>'form-control select-one', 'placeholder' => trans('userinterface.placeholders.BRANCH')]) !!}
						</div>
					</div>
				</div>
			</div>

@endsection

@section('js')

	@endsection
