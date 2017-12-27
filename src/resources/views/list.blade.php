@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.list'))
@section('page_header', trans('srp::srp.list'))

@section('full')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">My SRP Requests</h3>
        </div>
        <div class="panel-body">
          <table class="table datatable table-condensed table-hover table-responsive">
            <thead>
            <tr>
              <th>{{ trans('srp::srp.id') }}</th>
              <th>{{ trans('srp::srp.characterName') }}</th>
              <th>{{ trans('srp::srp.shipType') }}</th>
              <th>{{ trans('srp::srp.costs') }}</th>
              <th>{{ trans('srp::srp.paidout') }}</th>
              <th>{{ trans('srp::srp.submitted') }}</th>
              <th>{{ trans('srp::srp.action') }}</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($killmails as $kill)
            <tr>
              <td data-order=""><a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/" target="_blank">{{ $kill->kill_id }}</a></td>
              <td data-order="">{{ $kill->character_name }}</td>
              <td data-order="">{{ $kill->ship_type }}</td>
              <td data-order="">{{ number_format($kill->cost) }} ISK</td>
              @if ($kill->approved === 0)
                <td data-order="" id="id-{{ $kill->kill_id }}"><span class="label label-warning">Pending</span></td>
              @elseif ($kill->approved === -1)
                <td data-order="" id="id-{{ $kill->kill_id }}"><span class="label label-danger">Rejected</span></td>
              @elseif ($kill->approved === 1)
                <td data-order="" id="id-{{ $kill->kill_id }}"><span class="label label-success">Approved</span></td>
              @elseif ($kill->approved === 2)
                <td data-order="" id="id-{{ $kill->kill_id }}"><span class="label label-primary">Paid Out</span></td>
              @endif
              <td data-order="">{{ $kill->created_at }}</td>
              <td data-order="">
                  <input type="button" name="{{ $kill->kill_id }}" value="Approve" />
                  <input type="button" name="{{ $kill->kill_id }}" value="Reject" />
                  <input type="button" name="{{ $kill->kill_id }}" value="Paid Out" />
                  <input type="button" name="{{ $kill->kill_id }}" value="Pending" /></td>
            </tr>
            @endforeach
            </tbody>
          </table>
        </div>
    </div>
@stop

@push('javascript')
<script>
$(document).ready( function () {
$(':button').click(function(data) {
    $.ajax({
      headers: function() {},
      url: "{{ route('srpadmin.list') }}/" + data.target.name + "/" + data.target.value,
      dataType: 'json',
      timeout: 5000,
    }).done(function (data) {
console.log(data);
      if (data.name === "Approve") {
          $("#id-"+data.value).html('<span class="label label-success">Approved</span>');
      } else if (data.name === "Reject") {
          $("#id-"+data.value).html('<span class="label label-danger">Rejected</span>');
      } else if (data.name === "Paid Out") {
          $("#id-"+data.value).html('<span class="label label-primary">Paid Out</span>');
      } else if (data.name === "Pending") {
          $("#id-"+data.value).html('<span class="label label-warning">Pending</span>');
      }
    });

    
});
});
</script>
@endpush
