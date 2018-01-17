<div style="font-size:9px; margin-top:0px;">
  <center><b style="font-size:10px;">{{session('company')->name}}</b></center>
  <b>Id:</b> {{$data->id_lot}}
  <b>Lote:</b> {{$data->lot}}
  <b>Item:</b>{{$data->item->name}}
  <b>Unidad:</b> {{$data->unit->name}}
  <br>
  <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" />
  <br>
  <span>{{$barcode}}</span>
</div>
