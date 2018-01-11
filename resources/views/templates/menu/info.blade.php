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
         <p>{{ trans('userinterface.labels.COMPANY') }}</p>
         <div class="well well-sm"><b>{{ session()->has('company') ? session('company')->name : '' }}</b></div>
         <p>{{ trans('userinterface.labels.BRANCH') }}</p>
         <div class="well well-sm"><b>{{ session()->has('branch') ? session('branch')->name : '' }}</b></div>
         <p>{{ trans('userinterface.labels.WAREHOUSE') }}</p>
         <div class="well well-sm"><b>{{ session()->has('whs') ? session('whs')->name : '' }}</b></div>
         <p>{{ trans('userinterface.labels.USER') }}</p>
         <div class="well well-sm"><b>{{ Auth::check() ? Auth::user()->username : '' }}</b></div>
       </div>
       <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal">{{ trans('actions.CLOSE') }}</button>
       </div>
     </div>

   </div>
 </div>
