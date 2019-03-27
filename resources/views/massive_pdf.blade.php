@extends('wms.pallets.print')

@for($i=0;$i<sizeof($data);$i++)
  @for($j=1;$j<=session('number_label');$j++)
    @if($i == (sizeof($data)-1) && $j == session('number_label') )
      {{-- <div style=" font-size:18px; padding:100px -300px 0px 140px; bottom:-20px; margin-right:-20px;  bottom:20px;transform:rotate(90deg);">
        <center><b>{{session('company')->name}}</b></center>
        <b>Id: </b>{{$data[$i]->id_pallet}}<br>{{$data[$i]->item->name}}-{{$data[$i]->unit->name}}
        <br>
        <br>
        <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode[$i], 'C128',1,33)}}" alt="barcode" width="50%" height="50%" /></center>
        <br>
        <center><span>{{$barcode[$i]}}</span></center>
      </div> --}}
      <?php
        $pallet = $data[$i]->id_pallet;
        $item_name = $data[$i]->item->name;
        $unit_name = $data[$i]->unit->name;
        $barcode_ = $barcode[$i];
      ?>
      
        @include('wms.pallets.p_label')
      
    @else
    {{-- <div style=" font-size:18px; padding:100px -300px 0px 140px; bottom:-20px; margin-right:-20px;  bottom:20px;transform:rotate(90deg); page-break-after:always;"> --}}
      {{-- <center><b>{{session('company')->name}}</b></center>
      <b>Id: </b>{{$data[$i]->id_pallet}}<br>{{$data[$i]->item->name}}-{{$data[$i]->unit->name}}
      <br>
      <br>
      <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode[$i], 'C128',1,33)}}" alt="barcode" width="50%" height="50%" /></center>
      <br>
      <center><span>{{$barcode[$i]}}</span></center>
    </div> --}}
    <?php
        $pallet = $data[$i]->id_pallet;
        $item_name = $data[$i]->item->name;
        $unit_name = $data[$i]->unit->name;
        $barcode_ = $barcode[$i];
      ?>

      
        @include('wms.pallets.p_label')
      
    @endif
  @endfor
@endfor
