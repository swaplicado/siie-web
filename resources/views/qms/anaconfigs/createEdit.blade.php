@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($itemcontainer))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'qms.anaconfigs.store';
			}
			else
			{
				$sRoute = 'qms.anaconfigs.update';
			}
			$aux = $itemcontainer;
	?>
	@section('title', trans('userinterface.titles.EDIT_ITEM_CONTAINER'))
@else
	<?php
		$sRoute='qms.anaconfigs.store';
	?>
	@section('title', trans('userinterface.titles.CREATE_ITEM_CONTAINER'))
@endif
	<?php $sRoute2 = 'qms.anaconfigs.index' ?>

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
			{!! Form::label('l_ana', trans('qms.ANALYSIS').'*') !!}
			<select name="aranalysis[]" class="chosen-select form-control" data-placeholder="Seleccione análisis" multiple required>
					<option value=""></option>
					@foreach ($lTypes as $type)
						<optgroup label="{{ $type->name }}">
						@foreach ($lAnalysis as $analysis)
							@if ($analysis->type_id == $type->id_analysis_type)
								<option value="{{ $analysis->id_analysis }}">{{ $analysis->ana_name }}</option>
							@endif
						@endforeach
						</optgroup>
					@endforeach
			</select>
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
		<script src="{{ asset('js/qms/configs/Anaconfigs.js')}}"></script>
		<script src="{{ asset('js/itemcontainers/itemcontainer.js')}}"></script>
@endsection
