@extends('web::layouts.grids.6-6')

@section('title', trans('srp::srp.settings'))
@section('page_header', trans('srp::srp.settings'))

@push('head')
<link rel = "stylesheet"
   type = "text/css"
   href = "https://snoopy.crypta.tech/snoopy/seat-srp-config.css" />
@endpush

@section('left')
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
                        <input class="form-control" type="text" name="webhook_url" id="webhook_url" size="32" value="{{ setting('webhook_url', true) }}" />
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="mention_role" class="col-sm-3 col-form-label">Discord Mention Role</label>
                <div class="col-sm-8">
                    <div class="input-group col-sm">
                        <input class="form-control" type="text" name="mention_role" id="mention_role" size="32" value="{{ setting('mention_role', true) }}" />
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <input class="btn btn-success float-right" type="submit" value="Update">
        </div>
    </form>
</div>
@endsection

@section('right')
<div class="card card-success border-danger">
    <div class="card-header ">
        <h3 class="card-title">{{ trans('srp::srp.settings') }}</h3>
    </div>
    <div class="card-body">
        <div class="col-sm-12">
            <p><label>Webhook URL</label> This is the discord webhook url</p>
        </div>
        <div class="col-sm-12">
            <p><label>Mention Role</label> This is the discord role to mention</p>
        </div>
    </div>
    <div class="card-footer text-muted">
        Plugin maintained by <a href="{{ route('srp.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
    </div>
</div>
@endsection
