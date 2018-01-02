@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.list'))
@section('page_header', trans('srp::srp.list'))

@section('full')
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title">SRP Requests</h3>
        </div>
        <div class="box-body">
          <table id="srps" class="table table-bordered">
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
                  <td>
                      <a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/" target="_blank">{{ $kill->kill_id }}</a>
                      @if(!is_null($kill->ping()))
                      <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-ping" data-kill-id="{{ $kill->kill_id }}">
                          <i class="fa fa-comment"></i>
                      </button>
                      @endif
                  </td>
                  <td>{{ $kill->character_name }}</td>
                  <td>{{ $kill->ship_type }}</td>
                  <td>
                      <button type="button" class="btn btn-xs btn-link" data-toggle="modal" data-target="#insurances" data-kill-id="{{ $kill->kill_id }}">
                          {{ number_format($kill->cost) }} ISK
                      </button>
                  </td>
                  @if ($kill->approved === 0)
                    <td id="id-{{ $kill->kill_id }}"><span class="label label-warning">Pending</span></td>
                  @elseif ($kill->approved === -1)
                    <td id="id-{{ $kill->kill_id }}"><span class="label label-danger">Rejected</span></td>
                  @elseif ($kill->approved === 1)
                    <td id="id-{{ $kill->kill_id }}"><span class="label label-success">Approved</span></td>
                  @elseif ($kill->approved === 2)
                    <td id="id-{{ $kill->kill_id }}"><span class="label label-primary">Paid Out</span></td>
                  @endif
                  <td>
                      <span data-toggle="tooltip" data-placement="top" title="{{ $kill->created_at }}">{{ human_diff($kill->created_at) }}</span>
                  </td>
                  <td>
                      <button type="button" class="btn btn-xs btn-warning srp-status" name="{{ $kill->kill_id }}">Pending</button>
                      <button type="button" class="btn btn-xs btn-danger srp-status" name="{{ $kill->kill_id }}">Reject</button>
                      <button type="button" class="btn btn-xs btn-success srp-status" name="{{ $kill->kill_id }}">Approve</button>
                      <button type="button" class="btn btn-xs btn-primary srp-status" name="{{ $kill->kill_id }}">Paid Out</button>
                  </td>
                </tr>
                @endforeach
            </tbody>
          </table>
        </div>
    </div>
    @include('srp::includes.insurances-modal')
    @include('srp::includes.ping-modal')
@stop

@push('head')
<link rel="stylesheet" type="text/css" href="{{ asset('web/css/denngarr-srp-hook.css') }}" />
@endpush

@push('javascript')
<script type="application/javascript">
  $(function () {
    $('#srps').DataTable();

    $('#srp-ping').on('show.bs.modal', function(e){
        var link = '{{ route('srp.ping', 0) }}';

        $(this).find('.overlay').show();
        $(this).find('.modal-body>p').text('');

        $.ajax({
            url: link.replace('/0', '/' + $(e.relatedTarget).attr('data-kill-id')),
            dataType: 'json',
            method: 'GET'
        }).done(function(response){
            $('#srp-ping').find('.modal-body>p').text(response.note).removeClass('text-danger');
        }).fail(function(jqXHR, status){
            $('#srp-ping').find('.modal-body>p').text(status).addClass('text-danger');

            if (jqXHR.statusCode() !== 500)
                $('#srp-ping').find('.modal-body>p').text(jqXHR.responseJSON.msg);
        });

        $(this).find('.overlay').hide();
    });

    $('#insurances').on('show.bs.modal', function(e){
        var link = '{{ route('srp.insurances', 0) }}';
        var table = $('#insurances').find('table');

        if (!$.fn.DataTable.isDataTable(table)) {
            table.DataTable({
                "ajax": {
                    url: link.replace('/0', '/' + $(e.relatedTarget).attr('data-kill-id')),
                    dataSrc: ''
                },
                "searching": false,
                "ordering": true,
                "info": false,
                "paging": false,
                "processing": true,
                "order": [[0, "asc"]],
                "columnDefs": [
                    {
                        "render": function(data, type, row) {
                            return row.name;
                        },
                        "targets": 0
                    },
                    {
                        "className": "text-right",
                        "render": function(data, type, row) {
                            return parseFloat(row.cost).toLocaleString(undefined, {
                                "minimumFractionDigits": 2,
                                "maximumFractionDigits": 2
                            });
                        },
                        "targets": 1
                    },
                    {
                        "className": "text-right",
                        "render": function(data, type, row) {
                            return parseFloat(row.payout).toLocaleString(undefined, {
                                "minimumFractionDigits": 2,
                                "maximumFractionDigits": 2
                            });
                        },
                        "targets": 2
                    },
                    {
                        "className": "text-right",
                        "render": function(data, type, row) {
                            return parseFloat(row.refunded).toLocaleString(undefined, {
                                "minimumFractionDigits": 2,
                                "maximumFractionDigits": 2
                            });
                        },
                        "targets": 3
                    },
                    {
                        "className": "text-right",
                        "render": function(data, type, row) {
                            return parseFloat(row.remaining).toLocaleString(undefined, {
                                "minimumFractionDigits": 2,
                                "maximumFractionDigits": 2
                            }) + " ISK";
                        },
                        "targets": 4
                    }
                ]
            });
        }
    })
    .on('hidden.bs.modal', function(e){
        var table = $('#insurances').find('table').DataTable();
        table.destroy();
    });

    $('button.srp-status').click(function(btn) {
        $.ajax({
          headers: function() {},
          url: "{{ route('srpadmin.list') }}/" + btn.target.name + "/" + $(btn.target).text(),
          dataType: 'json',
          timeout: 5000
        }).done(function (data) {
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
