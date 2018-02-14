<!-- Modal -->
<div id="modalNote" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Notas</h4>
      </div>
      <div id='app' class="modal-body">
        <div class="row">
          <div class="col-md-12">
            {!! Form::label(trans('messages.MAX_LENGTH').'150') !!}
            {!! Form::textarea('notes', null, ['size' => '60x8',
                                                'class' => 'form-control',
                                                'id' => 'note_area',
                                                'onKeyup' => 'changeNote()',
                                                'maxlength' => '150',
                                                'placeholder' => trans('userinterface.placeholders.NOTE'),
                                                'readonly' => 'true']) !!}
          </div>
        </div>
        <div class="row">
          <div class="col-md-7 col-md-offset-5">
              <button type="button" id='btnNewNote' onclick="newNote()" class="btn btn-success">{{ trans('actions.CREATE') }}</button>
              <button type="button" id='btnEditNote' onclick="editNote()" disabled class="btn btn-info">{{ trans('actions.EDIT') }}</button>
              <button type="button" id='btnSaveNote' onclick="saveNote()" disabled class="btn btn-primary">{{ trans('actions.SAVE') }}</button>
              <button type="button" id='btnDelNote' onclick="deleteNote()" disabled class="btn btn-danger">{{ trans('actions.QUIT') }}</button>
          </div>
        </div>
        <br />
        <div class="row">
          <div class="col-md-12">
            <table id="notes_table" class="table table-striped table-bordered display responsive no-wrap" cellspacing="0" width="100%">
        				<thead>
        						<tr class="titlerow">
        								<th>numNote</th>
        								<th>idNote</th>
        								<th>Note</th>
        						</tr>
        				</thead>
        				<tbody>
                  <?php
                    $i = 0;
                  ?>
        					@foreach ($oFormula->notes as $note)
                    @if (! $note->is_deleted)
          						<tr>
          								<td>{{ $i++ }}</td>
          								<td>{{ $note->id_note }}</td>
          								<td>{{ $note->note }}</td>
          						</tr>
                    @endif
        					@endforeach
        				</tbody>
        		</table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button  id="closeNote" onclick="setFormulaToForm()" type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
      </div>
    </div>

  </div>
</div>
