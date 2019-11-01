@extends('templates.home.modules')

@section('title', 'Autorizaciones')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Usuario</th>
                        <th>Autorizaciones</th>
                        <th>-</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lUsers as $user)
                        <form action="{{ route('siie.signauths.store') }}" method="POST">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="user" value="{{ $user->id }}">
                            <tr>
                                <td>{{ $user['username'] }}</td>
                                <td>
                                    <select name="authorizations[]" data-placeholder="Autorización para..." class="chosen-selectt" multiple>
                                        <option value=""></option>
                                        @foreach ($lSignatures as $signature)
                                            <?php $selected = false ?>
                                            @foreach ($user->auths as $auth)
                                                @if ($auth == $signature->id_signature_type)
                                                    <?php $selected = true ?>
                                                @endif
                                            @endforeach

                                            <option 
                                                @if ($selected)
                                                    {{ 'selected' }}
                                                @endif 
                                                value="{{ $signature->id_signature_type }}">{{ $signature->stype }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <button class="btn btn-info" type="submit">Guardar</button>
                                </td>
                            </tr>
                        </form>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
    
@section('js')
    <script src="{{ asset('js/qms/qdocs/objs/SGui.js') }}"></script>

    <script>
        function GlobalData () {
            this.scqms = <?php echo json_encode(\Config::get('scqms')) ?>;
            this.scsiie = <?php echo json_encode(\Config::get('scsiie')) ?>;
            this.lUsers = <?php echo json_encode(\Config::get('lUsers')) ?>;
        }

        var oData = new GlobalData();
        var oGui = new SGui();

        $(".chosen-selectt").chosen({width: "95%"});
    </script>

    <script src="{{ asset('js/siie/authorizations/SAuthorizations.js') }}"></script>
@endsection