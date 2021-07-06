@extends('web::layouts.grids.8-4')

@section('title', trans('srp::srp.request'))
@section('page_header', trans('srp::srp.request'))

@push('head')
<link rel="stylesheet" type="text/css" href="https://snoopy.crypta.tech/snoopy/seat-srp-request.css" />
@endpush

@section('left')
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">Request New SRP</h3>
    </div>
    <form role="form" action="{{ route('srp.saveKillMail') }}" method="post">
        <div class="card-body">
            <p>{{ trans('srp::srp.request_inst') }}</p>
            <div class="form-group">
                <label for="killMailUrl" class="control-label">External Url</label>
                <input type="text" class="form-control" id="killMailUrl" name="killMailUrl" placeholder="https://esi.tech.ccp.is/v1/killmails/9999999/sidufhus6f4654fdsdf4/?datasource=tranquility" />
                <span class="help-block" style="display: none;">Invalid killmail address</span>
            </div>
            <div class="form-group">
                <label for="srpPingContent">{{ trans('srp::srp.ping') }}</label>
                <textarea class="form-control" name="srpPingContent" rows="3" placeholder="{{ trans('srp::srp.ping_info') }}"></textarea>
            </div>
        </div>
        <div class="card-footer">
            <div class="btn-group pull-right" role="group">
                <input type="button" class="btn btn-default" id="readUrl" name="readUrl" value="{{ trans('srp::srp.verify_killmail') }}" />
                <input type="submit" class="btn btn-primary" id="saveKillMail" value="{{ trans('srp::srp.submit_killmail') }}" />
            </div>
            <input type="hidden" class="form-control" id="srpCharacterName" name="srpCharacterName" value="" />
            <input type="hidden" class="form-control" id="srpTypeId" name="srpTypeId" value="" />
            <input type="hidden" class="form-control" id="srpShipType" name="srpShipType" value="" />
            <input type="hidden" class="form-control" id="srpCost" name="srpCost" value="" />
            <input type="hidden" class="form-control" id="srpKillId" name="srpKillId" value="" />
            <input type="hidden" class="form-control" id="srpKillToken" name="srpKillToken" value="" />
            {{ csrf_field() }}
        </div>
    </form>
    <div class="overlay">
        <div class="d-flex justify-content-center">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>
