
<div class="container text-center" style="border: 1px solid #a1a1a1;padding: 15px;width: 40%;">
  <center><b>{{session('company')->name}}</b></center>
  <b>Id Lote:</b> {{$data->id_lot}}
  <b>Lote:</b> {{$data->lot}}
  <br>
  <b>Producto:</b>{{$data->item->name}}
  <b>Unidad:</b> {{$data->unit->name}}
  <br>
  <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" />
  <br>
  <span>{{$barcode}}</span>
</div>
