<span class="input-group-btn">
  {!! Form::select('filter', [
                   \Config::get('scsys.FILTER.ACTIVES') => trans('userinterface.labels.ACTIVES'),
                   \Config::get('scsys.FILTER.DELETED') => trans('userinterface.labels.INACTIVES'),
                   \Config::get('scsys.FILTER.ALL') => trans('userinterface.labels.ALL')
                    ],
                    $iFilter, ['class' => 'form-control', 'required']) !!}
</span>
