<div style="font-size:35px; margin-top:0px;">
  <center><b>{{session('company')->name}}</b></center>
  <br>
  <center><b>CODIGO ALMACEN:</b>{{$data->warehouse->code}}<br></center>
  <br>
  <center><b>NOMBRE ALMACEN:</b> {{$data->warehouse->name}}<br></center>
  <br>
  <center><b>UBICACION:</b>{{$data->code}}</center>
  <br>
  <br>
  <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" /></center>
  <br>
  <center><span>{{$barcode}}</span></center>
</div>
