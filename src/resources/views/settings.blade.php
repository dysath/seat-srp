@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.settings'))
@section('page_header', trans('srp::srp.settings'))

@push('head')
<link rel="stylesheet" type="text/css" href="https://snoopy.crypta.tech/snoopy/seat-srp-config.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">
@endpush

@section('full')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card card-success border-success">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('srp::srp.settings') }}</h3>
                </div>
                <form method="POST" action="{{ route('srp.savesettings')  }}" class="form-horizontal">
                    <div class="card-body">
                        {{ csrf_field() }}
                        <h4>Webhook Config</h4>
                        <div class="form-group row">
                            <label for="webhook_url" class="col-sm-3 col-form-label">Webhook URL</label>
                            <div class="col-sm-8">
                                <div class="input-group col-sm">
                                    <input class="form-control" type="text" name="webhook_url" id="webhook_url" size="32" value="{{ setting('denngarr_seat_srp_webhook_url', true) }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="mention_role" class="col-sm-3 col-form-label">Discord Mention Role</label>
                            <div class="col-sm-8">
                                <div class="input-group col-sm">
                                    <input class="form-control" type="text" name="mention_role" id="mention_role" size="32" value="{{ setting('denngarr_seat_srp_mention_role', true) }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="srp_method" class="col-sm-3 col-form-label">SRP Method</label>
                            <div class="form-check form-check-inline">
                                @if (setting('denngarr_seat_srp_advanced_srp', true) == "0")
                                <input class="form-check-input" type="radio" name="srp_method" id="method1" value="0" checked>
                                @else
                                <input class="form-check-input" type="radio" name="srp_method" id="method1" value="0">
                                @endif
                                <label class="form-check-label" for="method1">Simple</label>
                            </div>
                            <div class="form-check form-check-inline">
                                @if (setting('denngarr_seat_srp_advanced_srp', true) == "1")
                                <input class="form-check-input" type="radio" name="srp_method" id="method2" value="1" checked>
                                @else
                                <input class="form-check-input" type="radio" name="srp_method" id="method2" value="1">
                                @endif
                                <label class="form-check-label" for="method2">Advanced</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="srp_delete" class="col-sm-3 col-form-label">Process Pending Deletions</label>
                            <input id="srp_delete" class="btn btn-danger float-right" value="DELETE!">
                        </div>
                    </div>
                    <div class="card-footer">
                        <input class="btn btn-success float-right" type="submit" value="Update">
                    </div>
                </form>
            </div>
        </div>
        
    </div>
</div>


