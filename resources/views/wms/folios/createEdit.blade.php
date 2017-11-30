@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($folio))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'wms.folios.store';
			}
			else
			{
				$sRoute = 'wms.folios.update';
			}
			$aux = $folio;
	?>
	@section('title', trans('userinterface.titles.EDIT_FOLIO'))
@else
	<?php
		$sRoute='wms.folios.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_FOLIO'))
@endif
	<?php $sRoute2 = 'wms.folios.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('folio_start', trans('wms.labels.FOLIO_START').'*') !!}
			{!! Form::text('folio_start',
				isset($folio) ? $folio->folio_start : null , ['class'=>'form-control', 'onKeyup' => 'javascript:this.value=this.value.toUpperCase();',
				 																						'placeholder' => trans('wms.placeholders.FOLIO_START'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('mvt_class_id', trans('wms.labels.MVT_CLASS').'*') !!}
			{!! Form::select('mvt_class_id', $mvtClasses, isset($folio) ?  $folio->mvt_class_id : null,
								['class'=>'form-control', 'onChange' => 'whenChangeClass(\'mvt_class_id\')', 'placeholder' => trans('wms.placeholders.SELECT_MVT_CLASS')]) !!}
		</div>

		<div class="form-group">
			{!! Form::label('mvt_type_id', trans('wms.labels.MVT_TYPE').'*'	) !!}
			<div class="tps">
				{!! Form::select('mvt_type_id', array(), isset($folio) ?  $folio->mvt_type_id : null,
									['class'=>'form-control', 'placeholder' => trans('wms.placeholders.SELECT_MVT_TYPE')]) !!}
			</div>
		</div>

		{{-- <div class="form-group">
			{!! Form::label('aux_company_b', trans('wms.labels.COMPANY_LEVEL')) !!}
			{!! Form::checkbox('aux_company_b', 1, isset($folio) ?  $folio->aux_company_b : 0) !!}
		</div> --}}

		<div class="form-group">
			{!! Form::label('aux_branch_id', trans('userinterface.labels.BRANCH')) !!}
			{!! Form::select('aux_branch_id', $branches, isset($folio) ?  $folio->aux_branch_id : null,
								['class'=>'form-control', 'onChange' => 'whenChangeBranch(\'aux_branch_id\')', 'placeholder' => trans('userinterface.placeholders.SELECT_BRANCH')]) !!}
		</div>

		<div class="form-group">
			{!! Form::label('aux_whs_id', trans('userinterface.labels.WAREHOUSE')) !!}
			<div class="whss">
				{!! Form::select('aux_whs_id', array(), isset($folio) ?  $folio->aux_whs_id : null ,
									['class'=>'form-control', 'onChange' => 'whenChangeWarehouse(\'aux_whs_id\')', 'placeholder' => trans('userinterface.placeholders.SELECT_WHS')]) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('aux_location_id', trans('userinterface.labels.LOCATION')) !!}
			<div class="locs">
				{!! Form::select('aux_location_id', array(), isset($folio) ?  $folio->aux_location_id : null ,
									['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.SELECT_LOCATION')]) !!}
			</div>
		</div>

@endsection

@section('js')
		<script>
			function Data() {
				  this.lTypes = <?php echo json_encode($mvtTypes); ?>;
					this.lWarehouses = <?php echo json_encode($warehouses); ?>;
					this.lLocations = <?php echo json_encode($locations); ?>;
			}

			var oData = new Data();
		</script>
		<script src="{{ asset('js/folios/folios.js')}}"></script>
		<script>
				whenChangeBranch('aux_branch_id');
		</script>
@endsection
