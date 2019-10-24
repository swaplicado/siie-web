<!-- Modal -->
<div id="previousPrintModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-sm">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Certificado de calidad</h4>
            </div>
            <form action="{{ route('qms.results.print') }}" target="_blank">
                {!! Form::hidden('id_lot', null, ['id' => 'id_lot']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">Fecha Emisión</div>
                        <div class="col-md-7">
                            <input type="date" 
                                value="null" 
                                name="cert_date" 
                                id="cert_date" 
                                class="form-control input-sm">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="submit" value="{{ trans('actions.PRINT') }}" class="btn btn-success">
                </div>
            </form>
        </div>
    
    </div>
</div>