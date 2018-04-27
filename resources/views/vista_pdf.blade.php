
<div style="font-size:66%; margin:-2px; padding:-20px; bottom:-20px; margin-right:-20px">
  {{$data->lot}}-{{$data->item->name}}-{{$data->unit->name}}
  <br>
  <center>
    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" height="60%" width="60%"/>
  </center>
  <center>
    {{$barcode}} - {{session('company')->name}}
  </center>
</div>
