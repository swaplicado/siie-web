<div style="font-size:18px; padding:-20px; bottom:-20px; margin-right:-20px">
  <center><b>{{session('company')->name}}</b></center>
  <b>Id: </b>{{$data->id_pallet}}<br>{{$data->item->name}}-{{$data->unit->name}}
  <br>
  <br>
  <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" height="60%" width="60%"/></center>
  <br>
  <center><span>{{$barcode}}</span></center>
</div>
