@extends('templates.newedit.mainnewedit')

@section('menu')
	@include('templates.menu.menumodules')
@endsection

@if(isset($oAnaConfig))
	<?php
			if (isset($bIsCopy))
			{
				$sRoute = 'qms.certconfigs.storeorg';
			}
			else
			{
				$sRoute = 'qms.certconfigs.updorg';
			}

			$aux = $oAnaConfig;
	?>
	@section('title', trans('userinterface.titles.EDIT_ITEM_CONTAINER'))
@else
	<?php
		$sRoute='qms.certconfigs.storeorg';
	?>
	@section('title', trans('userinterface.titles.CREATE_ITEM_CONTAINER'))
@endif
	<?php $sRoute2 = 'qms.certconfigs.index' ?>

@section('content')

		<div class="form-group">
			{!! Form::label('item_link_type_id', trans('wms.labels.LEVEL').'*') !!}
			{!! Form::select('item_link_type_id', $links, isset($oAnaConfig) ?  $oAnaConfig->get(0)->item_link_type_id : null,
								['class'=>'form-control select-one', 'onChange' => 'whenChangeLink("item_link_type_id")',
								 'placeholder' => trans('wms.placeholders.SELECT_LEVEL'), 'required']) !!}
		</div>

		<div class="form-group">
			{!! Form::label('item_link_id', trans('wms.labels.REFERENCE').'*') !!}
			<div class="linid">
				{!! Form::select('item_link_id', array(), isset($oAnaConfig) ?  $oAnaConfig->get(0)->item_link_id : null,
									['class'=>'form-control select-one', 'required', 'placeholder' => trans('wms.placeholders.SELECT_REFERENCE')]) !!}
			</div>
		</div>

		@if (isset($oCertConfig))
			@foreach ($oCertConfig as $oElemConfig)
				<div class="form-group">
					{!! Form::label($oElemConfig->id_cert_configuration, $oElemConfig->analysis->name.'*') !!}
					<div >
						{!! Form::text($oElemConfig->id_cert_configuration.'+anaid', $oElemConfig->specification,
											['class'=>'form-control', 'required', 'placeholder' => $oElemConfig->result]) !!}
					</div>
				</div>
			@endforeach
		@else
			@foreach ($lAnalysis as $oAnalysis)
				<div class="form-group">
					{!! Form::label($oAnalysis->id_analysis, $oAnalysis->name.'*') !!}
					<div >
						{!! Form::text($oAnalysis->id_analysis.'+anaid', null,
											['class'=>'form-control', 'required', 'placeholder' => $oAnalysis->name]) !!}
					</div>
				</div>
			@endforeach
		@endif

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
				document.getElementById('item_link_id').value = <?php echo json_encode(isset($oAnaConfig) ? $oAnaConfig[0]->item_link_id : ''); ?>;

				var idAnalysis = <?php echo json_encode(isset($oAnaConfig) ? $oAnaConfig[0]->analysis_id : 0); ?>;

				if (idAnalysis > 0) {
					$('#lanali').val([idAnalysis]).trigger('chosen:updated');
				}
		</script>
@endsection
