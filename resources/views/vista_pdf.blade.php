
<div style="font-size:9px; margin-top:0px;">
  {{$data->lot}}-{{$data->item->name}}-{{$data->unit->name}}
  <br>
  <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" height="60%" width="60%"/>
  <br>
  {{$barcode}} - {{session('company')->name}}
</div>
