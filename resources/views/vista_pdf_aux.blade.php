<div style="font-size:11; margin:30px 0px 0px 0px;">
  <strong><center>{{session('company')->name}}</center></strong>
  <br>
  <br>
  <strong>Tarima:</strong> {{$data->id_pallet}}
  <br>
  <br>
  <strong>Producto:</strong> {{$data->item->name}}
  <br>
  <br>
  <strong>Unidad de Medida:</strong> {{$data->unit->name}}
  <br>
  <br><br><br>
  <center>
    <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" height="80%" width="80%"/>
  </center>
  <br>
  <center>
    {{$barcode}}
    <br>
  </center>
</div>
