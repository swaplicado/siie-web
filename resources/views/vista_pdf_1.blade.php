<div class="container text-center" style="border: 1px solid #a1a1a1;padding: 15px;width: 40%;">
  <span><b>Id del Tarima:</b> {{$dataLot->id_pallet}} </span>
  <br>
  <span><b>Nombre Tarima:</b>        {{$dataLot->pallet}} </span>
  <br><br>
  <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" />
  <br>
  <span>{{$barcode}}</span>
</div>
