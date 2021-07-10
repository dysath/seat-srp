@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.list'))
@section('page_header', trans('srp::srp.list'))

@push('head')
<link rel = "stylesheet"
   type = "text/css"
   href = "https://snoopy.crypta.tech/snoopy/seat-srp-approval.css" />

<link rel="stylesheet" type="text/css" href="{{ asset('web/css/denngarr-srp-hook.css') }}" />
@endpush

@section('full')
    <div class="card card-primary card-solid">
        <div class="card-header">
            <h3 class="card-title">SRP Requests</h3>
        </div>
        <div class="card-body">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active nav-item"><a class="nav-link active" href="#tab_1" data-toggle="tab">Pending Requests</a></li>
              <li class="nav-item"><a class="nav-link" href="#tab_2" data-toggle="tab">Completed Requests</a></li>
            </ul>

          <div class="tab-content">
          <div class="tab-pane active" id="tab_1">
          <table id="srps" class="table table-bordered">
            <thead>
                <tr>
                  <th>{{ trans('srp::srp.id') }}</th>
                  <th>{{ trans('srp::srp.characterName') }}</th>
                  <th>{{ trans('srp::srp.submittedby') }}</th>
                  <th>{{ trans('srp::srp.shipType') }}</th>
                  <th>{{ trans('srp::srp.costs') }}</th>
                  <th>{{ trans('srp::srp.paidout') }}</th>
                  <th>{{ trans('srp::srp.submitted') }}</th>
                  <th>{{ trans('srp::srp.action') }}</th>
                  <th>{{ trans('srp::srp.notes') }}</th>
                  <th>{{ trans('srp::srp.changedby') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($killmails as $kill)
                @if(($kill->approved === 0) || ($kill->approved === 1) || ($kill->approved === 99))
                <tr>
                  <td>
                      <a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/" target="_blank">{{ $kill->kill_id }}</a>
                      @if(!is_null($kill->ping()))
                      <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-ping" data-kill-id="{{ $kill->kill_id }}">
                          <i class="fa fa-comment"></i>
                      </button>
                      @endif
                  </td>
                  <td><span class='id-to-name' data-id="{{ $kill->character_name }}">{{ $kill->character_name }}</span></td>
                  <td>{{$kill->user->name}}</td>
                  <td>{{ $kill->ship_type }}</td>
                  <td>
                      <button type="button" class="btn btn-xs btn-link" data-toggle="modal" data-target="#insurances" data-kill-id="{{ $kill->kill_id }}">
                          {{ number_format($kill->cost, 2) }} ISK
                      </button>
                  </td>
                  @if ($kill->approved === 0)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-warning">Pending</span></td>
                  @elseif ($kill->approved === -1)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-danger">Rejected</span></td>
                  @elseif ($kill->approved === 1)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-success">Approved</span></td>
                  @elseif ($kill->approved === 2)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-primary">Paid Out</span></td>
                  @elseif ($kill->approved === 99)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-info">Pending Deletion</span></td>
                  @endif
                  <td data-order="{{ strtotime($kill->created_at) }}>
                      <span data-toggle="tooltip" data-placement="top" title="{{ $kill->created_at }}">{{ human_diff($kill->created_at) }}</span>
                  </td>
                  <td>
                      <button type="button" class="btn btn-xs btn-warning srp-status" id="srp-status" name="{{ $kill->kill_id }}">Pending</button>
                      <button type="button" class="btn btn-xs btn-danger srp-status" id="srp-status" name="{{ $kill->kill_id }}">Reject</button>
                      <button type="button" class="btn btn-xs btn-success srp-status" id="srp-status" name="{{ $kill->kill_id }}">Approve</button>
                      <button type="button" class="btn btn-xs btn-primary srp-status" id="srp-status" name="{{ $kill->kill_id }}">Paid Out</button>
                      @can('srp.delete')
                      <button type="button" class="btn btn-xs btn-danger srp-status" id="srp-status" name="{{ $kill->kill_id }}">Delete</button>
                      @endcan
                  </td>                 
                  <td>
                  @if(!is_null($kill->reason()))
                      <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-reason" data-kill-id="{{ $kill->kill_id }}">
                          <i class="fa fa-comment"></i>
                      </button>
                  @endif
                      <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-reason-edit" data-kill-id="{{ $kill->kill_id }}">
                          <i class="fas fa-pencil-alt snoopy"></i>
                      </button>
                  </td>
                  <td id="approver-{{ $kill->kill_id }}">{{ $kill->approver }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
          </table>
        </div>
          <div class="tab-pane" id="tab_2">
          <table id="srps-arch" class="table table-bordered">
            <thead>
                <tr>
                  <th>{{ trans('srp::srp.id') }}</th>
                  <th>{{ trans('srp::srp.characterName') }}</th>
                  <th>{{ trans('srp::srp.submittedby') }}</th>
                  <th>{{ trans('srp::srp.shipType') }}</th>
                  <th>{{ trans('srp::srp.costs') }}</th>
                  <th>{{ trans('srp::srp.paidout') }}</th>
                  <th>{{ trans('srp::srp.submitted') }}</th>
                  <th>{{ trans('srp::srp.notes') }}</th>
                  <th>{{ trans('srp::srp.changedby') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($killmails as $kill)
                @if(($kill->approved === -1) || ($kill->approved === 2))
                <tr>
                  <td>
                      <a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/" target="_blank">{{ $kill->kill_id }}</a>
                      @if(!is_null($kill->ping()))
                      <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-ping" data-kill-id="{{ $kill->kill_id }}">
                          <i class="fa fa-comment"></i>
                      </button>
                      @endif
                  </td>
                  <td><span class='id-to-name' data-id="{{ $kill->character_name }}">{{ $kill->character_name }}</span></td>
                  <td>{{$kill->user->name}}</td>
                  <td>{{ $kill->ship_type }}</td>
                  <td>
                      <button type="button" class="btn btn-xs btn-link" data-toggle="modal" data-target="#insurances" data-kill-id="{{ $kill->kill_id }}">
                          {{ number_format($kill->cost, 2) }} ISK
                      </button>
                  </td>
                  @if ($kill->approved === 0)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-warning">Pending</span></td>
                  @elseif ($kill->approved === -1)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-danger">Rejected</span></td>
                  @elseif ($kill->approved === 1)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-success">Approved</span></td>
                  @elseif ($kill->approved === 2)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-primary">Paid Out</span></td>
                  @elseif ($kill->approved === 99)
                    <td id="id-{{ $kill->kill_id }}"><span class="badge badge-primary">Pending Deletion</span></td>
                  @endif
                  <td data-order="{{ strtotime($kill->created_at) }}>
                      <span data-toggle="tooltip" data-placement="top" title="{{ $kill->created_at }}">{{ human_diff($kill->created_at) }}</span>
                  </td>
                  <td>
                  @if(!is_null($kill->reason()))
                      <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-reason" data-kill-id="{{ $kill->kill_id }}">
                          <i class="fa fa-comment"></i>
                      </button>
                  @else
                      -
                  @endif
                  </td>
                  <td id="approver-{{ $kill->kill_id }}">{{ $kill->approver }}</td>
                </tr>
                @endif
                @endforeach
            </tbody>
          </table>
        </div>
        </div>
          </div>
    </div>
    @include('srp::includes.insurances-modal')
    @include('srp::includes.ping-modal')
    @include('srp::includes.reason-edit-modal')
    @include('srp::includes.reason-modal')
    <div class="card-footer text-muted">
        Plugin maintained by <a href="{{ route('srp.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
    </div>
</div>
@stop

@push('javascript')
@include('web::includes.javascript.id-to-name')

<script>
    $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    });
</script>

<script type="application/javascript">

  $(function () {
    $('#srps').DataTable();
    $('#srps-arch').DataTable();

    $('#srp-ping').on('show.bs.modal', function(e){
        var link = "{{ route('srp.ping', 0) }}";
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

    $('#srp-reason').on('show.bs.modal', function(e){
                var link = "{{ route('srp.reason', 0) }}";

                $(this).find('.overlay').show();
                $(this).find('.modal-body>p').text('');

                $.ajax({
                    url: link.replace('/0', '/' + $(e.relatedTarget).attr('data-kill-id')),
                    dataType: 'json',
                    method: 'GET'
                }).done(function(response){
                    $('#srp-reason').find('.modal-body>p').text(response.note).removeClass('text-danger');
                }).fail(function(jqXHR, status){
                    $('#srp-reason').find('.modal-body>p').text(status).addClass('text-danger');

                    if (jqXHR.statusCode() !== 500)
                        $('#srp-reason').find('.modal-body>p').text(jqXHR.responseJSON.msg);
                });

                $(this).find('.overlay').hide();
            });


    $('#srp-reason-edit').on('show.bs.modal', function(e){
        var link = '{{ route('srp.reason', 0) }}';

        $(this).find('#reasonContent').text('');
        $(this).find('#srpKillId').val($(e.relatedTarget).attr('data-kill-id'));
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
                "order": [[2, "asc"]],
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
                            });
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

    $('#srps tbody').on('click', 'button', function(btn) {
        $.ajax({
          headers: function() {},
          url: "{{ route('srpadmin.list') }}/" + btn.target.name + "/" + $(btn.target).text(),
          dataType: 'json',
          timeout: 5000
        }).done(function (data) {
          if (data.name === "Approve") {
              $("#id-"+data.value).html('<span class="badge badge-success">Approved</span>');
          } else if (data.name === "Reject") {
              $("#id-"+data.value).html('<span class="badge badge-danger">Rejected</span>');
          } else if (data.name === "Paid Out") {
              $("#id-"+data.value).html('<span class="badge badge-primary">Paid Out</span>');
          } else if (data.name === "Pending") {
              $("#id-"+data.value).html('<span class="badge badge-warning">Pending</span>');
          } else if (data.name === "Delete") {
            $("#id-"+data.value).html('<span class="badge badge-info">Pending Deletion</span>');
          }
          $("#approver-"+data.value).html(data.approver);
        });
    });

});
</script>
@endpush
