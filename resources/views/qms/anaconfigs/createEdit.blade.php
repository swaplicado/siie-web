@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($oAnaConfig))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'qms.anaconfigs.store';
			}
			else
			{
				$sRoute = 'qms.anaconfigs.update';
			}
			$aux = $oAnaConfig;
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
			{!! Form::select('item_link_type_id', $links, isset($oAnaConfig) ?  $oAnaConfig->item_link_type_id : null,
								['class'=>'form-control select-one', 'onChange' => 'whenChangeLink("item_link_type_id")',
								 'placeholder' => trans('wms.placeholders.SELECT_LEVEL'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('item_link_id', trans('wms.labels.REFERENCE').'*') !!}
			<div class="linid">
				{!! Form::select('item_link_id', array(), isset($oAnaConfig) ?  $oAnaConfig->item_link_id : null,
									['class'=>'form-control select-one', 'required', 'placeholder' => trans('wms.placeholders.SELECT_REFERENCE')]) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('min_value', trans('qms.labels.MIN_VALUE').'*') !!}
			<div class="col-md-5 col-md-offset-7">
				{!! Form::number('min_value', isset($oAnaConfig) ?  $oAnaConfig->min_value : 0,
								['class'=>'form-control', 
								'required', 
								'value' => "0",
								'placeholder' => "1.0",
								'style' => 'text-align: right;',
								'step'=> "0.01"
								]) !!}
			</div>
		</div>
		
		<div class="form-group">
			{!! Form::label('max_value', trans('qms.labels.MAX_VALUE').'*') !!}
			<div class="col-md-5 col-md-offset-7">
				{!! Form::number('max_value', isset($oAnaConfig) ?  $oAnaConfig->max_value : 0,
								['class'=>'form-control', 
								'required', 
								'value' => "0",
								'placeholder' => "1.0",
								'style' => 'text-align: right;',
								'step'=> "0.01"
								]) !!}
			</div>
		</div>

		<div class="form-group">
			{!! Form::label('l_ana', trans('qms.ANALYSIS').'*') !!}
			<select id="lanali" name="aranalysis[]" value="{{ isset($oAnaConfig) ?  $oAnaConfig->analysis_id : 0 }}" class="chosen-select form-control" data-placeholder="Seleccione análisis" multiple required>
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
		<script type="text/javascript">
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
		</script>
		<script type="text/javascript" src="{{ asset('js/qms/configs/Anaconfigs.js')}}"></script>
		<script type="text/javascript" src="{{ asset('js/itemcontainers/itemcontainer.js')}}"></script>
		<script type="text/javascript">
				whenChangeLink('item_link_type_id');
				document.getElementById('item_link_id').value = <?php echo json_encode(isset($oAnaConfig) ? $oAnaConfig->item_link_id : ''); ?>;

				var idAnalysis = <?php echo json_encode(isset($oAnaConfig) ? $oAnaConfig->analysis_id : 0); ?>;

				if (idAnalysis > 0) {
					$('#lanali').val([idAnalysis]).trigger('chosen:updated');
				}
		</script>
@endsection
