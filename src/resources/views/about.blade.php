@extends('web::layouts.grids.4-4-4')

@section('title', trans('srp::srp.about'))
@section('page_header', trans('srp::srp.srp'))
@section('page_description', trans('srp::srp.about'))

@section('left')

  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Functionality</h3>
    </div>
    <div class="card-body">

     <p>This plugin provides a very simple yet powerful functionality to coordinate an SRP (Ship Replacement Program).</p>

     <p> TODO: Fill this out with some more marketing schpeel </p>
    </div>
  </div>
@stop

@section('center')

  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">THANK YOU!</h3>
    </div>
    <div class="card-body">
      <div class="box-body">

        <p> Both <strong>SeAT</strong> and <strong>SRP</strong> are community creations designed to benefit you! I sincerely hope you enjoy using them. If you are feeling generous then please feel free to front up some isk to either of the projects.</p>

        <p>
            <table class="table table-borderless">
                <tr> <td>SRP</td> <td> <a href="https://evewho.com/character/96057938"> {!! img('characters', 'portrait', 96057938, 64, ['class' => 'img-circle eve-icon small-icon']) !!} Crypta Electrica</a></td></tr>

                <tr> <td>Seat</td> <td> <a href="https://evewho.com/corporation/98482334"> {!! img('corporations', 'logo', 98482334, 64, ['class' => 'img-circle eve-icon small-icon']) !!} eveseat.net</a></td></tr>
            </table>
        </p>

        <p> If you are one of those people who feels ISK is never enough..... I will just drop this here.... my <a href="https://www.patreon.com/cryptaelectrica"> patreon</a>.</p>
        </div>
    </div>
  </div>

@stop
@section('right')

  <div class="card card-default">
    <div class="card-header">
      <h3 class="card-title">Info</h3>
    </div>
    <div class="card-body">

      <legend>Bugs and Feature Requests</legend>

      <p>If you encounter a bug or have a suggestion, either contact Crypta-Eve on <a href="https://eveseat.github.io/docs/about/contact/">SeAT-Slack</a> or submit an <a href="https://github.com/dysath/seat-srp/issues/new">issue on Github</a></p>

    </div>
  </div>

@stop