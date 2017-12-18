@extends('templates.home.modules')

@section('title', trans('siie.MODULE'))

@section('content')

  <div class="row">
    @include('templates.home.rapidaccess')
    <?php echo createBlock(asset('images/siie/sync.gif'), route('siie.importation', 0), trans('siie.SYNC'), "success3", trans('siie.SYNC_T'));?>
  </div>

@endsection
