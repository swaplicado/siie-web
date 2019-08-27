<!-- Modal -->
<div id="syncMms" class="modal fade" role="dialog">
  <div class="modal-dialog">
    <div id="app">
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Sincronizar con siie</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">Fórmulas</div>
                <div class="col-md-8">@{{ nFormulas }} elementos importados</div>
            </div>
            <div class="row">
                <div class="col-md-4">Órdenes de producción</div>
                <div class="col-md-8">@{{ nPOs }} elementos importados</div>
            </div>
            <div class="row">
                <div class="col-md-3 col-md-offset-9">
                    <button v-on:click="syncMms" class="btn btn-info"><i class="glyphicon glyphicon-refresh"></i> Sincronizar</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
        </div>
        </div>
    </div>

    {{-- Este modal funciona con Vue y el archivo js/siie/SSync.js  --}}
  </div>
</div>