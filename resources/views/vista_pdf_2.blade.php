<div style="font-size:18px; margin-top:0px;">
  <center><b>{{session('company')->name}}</b></center>
  <b>Codigo: </b>{{$data->warehouse->code}}
  <br>
  <b>Nombre: </b> {{$data->warehouse->name}}
  <br>
  <b>Ubicación: </b>{{$data->code}}
  <br>
  <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" /></center>
  <br>
  <center><span>{{$barcode}}</span></center>
</div>
