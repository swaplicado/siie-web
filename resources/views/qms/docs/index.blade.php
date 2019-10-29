@extends('templates.home.modules')

@section('title',  trans('qms.QLTY_DOC'))

@section('content')
    <div id="docsApp">
        @include('qms.docs.header')
        <br>
        @include('qms.docs.body')
    </div>
@endsection

@section('js')
    <script src="{{ asset('js/qms/qdocs/objs/SGui.js') }}"></script>
    <script src="{{ asset('js/qms/qdocs/objs/SQDocument.js') }}"></script>

    <script>
        function GlobalData () {
            this.scqms = <?php echo json_encode(\Config::get('scqms')) ?>;
            this.oQDocument = <?php echo json_encode($oQDocument) ?>;
            this.oMongoDocument = <?php echo json_encode($oMongoDocument) ?>;
            this.lSections = <?php echo json_encode($lSections) ?>;
            this.lConfigurations = <?php echo json_encode($lConfigurations) ?>;
            this.data = <?php echo json_encode($aData) ?>;
            this.lUsers = <?php echo json_encode($lUsers) ?>;
            this.cfgZone = <?php echo json_encode($cfgZone) ?>;
        }

        var oData = new GlobalData();
        var oGui = new SGui();
    </script>

    <script src="{{ asset('js/qms/qdocs/objs/SClasses.js') }}"></script>
    <script src="{{ asset('js/qms/qdocs/SQDocs.js') }}"></script>
@endsection