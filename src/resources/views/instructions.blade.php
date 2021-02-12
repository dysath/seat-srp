@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.srp'))
@section('page_header', trans('srp::srp.srp'))
@section('page_description', trans('srp::srp.instructions'))

@push('head')
<link rel = "stylesheet"
   type = "text/css"
   href = "https://snoopy.crypta.tech/snoopy/seat-srp-instructions.css" />
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
                <p>The following instruction page will explain how to request SRP from the SRP program</p>
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
            <div class="card">
                <div class="card-header">Step 1</div>
                <div class="card-body">
                    <p class="card-text">
                        The first step is to create head to the <a href="{{ route('srp.request') }}">srp request</a> page. You can get the <code>External Url</code> from either an ingame killmail, or from a zkillboard link as shown below.

                        If relevant to the request, fill out the ping section.

                        <div class="accordion" id="imageExpander">
                            <div class="card">
                                <div class="card-header" id="headingOne">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            Ingame Method
                                        </button>
                                    </h2>
                                </div>

                                <div id="collapseOne" class="collapse" aria-labelledby="headingOne" data-parent="#imageExpander">
                                    <div class="card-body">
                                        <img src="{{ asset('web/img/ingame_esi_link.gif') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" id="headingTwo">
                                    <h2 class="mb-0">
                                        <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            zKillboard Method
                                        </button>
                                    </h2>
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#imageExpander">
                                    <div class="card-body">
                                    <img src="{{ asset('web/img/zkill_esi_link.gif') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </p>
                </div>
            </div>
            <div class="card">
                <div class="card-header">Step 2</div>
                <div class="card-body">
                    <p class="card-text">
                        Next click the <code>Verify Killmail</code> button. This will populate the right hand side of the page with the killmail information.
                        If the information is correct then you can press the <code>Submit Killmail</code> button that will have appeared next to the original verify button.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>


@stop