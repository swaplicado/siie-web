@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($itemcontainer))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'wms.itemcontainers.store';
			}
			else
			{
				$sRoute = 'wms.itemcontainers.update';
			}
			$aux = $itemcontainer;
	?>
	@section('title', trans('userinterface.titles.EDIT_ITEM_CONTAINER'))
@else
	<?php
		$sRoute='wms.itemcontainers.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_ITEM_CONTAINER'))
@endif
	<?php $sRoute2 = 'wms.itemcontainers.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('item_link_type_id', trans('wms.labels.LEVEL').'*') !!}
			{!! Form::select('item_link_type_id', $links, isset($itemcontainer) ?  $itemcontainer->item_link_type_id : null,
								['class'=>'form-control select-one', 'onChange' => 'whenChangeLink(\'item_link_type_id\')',
								 'placeholder' => trans('wms.placeholders.SELECT_LEVEL'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('item_link_id', trans('wms.labels.REFERENCE').'*') !!}
			<div class="linid">
				{!! Form::select('item_link_id', array(), isset($itemcontainer) ?  $itemcontainer->item_link_id : null,
									['class'=>'form-control select-one', 'required', 'placeholder' => trans('wms.placeholders.SELECT_REFERENCE')]) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('aux_branch_id', trans('userinterface.labels.BRANCH').'*') !!}
			{!! Form::select('aux_branch_id', $branches, isset($itemcontainer) ?  $itemcontainer->aux_branch_id : null,
								['class'=>'form-control', 'onChange' => 'whenChangeBranch(\'aux_branch_id\')',
								'placeholder' => trans('userinterface.placeholders.SELECT_BRANCH'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('aux_whs_id', trans('userinterface.labels.WAREHOUSE')) !!}
			<div class="whss">
				{!! Form::select('aux_whs_id', array(), isset($itemcontainer) ?  $itemcontainer->aux_whs_id : null ,
									['class'=>'form-control', 'onChange' => 'whenChangeWarehouse(\'aux_whs_id\')', 'placeholder' => trans('userinterface.placeholders.SELECT_WHS')]) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('aux_location_id', trans('userinterface.labels.LOCATION')) !!}
			<div class="locs">
				{!! Form::select('aux_location_id', array(), isset($itemcontainer) ?  $itemcontainer->aux_location_id : null ,
									['class'=>'form-control', 'placeholder' => trans('userinterface.placeholders.SELECT_LOCATION')]) !!}
			</div>
		</div>

@endsection

@section('js')
		<script>
			function Data() {
				  this.ALL = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.ALL')); ?>;
				  this.CLASS = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.CLASS')); ?>;
				  this.TYPE = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.TYPE')); ?>;
				  this.FAMILY = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.FAMILY')); ?>;
				  this.GROUP = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.GROUP')); ?>;
				  this.GENDER = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.GENDER')); ?>;
				  this.ITEM = <?php echo json_encode(\Config::get('scsiie.ITEM_LINK.ITEM')); ?>;

					this.lWarehouses = <?php echo json_encode($warehouses); ?>;
					this.lLocations = <?php echo json_encode($locations); ?>;

					this.lItemClasses = <?php echo json_encode($itemClasses); ?>;
					this.lItemTypes = <?php echo json_encode($itemTypes); ?>;
					this.lItemFamilies = <?php echo json_encode($families); ?>;
					this.lItemGroups = <?php echo json_encode($groups); ?>;
					this.lItemGenders = <?php echo json_encode($genders); ?>;
					this.lItems = <?php echo json_encode($items); ?>;
			}

			var oData = new Data();

			var whsId = <?php echo json_encode(isset($itemcontainer) ? $itemcontainer->aux_whs_id == "" ? 0 : $itemcontainer->aux_whs_id : 0); ?>;
			var locationId = <?php echo json_encode(isset($itemcontainer) ? $itemcontainer->aux_location_id == "" ? 0 : $itemcontainer->aux_location_id : 0); ?>;
		</script>
		<script src="{{ asset('js/folios/folios.js')}}"></script>
		<script src="{{ asset('js/itemcontainers/itemcontainer.js')}}"></script>
		<script>
				whenChangeBranch('aux_branch_id');
				document.getElementById('aux_whs_id').value = whsId;
				document.getElementById('aux_location_id').value = locationId;
		</script>
@endsection
