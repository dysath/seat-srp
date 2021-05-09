<div class="text-right">
  @can('srp.settings')
    
      <form method="post" action="{{ route('srp.adv.remove', ['rule' => $row]) }}">
        {!! csrf_field() !!}
        {!! method_field('DELETE') !!}
        <button type="submit" class="btn btn-danger btn-sm">
          <i class="fas fa-trash"></i> {{ trans('srp::srp.remove') }}
        </button>
      </form>
    
  @endcan
</div>