<div class="card card-success">
    <div class="card-header">
        <h3 class="card-title">{{ trans('srp::srp.mysrp') }}</h3>
    </div>
    <div class="card-body">
        <table id="srps" class="table table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{{ trans('srp::srp.id') }}</th>
                    <th>{{ trans('srp::srp.characterName') }}</th>
                    <th>{{ trans('srp::srp.shipType') }}</th>
                    <th>{{ trans('srp::srp.costs') }}</th>
                    <th>{{ trans('srp::srp.paidout') }}</th>
                    <th>{{ trans('srp::srp.submitted') }}</th>
                    <th>{{ trans('srp::srp.approvedby') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($kills as $kill)
                <tr>
                    <td><a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/" target="_blank">{{ $kill->kill_id }}</a>
                        @if(!is_null($kill->ping()))
                        <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-ping" data-kill-id="{{ $kill->kill_id }}">
                            <i class="fa fa-comment"></i>
                        </button>
                        @endif
                    </td>
                    <td><span class='id-to-name' data-id="{{ $kill->character_name }}">{{ $kill->character_name }}</span></td>
                    <td>{{ $kill->ship_type }}</td>
                    <td>
                        <button type="button" class="btn btn-xs btn-link" data-toggle="modal" data-target="#insurances" data-kill-id="{{ $kill->kill_id }}">
                            {{ number_format($kill->cost, 2) }} ISK
                        </button>
                    </td>
                    <td>
                        @if ($kill->approved === 0)
                        <span class="badge badge-warning">Pending</span>
                        @elseif ($kill->approved === -1)
                        <span class="badge badge-danger">Rejected</span>
                        @elseif ($kill->approved === 1)
                        <span class="badge badge-success">Approved</span>
                        @elseif ($kill->approved === 2)
                        <span class="badge badge-primary">Paid Out</span>
                        @elseif ($kill->approved === 99)
                        <span class="badge badge-info">Pending Deletion</span>
                        @endif
                        @if(!is_null($kill->reason()))
                        <button class="btn btn-xs btn-link" data-toggle="modal" data-target="#srp-reason" data-kill-id="{{ $kill->kill_id }}">
                            <i class="fa fa-comment"></i>
                        </button>
                        @endif
                    </td>
                    <td>
                        <span data-toggle="tooltip" data-placement="top" title="{{ $kill->created_at }}">{{ human_diff($kill->created_at) }}</span>
                    </td>
                    <td>
                        {{ $kill->approver }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="card-footer text-muted">
        Plugin maintained by <a href="{{ route('srp.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
    </div>
</div>
@include('srp::includes.insurances-modal')
@include('srp::includes.ping-modal')
@include('srp::includes.reason-modal')
@stop

@section('right')
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">{{ trans('srp::srp.killmail_details') }}</h3>
    </div>
    <div class="card-body">
        <div id="kill-report">
            <div>
                <table class="table table-condensed">
                    <thead>
                        <tr>
                            <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Pilot:</label></th>
                            <th class="bg-white"><span class='id-to-name' data-id="" id="characterName"></span></th>
                        </tr>
                        <tr>
                            <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Ship:</label></th>
                            <th class="bg-white"><label id="shipType"></label></th>
                        </tr>
                        <tr>
                            <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">SRP Amount:</label></th>
                            <th class="bg-white"><label id="price"></label></th>
                        </tr>
                    </thead>
                </table>
            </div>
            <div>
                <table class="table table-condensed table-striped" id="lowSlots">
                    <thead>
                        <tr>
                            <th>{{ trans('srp::srp.low_slot_mod') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table class="table table-condensed table-striped" id="midSlots">
                    <thead>
                        <tr>
                            <th>{{ trans('srp::srp.mid_slot_mod') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table class="table table-condensed table-striped" id="highSlots">
                    <thead>
                        <tr>
                            <th>{{ trans('srp::srp.hi_slot_mod') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table class="table table-condensed table-striped" id="rigs">
                    <thead>
                        <tr>
                            <th>{{ trans('srp::srp.rigs') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="drones" class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-10">{{ trans('srp::srp.drone_bay') }}</th>
                            <th class="col-md-2">{{ trans('srp::srp.number') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                <table id="cargo" class="table table-condensed table-striped">
                    <thead>
                        <tr>
                            <th class="col-md-10">{{ trans('srp::srp.cargo_bay') }}</th>
                            <th class="col-md-2">{{ trans('srp::srp.number') }}</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@stop

@push('head')
<link rel="stylesheet" type="text/css" href="{{ asset('web/css/denngarr-srp-hook.css') }}" />
@endpush

@push('javascript')
@include('web::includes.javascript.id-to-name')
<script type="application/javascript">
    $(function() {
        $('#srps').DataTable();

        $('#srp-ping').on('show.bs.modal', function(e) {
            var link = "{{ route('srp.ping', 0) }}";

            $(this).find('.overlay').show();
            $(this).find('.modal-body>p').text('');

            $.ajax({
                url: link.replace('/0', '/' + $(e.relatedTarget).attr('data-kill-id')),
                dataType: 'json',
                method: 'GET'
            }).done(function(response) {
                $('#srp-ping').find('.modal-body>p').text(response.note).removeClass('text-danger');
            }).fail(function(jqXHR, status) {
                $('#srp-ping').find('.modal-body>p').text(status).addClass('text-danger');

                if (jqXHR.statusCode() !== 500)
                    $('#srp-ping').find('.modal-body>p').text(jqXHR.responseJSON.msg);
            });

            $(this).find('.overlay').hide();
        });

        $('#srp-reason').on('show.bs.modal', function(e) {
            var link = "{{ route('srp.reason', 0) }}";

            $(this).find('.overlay').show();
            $(this).find('.modal-body>p').text('');

            $.ajax({
                url: link.replace('/0', '/' + $(e.relatedTarget).attr('data-kill-id')),
                dataType: 'json',
                method: 'GET'
            }).done(function(response) {
                $('#srp-reason').find('.modal-body>p').text(response.note).removeClass('text-danger');
            }).fail(function(jqXHR, status) {
                $('#srp-reason').find('.modal-body>p').text(status).addClass('text-danger');

                if (jqXHR.statusCode() !== 500)
                    $('#srp-reason').find('.modal-body>p').text(jqXHR.responseJSON.msg);
            });

            $(this).find('.overlay').hide();
        });

        $('#insurances').on('show.bs.modal', function(e) {
                var link = "{{ route('srp.insurances', 0) }}";
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
                        "order": [
                            [0, "asc"]
                        ],
                        "columnDefs": [{
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
            .on('hidden.bs.modal', function(e) {
                var table = $('#insurances').find('table').DataTable();
                table.destroy();
            });
    })
</script>

<script type="application/javascript">
    $('.overlay').hide();
    $('#kill-report').hide();
    $('#saveKillMail').hide();

    $('#readUrl').on('click', function() {
        $('.overlay').show();
        kmFormGroup = $('#killMailUrl').parent('div.form-group');
        kmFormGroup.find('span.help-block').hide();
        kmFormGroup.removeClass('has-error');
        $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones')
            .find('tbody')
            .empty();

        $.ajax({
            headers: function() {},
            url: "{{ route('srp.getKillMail') }}",
            dataType: 'json',
            data: 'km=' + encodeURIComponent($('#killMailUrl').val()),
            timeout: 10000,
        }).done(function(result) {
            $('.overlay').hide();

            if (result) {
                $('#kill-report').show();
                $('#saveKillMail').show();
                for (var slot in result) {

                    if (slot.indexOf('HiSlot') >= 0)
                        $('#highSlots').find('tbody').append(
                            "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                    if (slot.indexOf('MedSlot') >= 0)
                        $('#midSlots').find('tbody').append(
                            "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                    if (slot.indexOf('LoSlot') >= 0)
                        $('#lowSlots').find('tbody').append(
                            "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                    if (slot.indexOf('RigSlot') >= 0)
                        $('#rigs').find('tbody').append(
                            "<tr><td><img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name + "</td></tr>");

                    if (slot.indexOf('cargo') >= 0)
                        for (item in result[slot])
                            $('#cargo').find('tbody').append(
                                "<tr><td><img src='https://image.eveonline.com/Type/" + item + "_32.png' height='16' />" + result[slot][item].name + "</td><td>" + result[slot][item].qty + "</td></tr>");

                    if (slot.indexOf('dronebay') >= 0) {
                        for (item in result[slot])
                            $('#drones').find('tbody').append(
                                "<tr><td><img src='https://image.eveonline.com/Type/" + item + "_32.png' height='16' />" + result[slot][item].name + "</td><td>" + result[slot][item].qty + "</td></tr>");
                    }
                }

                formattedPrice = result["price"]["price"];
                $('#price').html(formattedPrice.toLocaleString() + " ISK");
                $('#shipType').text(result["shipType"]);
                $('#characterName').text(result["characterName"]);
                $('#characterName').attr('data-id', result["characterName"]);
                ids_to_names();

                $('#srpKillId').val(result["killId"]);
                $('#srpKillToken').val(result["killToken"]);
                $('#srpCharacterName').val(result["characterName"]);
                $('#srpCost').val(result["price"]["price"]);
                $('#srpShipType').val(result["shipType"]);
                $('#srpTypeId').val(result["typeId"])
            } else {
                $('.overlay').hide();
                $('#killMailUrl').append("Killmail not Found");
            }
        }).fail(function() {
            $('.overlay').hide();
            kmFormGroup.addClass('has-error');
            kmFormGroup.find('span.help-block').show();
        });
    });
    ids_to_names();
</script>

@endpush