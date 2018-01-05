@extends('web::layouts.grids.8-4')

@section('title', trans('srp::srp.request'))
@section('page_header', trans('srp::srp.request'))

@section('left')
    <div class="box box-success box-solid">
        <div class="box-header">
            <h3 class="box-title">Request New SRP</h3>
        </div>
        <form role="form" action="{{ route('srp.saveKillMail') }}" method="post">
            <div class="box-body">
                <p>Copy and paste the link from the Character Sheet -> Interactions -> Combat Log -> Losses -> External
                   URL into the box below.</p>
                <div class="form-group">
                    <label for="killMailUrl" class="control-label">External Url</label>
                    <input type="text" class="form-control" id="killMailUrl" name="killMailUrl" placeholder="https://esi.tech.ccp.is/v1/killmails/9999999/sidufhus6f4654fdsdf4/?datasource=tranquility" />
                    <span class="help-block" style="display: none;">Invalid killmail address</span>
                </div>
                <div class="form-group">
                    <label for="srpPingContent">Ping</label>
                    <textarea class="form-control" name="srpPingContent" rows="3" placeholder="Put the ping content related to the fleet where you loose this ship."></textarea>
                </div>
            </div>
            <div class="box-footer">
                <div class="btn-group pull-right" role="group">
                    <input type="button" class="btn btn-default" id="readUrl" name="readUrl" value="Verify Killmail"/>
                    <input type="submit" class="btn btn-primary" id="saveKillMail" value="Submit Killmail"/>
                </div>
                <input type="hidden" class="form-control" id="srpCharacterName" name="srpCharacterName" value=""/>
                <input type="hidden" class="form-control" id="srpTypeId" name="srpTypeId" value="" />
                <input type="hidden" class="form-control" id="srpShipType" name="srpShipType" value=""/>
                <input type="hidden" class="form-control" id="srpCost" name="srpCost" value=""/>
                <input type="hidden" class="form-control" id="srpKillId" name="srpKillId" value=""/>
                <input type="hidden" class="form-control" id="srpKillToken" name="srpKillToken" value=""/>
                {{ csrf_field() }}
            </div>
        </form>
        <div class="overlay">
            <i class="fa fa-refresh fa-spin"></i>
        </div>
    </div>
    <div class="box box-success box-solid">
        <div class="box-header">
            <h3 class="box-title">My SRP Requests</h3>
        </div>
        <div class="box-body">
            <table id="srps" class="table table table-bordered table-striped">
                <thead>
                <tr>
                    <th>{{ trans('srp::srp.id') }}</th>
                    <th>{{ trans('srp::srp.characterName') }}</th>
                    <th>{{ trans('srp::srp.shipType') }}</th>
                    <th>{{ trans('srp::srp.costs') }}</th>
                    <th>{{ trans('srp::srp.paidout') }}</th>
                    <th>{{ trans('srp::srp.submitted') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($kills as $kill)
                    <tr>
                        <td><a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/"
                               target="_blank">{{ $kill->kill_id }}</a>
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
                                {{ number_format($kill->cost, 2) }} ISK
                            </button>
                        </td>
                        <td>
                            @if ($kill->approved === 0)
                                <span class="label label-warning">Pending</span>
                            @elseif ($kill->approved === -1)
                                <span class="label label-danger">Rejected</span>
                            @elseif ($kill->approved === 1)
                                <span class="label label-success">Approved</span>
                            @elseif ($kill->approved === 2)
                                <span class="label label-primary">Paid Out</span>
                            @endif
                        </td>
                        <td>
                            <span data-toggle="tooltip" data-placement="top" title="{{ $kill->created_at }}">{{ human_diff($kill->created_at) }}</span>
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

@section('right')
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title">Killmail Details</h3>
        </div>
        <div class="box-body">
            <div id="kill-report">
                <div>
                    <table class="table table-condensed">
                        <thead>
                        <tr>
                            <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Pilot:</label></th>
                            <th class="bg-white"><label id="characterName"></label></th>
                        </tr>
                        <tr>
                            <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Ship:</label></th>
                            <th class="bg-white"><label id="shipType"></label></th>
                        </tr>
                        <tr>
                            <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Cost:</label></th>
                            <th class="bg-white"><label id="price"></label></th>
                        </tr>
                        </thead>
                    </table>
                </div>
                <div>
                    <table class="table table-condensed table-striped" id="lowSlots">
                        <thead>
                            <tr>
                                <th>Low Slot Module</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <table class="table table-condensed table-striped" id="midSlots">
                        <thead>
                            <tr>
                                <th>Mid Slot Module</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <table class="table table-condensed table-striped" id="highSlots">
                        <thead>
                            <tr>
                                <th>High Slot Module</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <table class="table table-condensed table-striped" id="rigs">
                        <thead>
                            <tr>
                                <th>Rigs</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <table id="drones" class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th class="col-md-10">Drone Bay</th>
                                <th class="col-md-2">Number</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <table id="cargo" class="table table-condensed table-striped">
                        <thead>
                            <tr>
                                <th class="col-md-10">Cargo Bay Contents</th>
                                <th class="col-md-2">Number</th>
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
        })
    </script>

    <script type="application/javascript">
        $('.overlay').hide();
        $('#kill-report').hide();
        $('#saveKillMail').hide();

        $('#readUrl').on('click', function () {
            $('.overlay').show();
            kmFormGroup = $('#killMailUrl').parent('div.form-group');
            kmFormGroup.find('span.help-block').hide();
            kmFormGroup.removeClass('has-error');
            $('#highSlots, #midSlots, #lowSlots, #rigs, #cargo, #drones')
                .find('tbody')
                .empty();

            $.ajax({
                headers: function () {
                },
                url: "{{ route('srp.getKillMail') }}",
                dataType: 'json',
                data: 'km=' + encodeURIComponent($('#killMailUrl').val()),
                timeout: 10000,
            }).done(function (result) {
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

                    formattedPrice = result["price"];
                    $('#price').html(formattedPrice.toLocaleString() + " ISK");
                    $('#shipType').text(result["shipType"]);
                    $('#characterName').text(result["characterName"]);

                    $('#srpKillId').val(result["killId"]);
                    $('#srpKillToken').val(result["killToken"]);
                    $('#srpCharacterName').val(result["characterName"]);
                    $('#srpCost').val(result["price"]);
                    $('#srpShipType').val(result["shipType"]);
                    $('#srpTypeId').val(result["typeId"])
                }
                else {
                    $('.overlay').hide();
                    $('#killMailUrl').append("Killmail not Found");
                }
            }).fail(function () {
                $('.overlay').hide();
                kmFormGroup.addClass('has-error');
                kmFormGroup.find('span.help-block').show();
            });
        });
    </script>

@endpush
