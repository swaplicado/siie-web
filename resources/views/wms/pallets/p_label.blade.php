<div class="rotao">
    <table width="100%">
        <tr>
            <td width="20%">
                <img src="{{ asset('images/companies/'.(session()->has('company') ? session('company')->database_name : 'siie').'.jpg') }}"
                        alt="saporis"
                        width="50" height="50">
            </td>
            <td width="80%" class="tcode taho">
            <b>{{session('company')->name}}</b>
            </td>
        </tr>
    </table>
    <table width="100%">
        <tr>
            <td width="100%" class="taho tc titem">
            {{$item_name}}-{{$unit_name}}
            </td>
        </tr>
    </table>
    <br>
    <table width="100%">
        <tr>
            <td>
                <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($barcode_, 'C128',1,33)}}"  width="210px" height="150px" alt="barcode" />
            </td>
            <td width="65%" class="taho bigger tc">
                <p class="titem"><b># Tarima (Id)</b></p>
                {{ session('utils')->formatPallet($pallet) }}
            </td>
        </tr>
        <tr>
            <td class="tcode tc">
                <span>{{$barcode_}}</span>
            </td>
        </tr>
    </table>
</div>