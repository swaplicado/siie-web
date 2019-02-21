<!-- Modal -->
 <div class="modal fade" id="myModal" role="dialog">
   <div class="modal-dialog">

     <!-- Modal content-->
     <div class="modal-content">
       <div class="modal-header">
         <button type="button" class="close" data-dismiss="modal">&times;</button>
         <h4 class="modal-title">{{ trans('siie.SESSION_INFO') }}</h4>
       </div>
       <div class="modal-body">
         <p>{{ trans('userinterface.labels.WORK_DATE') }}</p>
         <div class="well well-sm col-md-8">
           {!! Form::date('work_date',
             session()->has('work_date') ? session('work_date') : null,
                                                   ['class'=>'form-control',
                                                   'id' => 'work_date',
                                                   'max' => Carbon\Carbon::today()->toDateString()]) !!}
         </div>
         <div class="well well-sm col-md-4">
           {!! Form::button('Aplicar', ['class' => 'btn btn-primary', 'onClick' => 'changeDate()']) !!}
         </div>
         <p>{{ trans('userinterface.labels.COMPANY') }}</p>
         <div class="well well-sm"><b>{{ session()->has('company') ? session('company')->name : '' }}</b></div>
         <p>{{ trans('userinterface.labels.BRANCH') }}</p>
         <div class="well well-sm"><b>{{ session()->has('branch') ? session('branch')->name : '' }}</b></div>
         <p>{{ trans('userinterface.labels.WAREHOUSE') }}</p>
         <div class="well well-sm"><b>{{ session()->has('whs') ? session('whs')->name : '' }}</b></div>
         <p>{{ trans('userinterface.labels.USER') }}</p>
         <div class="well well-sm"><b>{{ Auth::check() ? Auth::user()->username : '' }}</b></div>
         <div class="well well-sm">
           <a href="https://docs.google.com/document/d/1tYdh6WbB724pQdGyTVSIK-j6LUMaLXKuQtvA_E_BuzU/edit?usp=sharing"
            target="_blank">
             Manual Almacenes y Calidad
           </a>
         </div>
         <div class="well well-sm">
           <a href="https://docs.google.com/document/d/1qmXq66fVFhPqCCGWHEbmMIevZmFtdWZzxWGpuDjz6c0/edit?usp=sharing"
            target="_blank">
             Manual Producción
           </a>
         </div>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
       </div>
     </div>
   </div>
 </div>

 <script type="text/javascript" src="{{ asset('js/SSelectDate.js')}}" charset="utf-8"></script>
