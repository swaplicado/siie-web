
<div class="container text-center" style="border: 1px solid #a1a1a1;padding: 15px;width: 40%;">
  <span><b>Id del Lote:</b> {{$dataLot->id_lot}}     <b>Lote:</b> {{$dataLot->name}}</span>
  <br>
  <span><b>Item:</b>        {{$dataLot->item->name}} <b>Unidad:</b> {{$dataLot->unit->name}}</span>
  <br><br>
  <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode, 'C128',1,33)}}" alt="barcode" />
  <br>
  <span>{{$barcode}}</span>
</div>
