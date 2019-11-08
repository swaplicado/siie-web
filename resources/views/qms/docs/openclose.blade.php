<div id="openCloseModal" class="modal fade" role="dialog">
    <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Confirmar</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-6">
                    @{{ getLabel(isClosed) + ' papeleta:' }}
                </div>
                <div class="col-md-6">
                    <input required type="password" v-model="sPassClose" class="form-control" placeholder="Firma (contraseña)">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" v-on:click="openCloseDocument()" class="btn btn-warning" data-dismiss="modal">@{{ getLabel(isClosed) + ' papeleta' }}</button>
        </div>
        </div>
    
    </div>
</div>