<div id="sigModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Firmas</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-2">
                   Etiqueta Argox:
                </div>
                <div class="col-md-3">
                    <input type="password" v-model="signatureArgox" class="form-control" placeholder="Firma (contraseña)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" v-on:click="signDocument(oData.scsiie.SIGNATURE.ARGOX, signatureArgox)">Firmar</button>
                </div>
                <div class="col-md-1">
                    <span :class="iconArgox" aria-hidden="true"></span>
                </div>
                <div class="col-md-1">
                    <label>@{{ oCurDocument.usr_argox }}</label>
                </div>
                <div class="col-md-3">
                    <label>@{{ oCurDocument.creation_argox }}</label>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-2">
                    Etiqueta Codificación:
                </div>
                <div class="col-md-3">
                    <input type="password" v-model="signatureCoding" class="form-control" placeholder="Firma (contraseña)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" v-on:click="signDocument(oData.scsiie.SIGNATURE.CODING, signatureCoding)">Firmar</button>
                </div>
                <div class="col-md-1">
                    <span :class="iconCoding" aria-hidden="true"></span>
                </div>
                <div class="col-md-1">
                    <label>@{{ oCurDocument.usr_coding }}</label>
                </div>
                <div class="col-md-3">
                    <label>@{{ oCurDocument.creation_coding }}</label>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-2">
                    Microbiología:
                </div>
                <div class="col-md-3">
                    <input type="password" v-model="signatureMb" class="form-control" placeholder="Firma (contraseña)">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-success" v-on:click="signDocument(oData.scsiie.SIGNATURE.MB, signatureMb)">Firmar</button>
                </div>
                <div class="col-md-1">
                    <span :class="iconMb" aria-hidden="true"></span>
                </div>
                <div class="col-md-1">
                    <label>@{{ oCurDocument.usr_mb }}</label>
                </div>
                <div class="col-md-3">
                    <label>@{{ oCurDocument.creation_mb }}</label>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
        </div>
        </div>
    
    </div>
</div>