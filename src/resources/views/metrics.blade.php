@extends('web::layouts.grids.12')

@section('title', trans('srp::srp.metrics'))

@section('page_header')
    @lang('srp::srp.metrics')
    <div class="pull-right">
        <div id="srpFilterToggle" class="btn btn-default" title="Toggle SRP Filters"
             data-toggle="control-sidebar"
             data-slide="false"
        >
            <i class="fa fa-filter" aria-hidden="true"></i>
        </div>
    </div>
@stop

@section('full')

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-right">
            <li class="pull-left header">
                Payouts by Month
            </li>
            <li class="active">
                <a href="#summaryAll" data-toggle="tab" aria-expanded="true">All Users</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="chart tab-pane srpChart active" id="summaryAll">
                <canvas id="summaryAllChart"></canvas>
            </div>
        </div>
    </div>
    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-right">
            <li class="pull-left header">
                Top 50 Payouts
            </li>
            <li>
                <a href="#topShips" data-toggle="tab" aria-expanded="true">Ships</a>
            </li>
            <li class="active">
                <a href="#topPilots" data-toggle="tab" aria-expanded="false">Pilots</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="chart tab-pane srpChart active" id="topPilots">
                <canvas id="topPilotsChart"></canvas>
            </div>
            <div class="chart tab-pane srpChart" id="topShips">
                <canvas id="topShipsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="nav-tabs-custom">
        <ul class="nav nav-tabs pull-right">
            <li class="pull-left header">
                User Specific:
            </li>
            <li class="pull-left" style="line-height: 35px;">
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
            <li>
                <a href="#specificUserShips" data-toggle="tab" aria-expanded="false">Ships</a>
            </li>
            <li class="active">
                <a href="#specificUserSummary" data-toggle="tab" aria-expanded="true">Summary</a>
            </li>
        </ul>
        <div class="tab-content">
            <div class="chart tab-pane srpChart active" id="specificUserSummary">
                <canvas id="specificUserSummaryChart"></canvas>
            </div>
            <div class="chart tab-pane srpChart" id="specificUserShips">
                <canvas id="specificUserShipsChart"></canvas>
            </div>
        </div>
    </div>
@stop

@section('right-sidebar')
    <ul class="sidebar-menu tree" data-widget="tree">
        <li class="header">
            SRP Filters
        </li>
        <li class="treeview active">
            <a href="#">
                <i class="fa fa-cogs"></i>
                <span>SRP Status: {{ title_case($srp_status) }}</span>
                <i class="fa fa-angle-left pull-right"></i>
            </a>
            <ul class=treeview-menu>
                <li class="
                    @if(
                        url()->current() == route('srp.metrics', ['srp_status' => 'all']) ||
                        url()->current() == route('srp.metrics')
                        )
                        active
                    @endif"
                >
                    <a href="{{ route('srp.metrics', ['srp_status' => 'all']) }}">
                        All
                    </a>
                </li>
                <li class="@if(url()->current() == route('srp.metrics', ['srp_statu' => 'approved'])) active @endif">
                    <a href="{{ route('srp.metrics', ['srp_status' => 'approved']) }}">
                        Approved
                    </a>
                </li>
                <li class="@if(url()->current() == route('srp.metrics', ['srp_statu' => 'paid'])) active @endif">
                    <a href="{{ route('srp.metrics', ['srp_status' => 'paid']) }}">
                        Paid
                    </a>
                </li>
                <li class="@if(url()->current() == route('srp.metrics', ['srp_statu' => 'rejected'])) active @endif">
                    <a href="{{ route('srp.metrics', ['srp_status' => 'rejected']) }}">
                        Rejected
                    </a>
                </li>
                <li class="@if(url()->current() == route('srp.metrics', ['srp_statu' => 'unprocessed'])) active @endif">
                    <a href="{{ route('srp.metrics', ['srp_status' => 'unprocessed']) }}">
                        Unprocessed
                    </a>
                </li>
            </ul>
        </li>
    </ul>
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
        let summaryAllDataUrl = '{{ route('srp.metrics.api.web.summary.monthly', [
            'srp_status' => $srp_status,
            'limit' => 15,
        ]) }}';
        let summaryUserDataUrl = '{{ route('srp.metrics.api.web.summary.user', ['srp_status' => $srp_status,]) }}';
        let topShipsDataUrl = '{{ route('srp.metrics.api.web.top.ship', [
            'srp_status' => $srp_status,
            'limit' => 50,
        ]) }}';
        let topPilotsDataUrl = '{{ route('srp.metrics.api.web.top.user', [
            'srp_status' => $srp_status,
            'limit' => 50,
        ]) }}';
    </script>

    <script src="{{ asset('web/js/metrics-colors.js') }}"></script>
    <script src="{{ asset('web/js/metrics-summary.js') }}"></script>
@endpush