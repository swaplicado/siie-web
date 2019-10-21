<!-- Modal -->
<div id="phReport" class="modal fade" role="dialog">
    <div class="modal-dialog">
        
        <!-- Modal content-->
        <div class="modal-content">
            <form action="{{ route('qms.reports.ph') }}" method="GET">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{{ trans('qms.REPORT_PH') }}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">
                                Material/producto:
                            </label>
                        </div>
                        <div class="col-md-9">
                            {!! Form::select('item_id', $lItems, null,
                                                            ['class'=>'form-control chosen-select',
                                                            'placeholder' => trans('userinterface.placeholders.SELECT_ITEM'), 'required']) !!}
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <label>
                                Fecha inicio:
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="start_date" class="form-control" required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <label>
                                Fecha fin:
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input type="date" name="end_date" class="form-control" required>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <label for="">
                                PH Máximo:
                            </label>
                        </div>
                        <div class="col-md-4">
                            <input style="text-align:right" type="number" step="0.01" value="0.00" name="max_ph" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">{{ trans('actions.GENERATE') }}</button>
                </div>

            </form>
        </div>
    </div>
</div>