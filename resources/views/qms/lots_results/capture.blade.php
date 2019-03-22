<!-- Modal -->
<div id="captResultsModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
    
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Captura de resultados</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-1  col-md-offset-1">
                            <label for="lot">Lote</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="lot" readonly>
                        </div>
                        <div class="col-md-1">
                            <label for="exp_date">Venc.</label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" class="form-control" id="exp_date" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-1 col-md-offset-1">
                            <label for="item">Item</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="item" value="-" readonly>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-1 col-md-offset-1">
                            <label for="family">Familia</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="family" value="-" readonly>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <table id="capture_table_id" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Análisis</th>
                                    <th>Norma</th>
                                    <th>Tipo</th>
                                    <th>Min</th>
                                    <th>Max</th>
                                    <th>Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button onclick="getResults()" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    
    </div>
</div>