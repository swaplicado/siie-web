
<div style=" font-size:17px; padding:100px -300px 0px 140px; bottom:-20px; margin-right:-20px;  bottom:20px;transform:rotate(90deg);">
  <center><b>{{session('company')->name}}</b></center>
  <b>Id: </b>{{$data->id_pallet}}<br>{{$data->item->name}}-{{$data->unit->name}}
  <br>
  <br>
  <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" width="50%" height="50%" /></center>
  <br>
  <center><span>{{$barcode}}</span></center>
</div>
