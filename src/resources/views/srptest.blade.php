@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.srp'))
@section('page_header', trans('srp::srp.srp'))
@section('page_description', trans('srp::srp.instructions'))

@push('head')
<link rel="stylesheet" type="text/css" href="https://snoopy.crypta.tech/snoopy/seat-srp-test.css" />
@endpush

@section('full')

<!-- TOP BANNER -->
<div class="row w-100">
    <div class="col">
        <div class="card card-default">
            <div class="card-header">
                <h1 class="card-title">Preface</h1>
            </div>
            <div class="card-body">
                <p>The following page allows you to test a request of SRP from the SRP program. It will download the killmail data, but recalculate each time the request is submitted. This will not create an SRP request</p>
            </div>
            <div class="card-footer text-muted">
                Plugin maintained by <a href="{{ route('srp.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
            </div>
        </div>
    </div>
</div>

<!-- Middle Instructions -->
<div class="row w-100">
    <div class="col">
        <div class="card-deck">
            <div class="card card-success">
                <div class="card-header">
                    <h3 class="card-title">Request Test SRP</h3>
                </div>
                <form role="form">
                    <div class="card-body">
                        <p>{{ trans('srp::srp.request_inst') }}</p>
                        <div class="form-group">
                            <label for="killMailUrl" class="control-label">External Url</label>
                            <input type="text" class="form-control" id="killMailUrl" name="killMailUrl" placeholder="https://esi.tech.ccp.is/v1/killmails/9999999/sidufhus6f4654fdsdf4/?datasource=tranquility" />
                            <span class="help-block" style="display: none;">Invalid killmail address</span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group pull-right" role="group">
                            <input type="button" class="btn btn-default" id="readUrl" name="readUrl" value="{{ trans('srp::srp.submit_killmail') }}" />
                        </div>
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
            <div class="card card-warning">
                <div class="card-header">
                    <h3 class="card-title">{{ trans('srp::srp.test_killmail') }}</h3>
                </div>
                <div class="card-body">
                    <div id="kill-report">
                        <div>
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Pilot:</label></th>
                                        <th class="bg-white"><label class='id-to-name' data-id="" id="characterName"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">Ship:</label></th>
                                        <th class="bg-white"><label id="shipType"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-primary"><label class="label pull-right" style="font-size: 100%">SRP Amount:</label></th>
                                        <th class="bg-white"><label id="price"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><label class="label pull-right" style="font-size: 100%">Rule Type</label></th>
                                        <th class="bg-white"><label id="type"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><label class="label pull-right" style="font-size: 100%">Base Value</label></th>
                                        <th class="bg-white"><label id="base"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><label class="label pull-right" style="font-size: 100%">Hull %</label></th>
                                        <th class="bg-white"><label id="hull"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><label class="label pull-right" style="font-size: 100%">Fit %</label></th>
                                        <th class="bg-white"><label id="fit"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><label class="label pull-right" style="font-size: 100%">Cargo %</label></th>
                                        <th class="bg-white"><label id="cargo"></label></th>
                                    </tr>
                                    <tr>
                                        <th class="bg-light"><label class="label pull-right" style="font-size: 100%">Deduct Insurance?</label></th>
                                        <th class="bg-white"><label id="insurance"></label></th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


@stop

@push('javascript')
@include('web::includes.javascript.id-to-name')

<script type="application/javascript">
    $('.overlay').hide();

    $('#readUrl').on('click', function() {
        $('.overlay').show();
        kmFormGroup = $('#killMailUrl').parent('div.form-group');
        kmFormGroup.find('span.help-block').hide();
        kmFormGroup.removeClass('has-error');

        $.ajax({
            headers: function() {},
            url: "{{ route('srp.getKillMail') }}",
            dataType: 'json',
            data: 'km=' + encodeURIComponent($('#killMailUrl').val()),
            timeout: 10000,
        }).done(function(result) {
            $('.overlay').hide();

            if (result) {

                console.log(result);
                
                formattedPrice = result["price"]["price"];
                $('#price').html(formattedPrice.toLocaleString() + " ISK");
                $('#shipType').text(result["shipType"]);
                $('#characterName').text(result["characterName"]);
                $('#characterName').data('id', result["characterName"]);
                // id_to_names();

                $('#type').text(result["price"]["rule"]);
                $('#base').text(result["price"]["base_value"].toLocaleString() + " ISK");
                $('#hull').text((result["price"]["hull_percent"] * 100).toLocaleString() + " %");
                $('#fit').text((result["price"]["fit_percent"] * 100).toLocaleString() + " %");
                $('#cargo').text((result["price"]["cargo_percent"] * 100).toLocaleString() + " %");
                $('#insurance').text(result["price"]["deduct_insurance"]);

                id_to_names();
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
    // id_to_names();
</script>


@endpush