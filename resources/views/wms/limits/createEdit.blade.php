@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($limit))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'wms.limits.store';
			}
			else
			{
				$sRoute = 'wms.limits.update';
			}
			$aux = $limit;
	?>
	@section('title', trans('userinterface.titles.EDIT_LIMIT'))
@else
	<?php
		$sRoute='wms.limits.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_LIMIT'))
@endif
	<?php $sRoute2 = 'wms.limits.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('max', trans('wms.labels.MAX').'*') !!}
			{!! Form::number('max',
				isset($limit) ? $limit->max : 0 , ['class'=>'form-control', 'min' => '0', 'step' => '1',
				 																						'placeholder' => '0.0', 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('min', trans('wms.labels.MIN').'*') !!}
			{!! Form::number('min',
				isset($limit) ? $limit->min : 0 , ['class'=>'form-control', 'min' => '0', 'step' => '1',
				 																						'placeholder' => '0.0']) !!}
		</div>

		{{-- <div class="form-group">
			{!! Form::label('aux_company_b', trans('wms.labels.COMPANY_LEVEL')) !!}
			{!! Form::checkbox('aux_company_b', 1, isset($limit) ?  $limit->aux_company_b : 0) !!}
		</div> --}}

		<div class="form-group">
			{!! Form::label('item_id', trans('wms.labels.MAT_PROD')) !!}
			{!! Form::select('item_id', $items, isset($limit) ?  $limit->item_id : null,
								['class'=>'form-control select-one', 'placeholder' => trans('wms.placeholders.SELECT_MAT_PROD')]) !!}
		</div>

		<div class="form-group">
			{!! Form::label('aux_branch_id', trans('userinterface.labels.BRANCH')) !!}
			{!! Form::select('aux_branch_id', $branches, isset($limit) ?  $limit->aux_branch_id : null,
								['class'=>'form-control', 'onChange' => 'whenChangeBranch(\'aux_branch_id\')', 'placeholder' => trans('userinterface.placeholders.SELECT_BRANCH')]) !!}
		</div>

		<div class="form-group">
			{!! Form::label('aux_whs_id', trans('userinterface.labels.WAREHOUSE')) !!}
			<div class="whss">
				{!! Form::select('aux_whs_id', array(), isset($limit) ?  $limit->aux_whs_id : null ,
									['class'=>'form-control', 'onChange' => 'whenChangeWarehouse(\'aux_whs_id\')', 'placeholder' => trans('userinterface.placeholders.SELECT_WHS')]) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('aux_location_id', trans('userinterface.labels.LOCATION')) !!}
			<div class="locs">
				{!! Form::select('aux_location_id', array(), isset($limit) ?  $limit->aux_location_id : null ,
									['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.SELECT_LOCATION')]) !!}
			</div>
		</div>

@endsection

@section('js')
		<script>
			function Data() {
					this.lWarehouses = <?php echo json_encode($warehouses); ?>;
					this.lLocations = <?php echo json_encode($locations); ?>;
			}

			var oData = new Data();

			var whsId = <?php echo json_encode(isset($limit) ? $limit->aux_whs_id == "" ? 0 : $limit->aux_whs_id : 0); ?>;
			var locationId = <?php echo json_encode(isset($limit) ? $limit->aux_location_id == "" ? 0 : $limit->aux_location_id : 0); ?>;
		</script>
		<script src="{{ asset('js/folios/folios.js')}}"></script>
		<script>
				whenChangeBranch('aux_branch_id');
				document.getElementById('aux_whs_id').value = whsId;
				document.getElementById('aux_location_id').value = locationId;
		</script>
@endsection
