<div style="font-size:18px; margin-top:0px;">
  <center><b>{{session('company')->name}}</b></center>
  <b>Id Tarima: </b>{{$data->id_pallet}}<br><b>Producto: </b> {{$data->item->name}}<br>
  <b>Unidad: </b>{{$data->unit->name}}
  <br>
  <br>
  <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" /></center>
  <br>
  <center><span>{{$barcode}}</span></center>
</div>
