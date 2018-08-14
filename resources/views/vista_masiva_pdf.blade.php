@for($i=0;$i<sizeof($data);$i++)
  @for($j=0;$j<session('number_label');$j++)
    <div style=" font-size:18px; padding:100px -300px 0px 140px; bottom:-20px; margin-right:-20px;  bottom:20px;transform:rotate(90deg); page-break-after:always;">
      <center><b>{{session('company')->name}}</b></center>
      <b>Id: </b>{{$data[$i]->id_pallet}}<br>{{$data[$i]->item->name}}-{{$data[$i]->unit->name}}
      <br>
      <br>
      <center><img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode[$i], 'C128',1,33)}}" alt="barcode" width="50%" height="50%" /></center>
      <br>
      <center><span>{{$barcode[$i]}}</span></center>
    </div>
  @endfor
@endfor
