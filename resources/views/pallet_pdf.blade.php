@extends('wms.pallets.print')

<?php
  $pallet = $data->id_pallet;
  $item_name = $data->item->name;
  $unit_name = $data->unit->name;
  $barcode_ = $barcode;
?>
@section('the_label')
    @include('wms.pallets.p_label')
@endsection
