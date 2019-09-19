<div>
    <div class="row">
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-2">
                    Producto
                </div>
                <div class="col-md-10">
                    <label class="form-control">@{{ vData.item_code + '-' + vData.item }}</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Presentación
                </div>
                <div class="col-md-8">
                    <label class="form-control">@{{ vData.unit }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Lote
                </div>
                <div class="col-md-8">
                    <label class="form-control">@{{ vData.lot }}</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Caducidad
                </div>
                <div class="col-md-8">
                    <label class="form-control">@{{ vData.dt_expiry }}</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Líder Calidad
                </div>
                <div class="col-md-8">
                    <label class="form-control" v-if="vData.sup_quality_id > 1">@{{ vData.sup_quality }}</label>
                    <div v-else>
                        <select class="form-control" v-model="vDocument.sup_quality_id">
                            <option v-for="usr in lUsers" :value="usr.id">@{{ usr.username }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Fecha
                </div>
                <div class="col-md-8">
                    <label class="form-control">@{{ vDocument.dt_document }}</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-6">
                    
                </div>
                <div class="col-md-6">
                    
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Líder Producc.
                </div>
                <div class="col-md-8">
                    <label class="form-control" v-if="vData.sup_production_id > 1">@{{ vData.sup_production }}</label>
                    <div v-else>
                        <select class="form-control" v-model="vDocument.sup_production_id">
                            <option v-for="usr in lUsers" :value="usr.id">@{{ usr.username }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Folio Padre
                </div>
                <div class="col-md-8">
                    <label class="form-control">@{{ vData.father_folio }}</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Folio hijo
                </div>
                <div class="col-md-8">
                        <label class="form-control">@{{ vData.son_folio }}</label>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-4">
                    Líder Proceso
                </div>
                <div class="col-md-8">
                    <label class="form-control" v-if="vData.sup_process_id > 1">@{{ vData.sup_process }}</label>
                    <div v-else>
                        <select class="form-control" v-model="vDocument.sup_process_id">
                            <option v-for="usr in lUsers" :value="usr.id">@{{ usr.username }}</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-2 col-md-offset-8">
            <button class="btn btn-warning btn-lg" type="submit">
                {{ trans('actions.CANCEL') }}
            </button>
        </div>
        <div class="col-md-1">
            <button class="btn btn-success btn-lg" type="submit" v-on:click="saveDocument()">
                {{ trans('actions.SAVE') }}
            </button>
        </div>
    </div>
</div>