<div class="card card-info">
    <div class="card-header">
        <h3 class="card-title">{{ trans('srp::srp.advanced_settings') }}</h3>
    </div>

    <div class="card-body">
        {{ csrf_field() }}

        <p>
            This section allows for the configuration of an advanced buyback system. Rules are evaluated from top to bottom of the below list, stopping on the first match (will always match the default rule at the bottom).
        </p>
        <p>
            Once a match is found the formula used is BaseValue + ( HullValue * Hull% ) + ( FitValue * Fit% ) + ( CargoValue * Cargo% ) - [if insurance is deducted](insurance payout - insurance cost).
        </p>

        <div class="accordion" id="rulesets">
            <div class="card m-0">
                <div class="card-header" id="headingOne">
                    <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Type Rules
                        </button>
                    </h2>
                </div>

                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#rulesets">
                    <div class="card-body">
                        These rules are made for a specific type, eg a Blackbird or a Rook.

                        <form class="needs-validation" id="type_rule_maker" novalidate>
                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <label for="type_type_id">Type</label>
                                    <select class="form-control" id="type_type_id" required>
                                        @foreach($types as $type)
                                        <option value="{{ $type->typeID }}">{{ $type->typeName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md">
                                    <label for="type_source">Price Source</label>
                                    <select class="form-control" id="type_source" required>
                                        <option value="evepraisal">evepraisal</option>
                                    </select>
                                </div>

                                <div class="form-group col-md">
                                    <label for="type_base">Base Value</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="type_base" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">ISK</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="type_hull_pc">Hull %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="type_hull_pc" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="type_fit_pc">Fit %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="type_fit_pc" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="type_cargo_pc">Cargo %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="type_cargo_pc" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="type_ins">Deduct Insurance?</label>
                                    <div class="input-group mt-2 ml-3">
                                        <input type="checkbox" class="" value="" id="type_ins">
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="type_add">Submit</label>
                                    <button type="button" data-placement="top" class="btn btn-primary form-control" id="type_add">Add / Update</button>
                                </div>


                            </div>

                        </form>

                        <!-- This is the area where Type rules are displayed -->

                        <table id="type_table" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Price Source</th>
                                    <th>Base Value</th>
                                    <th>Hull %</th>
                                    <th>Fit %</th>
                                    <th>Cargo %</th>
                                    <th>Deduct Insurance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
            <div class="card m-0">
                <div class="card-header" id="headingTwo">
                    <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Group Rules
                        </button>
                    </h2>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#rulesets">
                    <div class="card-body">
                        These rules are made for a specific group, eg Battleships or Cruisers.

                        <form class="needs-validation" id="group_rule_maker" novalidate>
                            <div class="form-row">

                                <div class="form-group col-md-2">
                                    <label for="group_type_id">Group</label>
                                    <select class="form-control" id="group_group_id" required>
                                        @foreach($groups as $group)
                                        <option value="{{ $group->groupID }}">{{ $group->groupName }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md">
                                    <label for="group_source">Price Source</label>
                                    <select class="form-control" id="group_source" required>
                                        <option value="evepraisal">evepraisal</option>
                                    </select>
                                </div>

                                <div class="form-group col-md">
                                    <label for="group_base">Base Value</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="group_base" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">ISK</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="group_hull_pc">Hull %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="group_hull_pc" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="group_fit_pc">Fit %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="group_fit_pc" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="group_cargo_pc">Cargo %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" class="form-control" id="group_cargo_pc" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="group_ins">Deduct Insurance?</label>
                                    <div class="input-group mt-2 ml-3">
                                        <input type="checkbox" class="" value="" id="group_ins">
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="group_add">Submit</label>
                                    <button type="button" data-placement="top" class="btn btn-primary form-control" id="group_add">Add / Update</button>
                                </div>


                            </div>

                        </form>

                        <!-- This is the area where Type rules are displayed -->

                        <table id="group_table" class="table table-striped" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Group</th>
                                    <th>Price Source</th>
                                    <th>Base Value</th>
                                    <th>Hull %</th>
                                    <th>Fit %</th>
                                    <th>Cargo %</th>
                                    <th>Deduct Insurance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header" id="headingThree">
                    <h2 class="mb-0">
                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Default Rules
                        </button>
                    </h2>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#rulesets">
                    <div class="card-body">
                        <form method="POST" action="{{ route('srp.saveadvdefault') }}" class="needs-validation" id="group_rule_maker" novalidate>
                            {{ csrf_field() }}
                            <div class="form-row">

                                <div class="form-group col-md">
                                    <label for="default_source">Price Source</label>
                                    <select class="form-control" name="default_source" id="default_source" required>
                                        <option value="evepraisal" >evepraisal</option>
                                    </select>
                                </div>

                                <div class="form-group col-md">
                                    <label for="default_base">Base Value</label>
                                    <div class="input-group mb-3">
                                        <input type="number" name="default_base" class="form-control" id="default_base" value="{{ setting('denngarr_seat_srp_advrule_def_base', true) }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">ISK</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="default_hull_pc">Hull %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" name="default_hull_pc" class="form-control" id="default_hull_pc" value="{{ setting('denngarr_seat_srp_advrule_def_hull', true) }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="default_fit_pc">Fit %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" name="default_fit_pc" class="form-control" id="default_fit_pc" value="{{ setting('denngarr_seat_srp_advrule_def_fit', true) }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="default_cargo_pc">Cargo %</label>
                                    <div class="input-group mb-3">
                                        <input type="number" name="default_cargo_pc" class="form-control" id="default_cargo_pc" value="{{ setting('denngarr_seat_srp_advrule_def_cargo', true) }}" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="default_ins">Deduct Insurance?</label>
                                    <div class="input-group mt-2 ml-3">
                                        @if (setting('advrule_def_ins', true) == "1")
                                        <input type="checkbox" class="" name="default_ins" id="default_ins" checked>
                                        @else
                                        <input type="checkbox" class="" name="default_ins" id="default_ins">
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group col-md">
                                    <label for="default_add">Submit</label>
                                    <button type="submit" data-placement="top" class="btn btn-primary form-control" id="default_add">Save Defaults</button>
                                </div>


                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card-footer text-muted">
        Plugin maintained by <a href="{{ route('srp.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
    </div>

</div>

@endsection

@push('javascript')
<script type="application/javascript">
    window.LaravelDataTables = window.LaravelDataTables || {};

    if (!$.fn.dataTable.isDataTable('#type_table')) {
        window.LaravelDataTables["typeTableBuilder"] = $('#type_table').DataTable({
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: "{{ route('srp.adv.type.get') }}",
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'GET'
                }
            },
            columns: [{
                    data: "type",
                    name: "type",
                    title: "Type",
                    "orderable": true,
                    "searchable": true
                },
                {
                    data: "price_source",
                    name: "price_source",
                    title: "Price Source",
                    "orderable": false,
                    "searchable": false
                },
                {
                    data: "base_value",
                    name: "base_value",
                    title: "Base Value",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "hull_percent",
                    name: "hull_percent",
                    title: "Hull Percent",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "fit_percent",
                    name: "fit_percent",
                    title: "Fit Percent",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "cargo_percent",
                    name: "cargo_percent",
                    title: "Cargo Percent",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "deduct_insurance",
                    name: "deduct_insurance",
                    title: "Insurance Deducted",
                    "orderable": true,
                    "searchable": false
                },
                {
                    defaultContent: "",
                    data: "action",
                    name: "action",
                    title: "Action",
                    "orderable": false,
                    "searchable": false
                }
            ],
            "drawCallback": function() {
                $("[data-toggle=tooltip]").tooltip();
            }
        });
    }

    if (!$.fn.dataTable.isDataTable('#group_table')) {
        window.LaravelDataTables["typeTableBuilder"] = $('#group_table').DataTable({
            dom: 'Bfrtip',
            processing: true,
            serverSide: true,
            order: [
                [0, 'desc']
            ],
            ajax: {
                url: "{{ route('srp.adv.group.get') }}",
                type: 'POST',
                headers: {
                    'X-HTTP-Method-Override': 'GET'
                }
            },
            columns: [{
                    data: "group",
                    name: "group",
                    title: "Group",
                    "orderable": true,
                    "searchable": true
                },
                {
                    data: "price_source",
                    name: "price_source",
                    title: "Price Source",
                    "orderable": false,
                    "searchable": false
                },
                {
                    data: "base_value",
                    name: "base_value",
                    title: "Base Value",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "hull_percent",
                    name: "hull_percent",
                    title: "Hull Percent",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "fit_percent",
                    name: "fit_percent",
                    title: "Fit Percent",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "cargo_percent",
                    name: "cargo_percent",
                    title: "Cargo Percent",
                    "orderable": true,
                    "searchable": false
                },
                {
                    data: "deduct_insurance",
                    name: "deduct_insurance",
                    title: "Insurance Deducted",
                    "orderable": true,
                    "searchable": false
                },
                {
                    defaultContent: "",
                    data: "action",
                    name: "action",
                    title: "Action",
                    "orderable": false,
                    "searchable": false
                }
            ],
            "drawCallback": function() {
                $("[data-toggle=tooltip]").tooltip();
            }
        });
    }

    $(document).ready(function() {

        $('#type_type_id').select2({
            sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            minimumResultsForSearch: 10,
        });

        $('#type_source').select2({
            sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            minimumResultsForSearch: 10,
        });

        $('#group_group_id').select2({
            sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            minimumResultsForSearch: 10,
        });

        $('#group_source').select2({
            sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            minimumResultsForSearch: 10,
        });

        $('#default_source').select2({
            sorter: data => data.sort((a, b) => a.text.localeCompare(b.text)),
            theme: 'bootstrap4',
            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
            minimumResultsForSearch: 10,
        });

        $('#default_source').value = "{{ setting('denngarr_seat_srp_advrule_def_source', true) }}";

    });

    const tform = document.getElementById('type_rule_maker');
    const gform = document.getElementById('group_rule_maker');

    $('#srp_delete').on('click', function() {
        $.ajax({
            type: "GET",
            url: "{{ route('srp.deletions') }}",
            success: function(data) {
                $('#srp_delete').removeClass("btn-danger");
                $('#srp_delete').removeClass("btn-warning");
                $('#srp_delete').addClass("btn-success");
                $('#srp_delete').prop('value', 'Deleted ' + JSON.parse(data).deleted + ' items');
                console.log(data);
            },
            error: function() {
                $('#srp_delete').removeClass("btn-success");
                $('#srp_delete').removeClass("btn-warning");
                $('#srp_delete').addClass("btn-danger");
                $('#srp_delete').prop('value', "Failed Deletion, check your logs");
            }
        })
    });

    $('#type_add').on('click', function() {

        $('#type_add').popover('dispose');

        if (!tform.checkValidity()) {
            $('#type_add').popover({
                content: "Please enure all details are filled out",
                title: "Details Missing"
            });
            $('#type_add').popover('show');
            return
        }

        $.ajax({
            type: "POST",
            url: "{{ route('srp.adv.type.add')}}",
            data: JSON.stringify({
                type_id: $('#type_type_id').val(),
                source: $('#type_source').val(),
                base_value: $('#type_base').val(),
                hull_percent: $('#type_hull_pc').val(),
                fit_percent: $('#type_fit_pc').val(),
                cargo_percent: $('#type_cargo_pc').val(),
                deduct_insurance: $('#type_ins').is(":checked") ? 1 : 0,
                rule_type: 'type',
            }),
            contentType: "application/json; charset=utf-8",
            success: function() {

                $('#type_table').DataTable().ajax.reload();

            },
            error: function(data) {
                alert("ERROR\n" + data.responseJSON.message);
            },
        })

    });

    $('#group_add').on('click', function() {

        $('#group_add').popover('dispose');

        if (!gform.checkValidity()) {
            $('#group_add').popover({
                content: "Please enure all details are filled out",
                title: "Details Missing"
            });
            $('#group_add').popover('show');
            return
        }

        $.ajax({
            type: "POST",
            url: "{{ route('srp.adv.type.add')}}",
            data: JSON.stringify({
                group_id: $('#group_group_id').val(),
                source: $('#group_source').val(),
                base_value: $('#group_base').val(),
                hull_percent: $('#group_hull_pc').val(),
                fit_percent: $('#group_fit_pc').val(),
                cargo_percent: $('#group_cargo_pc').val(),
                deduct_insurance: $('#group_ins').is(":checked") ? 1 : 0,
                rule_type: 'group',
            }),
            contentType: "application/json; charset=utf-8",
            success: function() {

                $('#group_table').DataTable().ajax.reload();

            },
            error: function(data) {
                alert("ERROR\n" + data.responseJSON.message);
            },
        })

    });
</script>

@endpush