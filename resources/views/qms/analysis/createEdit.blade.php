@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($oAnalysis))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'qms.analysis.store';
			}
			else
			{
				$sRoute = 'qms.analysis.update';
			}

			$aux = $oAnalysis;
	?>
	@section('title', trans('qms.titles.EDIT_ANALYSIS'))
@else
	<?php
		$sRoute='qms.analysis.store';
	?>
	@section('title', trans('qms.titles.CREATE_ANALYSIS'))
@endif
	<?php $sRoute2 = 'qms.analysis.index' ?>

@section('content')

			<div class="form-group">
				<div class="col-md-12">
					<div class="form-group row">
						{!! Form::label('code', trans('userinterface.labels.CODE').'*',['class'=>'col-md-3 control-label']) !!}
						<div class="col-md-5">
								{!! Form::text('code',
									isset($oAnalysis) ? $oAnalysis->code : '',
																		['class'=>'form-control',
																		'maxlength' => '5',
																		'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																		'placeholder' => 'Máximo 5 caracteres',
																		'required']) !!}
						</div>
					</div>
					<div class="form-group row">
						{!! Form::label('name', trans('qms.labels.ANALYSIS_NAME').'*', ['class'=>'col-md-3 control-label']) !!}
						<div class="col-md-5">
							{!! Form::text('name',
									isset($oAnalysis) ? $oAnalysis->name : '',
																		['class'=>'form-control',
																		'placeholder' => 'ACIDEZ [%]',
																		'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																		'required']) !!}
						</div>
					</div>
					<div class="form-group row">
						{!! Form::label('standard', trans('qms.labels.STANDARD').'*', ['class'=>'col-md-3 control-label']) !!}
						<div class="col-md-5">
							{!! Form::text('standard',
									isset($oAnalysis) ? $oAnalysis->standard : '',
																		['class'=>'form-control',
																		'placeholder' => 'NOM-111-SSA1-1994',
																		'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																		'required']) !!}
						</div>
					</div>
					<div class="form-group row">
						{!! Form::label('min_value', trans('qms.labels.MIN_VALUE').'*', ['class'=>'col-md-3 control-label']) !!}
						<div class="col-md-3 col-md-offset-2">
							{!! Form::number('min_value',
									isset($oAnalysis) ? $oAnalysis->min_value : '',
																		['class'=>'form-control',
																		'value' => 0,
																		'placeholder' => "1.0",
																		'step'=> "0.01",
																		'style' => 'text-align: right;',
																		'required']) !!}
						</div>
					</div>
					<div class="form-group row">
						{!! Form::label('max_value', trans('qms.labels.MAX_VALUE').'*', ['class'=>'col-md-3 control-label']) !!}
						<div class="col-md-3 col-md-offset-2">
							{!! Form::number('max_value',
									isset($oAnalysis) ? $oAnalysis->max_value : '',
																		['class'=>'form-control',
																		'value' => "0",
																		'placeholder' => "1.0",
																		'step'=> "0.01",
																		'style' => 'text-align: right;',
																		'required']) !!}
						</div>
					</div>
					<div class="form-group row">
						{!! Form::label('result_unit', trans('qms.labels.RESULT_UNIT').'*', ['class'=>'col-md-3 control-label']) !!}
						<div class="col-md-5">
							{!! Form::text('result_unit',
									isset($oAnalysis) ? $oAnalysis->result_unit : '',
																		['class'=>'form-control',
																		'placeholder' => 'UFC/g',
																		'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
																		'required']) !!}
						</div>
					</div>
					<div class="form-group row">
						{!! Form::label('type_id', trans('qms.labels.ANALYSIS_TYPE').'*',['class'=>'col-md-3 control-label']) !!}
						<div class="col-md-5">
								{!! Form::select('type_id', $types, isset($oAnalysis) ? $oAnalysis->type_id : null,
															['class'=>'form-control select-type',
															'placeholder' => trans('qms.placeholders.SELECT_ANALYSIS_TYPE'),
															'required']) !!}
						</div>
					</div>
				</div>
			</div>

@endsection

@section('js')

  <script>
		$('.datepicker').datepicker({
				format: "yyyy/mm/dd",
				language: "es",
				autoclose: true
		});

		$('.select-type').chosen({
		});

  </script>
@endsection
