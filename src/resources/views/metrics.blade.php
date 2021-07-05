@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.metrics'))

@push('head')
<link rel = "stylesheet"
   type = "text/css"
   href = "https://snoopy.crypta.tech/snoopy/seat-srp-metrics.css" />
@endpush

@section('page_header')
    @lang('srp::srp.metrics')
    <div class="float-right">
        <div id="srpFilterToggle" class="btn btn-default" title="Toggle SRP Filters"
             data-widget="control-sidebar"
             data-slide="false"
        >
            <i class="fa fa-filter" aria-hidden="true"></i>
        </div>
    </div>
@stop

@section('full')
        <div class="nav-tabs-custom card">
            <ul class="nav nav-tabs">
                <li class="float-left header nav-link">
                    Payouts by Month
                </li>
                <li class="active nav-link">
                    <a href="#summaryAll" data-toggle="tab" aria-expanded="true">All Users</a>
                </li>
            </ul>
            <div class="tab-content card-body">
                <div class="chart tab-pane srpChart active" id="summaryAll">
                    <canvas id="summaryAllChart"></canvas>
                </div>
            </div>
        </div>
    <div class="nav-tabs-custom card">
        <ul class="nav nav-tabs">
            <li class="float-left header nav-link">
                Top 50 Payouts
            </li>
            <li class="nav-item nav-link">
                <a href="#topShips" data-toggle="tab" aria-expanded="true">Ships</a>
            </li>
            <li class="active nav-item nav-link">
                <a href="#topPilots" data-toggle="tab" aria-expanded="false">Pilots</a>
            </li>
        </ul>
        <div class="tab-content card-body">
            <div class="chart tab-pane srpChart active" id="topPilots">
                <canvas id="topPilotsChart"></canvas>
            </div>
            <div class="chart tab-pane srpChart" id="topShips">
                <canvas id="topShipsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="nav-tabs-custom card">
        <ul class="nav nav-tabs float-right">
            <li class="float-left header  nav-link">
                User Specific:
            </li>
            <li class="float-left" style="line-height: 35px;">
                <select name="specificUserSelected" id="specificUserSelected" onchange="summarySpecificUser()">
                    @foreach($users as $group_id => $user_name)
                        @if($loop->first)
                            <option value="{{ $group_id }}" selected>{{ $user_name }}</option>
                            @continue
                        @endif
                        <option value="{{ $group_id }}">{{ $user_name }}</option>
                    @endforeach
                </select>
            </li>
            <li class="nav-item nav-link">
                <a href="#specificUserShips" data-toggle="tab" aria-expanded="false">Ships</a>
            </li>
            <li class="active nav-item nav-link">
                <a href="#specificUserSummary" data-toggle="tab" aria-expanded="true">Summary</a>
            </li>
        </ul>
        <div class="tab-content card-body">
            <div class="chart tab-pane srpChart active" id="specificUserSummary">
                <canvas id="specificUserSummaryChart"></canvas>
            </div>
            <div class="chart tab-pane srpChart" id="specificUserShips">
                <canvas id="specificUserShipsChart"></canvas>
            </div>
        </div>
        <div class="card-footer text-muted">
            Plugin maintained by <a href="{{ route('srp.about') }}"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a>. <span class="float-right snoopy" style="color: #fa3333;"><i class="fas fa-signal"></i></span>
        </div>
    </div>
@stop

@section('right-sidebar')
<nav class="mt-2">
        <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
            <h4 class="header">SRP Filters</h4>
            <li class="nav-item has-treeview">
                <a href="#" class="nav-link active">
                    <i class="fas fa-cogs"></i>
                    <p>
                        SRP Status: {{ Str::title($srp_status) }}
                        <i class="right fas fa-angle-left"></i>
                    </p>
                </a>
                <ul class="nav nav-treeview">
                    <li class="nav-item">
                        <a href="{{ route('srp.metrics', ['srp_status' => 'all']) }}" class="nav-link @if(
                            url()->current() == route('srp.metrics', ['srp_status' => 'all']) ||
                            url()->current() == route('srp.metrics')
                            )
                            active
                        @endif">
                            <p>All</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('srp.metrics', ['srp_status' => 'approved']) }}" class="nav-link @if(url()->current() == route('srp.metrics', ['srp_status' => 'approved'])) active @endif">
                            <p>Approved</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('srp.metrics', ['srp_status' => 'paid']) }}" class="nav-link @if(url()->current() == route('srp.metrics', ['srp_status' => 'paid'])) active @endif">
                            <p>Paid</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('srp.metrics', ['srp_status' => 'rejected']) }}" class="nav-link @if(url()->current() == route('srp.metrics', ['srp_status' => 'rejected'])) active @endif">
                            <p>Rejected</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('srp.metrics', ['srp_status' => 'unprocessed']) }}" class="nav-link @if(url()->current() == route('srp.metrics', ['srp_status' => 'unprocessed'])) active @endif">
                            <p>Unprocessed</p>
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </nav>
@stop

@push('head')
    <style>
        .srpChart{
            height: 35vh !important;
        }
    </style>
@endpush
@push('javascript')
    <script>
        let summaryAllDataUrl = "{{ route('srp.metrics.api.web.summary.monthly', ['status' => $srp_status,'limit' => 15,]) }}";
        let summaryUserDataUrl = "{{ route('srp.metrics.api.web.summary.user', ['status' => $srp_status,]) }}";
        let topShipsDataUrl = "{{ route('srp.metrics.api.web.top.ship', ['status' => $srp_status,'limit' => 50,]) }}";
        let topPilotsDataUrl = "{{ route('srp.metrics.api.web.top.user', ['status' => $srp_status,'limit' => 50,]) }}";
    </script>

    <script src="{{ asset('web/js/metrics-colors.js') }}"></script>
    <script src="{{ asset('web/js/metrics-summary.js') }}"></script>
@endpush