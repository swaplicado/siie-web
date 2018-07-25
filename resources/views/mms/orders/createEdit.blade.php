@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($orders))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'mms.orders.store';
			}
			else
			{
				$sRoute = 'mms.orders.update';
			}
			$aux = $orders;
	?>
	@section('title', trans('userinterface.titles.EDIT_ORDERS'))
@else
	<?php
		$sRoute='mms.orders.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_ORDERS'))
@endif
	<?php $sRoute2 = 'mms.orders.index' ?>

@section('content')

			<div class="form-group">
				<div class="col-md-12">
					<div class="form-group row">
						{{-- {!! Form::label('branch', trans('userinterface.labels.BRANCH').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
              {!! Form::select('branch_id', $branches, isset($orders) ?  $orders->branch_id : null ,
        												['class'=>'form-control select-one', 'placeholder' => trans('userinterface.placeholders.SELECT_BRANCH')]) !!}
						</div> --}}
						{!! Form::label('item_id', trans('userinterface.labels.ITEM').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::select('item_id', $items, isset($orders) ? $orders->item_id : null,
																['class'=>'form-control select-item item_id',
																'placeholder' => trans('userinterface.placeholders.SELECT_ITEM'),
																'required']) !!}
						</div>
						{!! Form::label('plan_id', trans('userinterface.labels.PLAN').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::select('plan_id', $plans, isset($orders) ?  $orders->plan_id : null ,
																['class'=>'form-control select-plan',
																'required']) !!}
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="col-md-12">
					<div class="form-group row">
						{!! Form::label('formula', trans('userinterface.labels.FORMULA').'*',['class'=>'col-md-1 control-label']) !!}
            <div class="col-md-4 vaciar">
              {!! Form::select('formula_id', isset($orders) ? $formulas : [], isset($orders) ? $orders->formula_id : null,
                                ['class'=>'form-control select-formula',
																'required']) !!}
            </div>
						{!! Form::label('date', trans('userinterface.labels.DATE_ORDER').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::date('date',
									isset($orders) ? $orders->date : session('work_date'),
																		['class'=>'form-control',
																		'placeholder' => trans('userinterface.placeholders.DATE_ORDER'),
																		'style' => 'text-align: right;',
																		'required']) !!}
						</div>
					</div>
				</div>
			</div>

      <div class="form-group">
        <div class="col-md-12">
          <div class="form-group row">
						{!! Form::label('type_id', trans('userinterface.labels.TYPE').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::select('type_id', $types, 	isset($orders) ? $orders->type->id_type : null,
																['class'=>'form-control select-type',
																'placeholder' => trans('userinterface.placeholders.SELECT_TYPE'),
																'required']) !!}
						</div>
						{!! Form::label('father_order', trans('userinterface.labels.FATHER_ORDER').'*',['class'=>'col-md-1 control-label']) !!}
						<div class="col-md-4">
							{!! Form::select('father_order', $father, isset($orders) ?  $orders->father_order : null ,
																['class'=>'form-control select-father','placeholder' => 'N/A']) !!}
						</div>
          </div>
        </div>
      </div>
      <div class="form-group">
        <div class="col-md-12">
          <div class="form-group row">
						{!! Form::label('charges', trans('userinterface.labels.CHARGE').'*',['class'=>'col-md-1 control-label']) !!}
            <div class="col-md-4">
							{!! Form::number('charges',
					    	isset($orders) ? $orders->charges : 0, ['required','class'=>'form-control',
									'min' => '0', 'placeholder' => trans('userinterface.placeholders.CHARGES'),
									'style' => 'text-align: right;',
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

	$('.select-father').chosen({
	});
	$('.select-formula').chosen({
	});
	$('.select-item').chosen({
	});
	$('.select-type').chosen({
	});
	$('.select-plan').chosen({
	});
	$(document).on('change','.item_id', function(){
		var eti_id=$(this).val();
		var div=$(this).parent();
		var opt=" ";

		$.ajax({
			type:'get',
			url:'{!!URL::to('mms/orders/findFormulas')!!}',
			data:{'id':eti_id},
			success:function(data){
				console.log('success');
				console.log(data);
				console.log(eti_id);


				opt+='<select class="form-control select-formula formula"  name="formula_id" id="formula_id">';

					for(var i=0;i<data.length;i++){
						opt+='<option value="'+data[i].id_formula+'">'+data[i].identifier+'</option>';
					}
					console.log(opt);
					$('.vaciar').empty(" ");
					$('.vaciar').append(opt);
		},
		error:function(){

		}
		});
	});

  </script>
	<script>

	</script>
	@endsection
