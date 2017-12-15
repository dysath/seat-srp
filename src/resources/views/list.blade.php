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
                <td data-order="" style="background-color: yellow; color: black;" id="id-{{ $kill->kill_id }}"> Pending </td>
              @elseif ($kill->approved === -1)
                <td data-order="" style="background-color: red; color: white;" id="id-{{ $kill->kill_id }}"> Rejected </td>
              @elseif ($kill->approved === 1)
                <td data-order="" style="background-color: green; color: white;" id="id-{{ $kill->kill_id }}"> Approved </td>
              @elseif ($kill->approved === 2)
                <td data-order="" style="background-color: blue; color: white;" id="id-{{ $kill->kill_id }}"> Paid Out </td>
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
          $("#id-"+data.value).css('background-color', 'green');
          $("#id-"+data.value).css('color', 'white');
          $("#id-"+data.value).text("Approved");
      } else if (data.name === "Reject") {
          $("#id-"+data.value).css('background-color', 'red');
          $("#id-"+data.value).css('color', 'white');
          $("#id-"+data.value).text("Rejected");
      } else if (data.name === "Paid Out") {
          $("#id-"+data.value).css('background-color', 'blue');
          $("#id-"+data.value).css('color', 'white');
          $("#id-"+data.value).text("Paid Out");
      } else if (data.name === "Pending") {
          $("#id-"+data.value).css('background-color', 'yellow');
          $("#id-"+data.value).css('color', 'black');
          $("#id-"+data.value).text("Pending");
      }
    });

    
});
});
</script>
@endpush
