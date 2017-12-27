@extends('web::layouts.grids.8-4')

@section('title', trans('srp::srp.request'))
@section('page_header', trans('srp::srp.request'))

@section('left')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Request New SRP</h3>
        </div>
        <div class="panel-body">
	    <p>Copy and paste the link from the Character Sheet -> Interactions -> Combat Log -> Losses -> External URL into the box below.</p>
            <div class="form-group">
                <div class="input-group input-group-sm">
                    <input type="text" class="loading" id="killMailUrl" name="killMailUrl" size="60"/>
                    <input type="button" id="readUrl" name="readUrl" value="Verify Killmail" />
                    <form role="form" action="{{ route('srp.saveKillMail') }}" method="post">
                        <input type="submit" id="saveKillMail" value="Submit Killmail" />
                        <input type="hidden" class="form-control" id="srpCharacterName" name="srpCharacterName" value="" />
                        <input type="hidden" class="form-control" id="srpShipType" name="srpShipType" value="" />
                        <input type="hidden" class="form-control" id="srpCost" name="srpCost" value="" />
                        <input type="hidden" class="form-control" id="srpKillId" name="srpKillId" value="" />
                        <input type="hidden" class="form-control" id="srpKillToken" name="srpKillToken" value="" />
                        {{ csrf_field() }}
                    </form>
                </div>
           </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">My SRP Requests</h3>
        </div>
        <div class="panel-body">
          <table class="table datatable table-condensed table-hover table-responsive">
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
              <td data-order=""><a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/" target="_blank">{{ $kill->kill_id }}</a></td>
              <td data-order="">{{ $kill->character_name }}</td>
              <td data-order="">{{ $kill->ship_type }}</td>
              <td data-order="">{{ number_format($kill->cost) }} ISK</td>
              <td data-order="">
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
              <td data-order="">{{ $kill->created_at }}</td>
            </tr>
            @endforeach
            </tbody>
          </table>
        </div>
    </div>
@stop

@section('right')
    <div class="panel panel-default">
        <div class="panel-heading">
            <h3 class="panel-title">Killmail Details</h3>
        </div>
        <div class="panel-body" id="killMailSheet">
            <table class="table">
                <tr>
                    <td><label>Pilot:</label><label id="characterName"></label></td>
                    <td><label>Ship:</label><label id="shipType"></label></td>
                    <td style="text-align: right"><label id="price"></label></td>
                </tr>
            </table>
            <table id="killReport" class="table table-striped" width="100%">
            <tr>
                <th>Low Slot Module</th>
                <th style="width: 2em">Number</th>
            <tr>
            @for ($slot = 0; $slot < 8; $slot++)
            <tr>
                <td id="LoSlot{{ $slot }}"></td>
                <td id="LoSlot{{ $slot }}-qty"></td>
            </tr>
            @endfor
            <tr>
                <th>Mid Slot Module</th>
                <th>Number</th>
            <tr>
            @for ($slot = 0; $slot < 8; $slot++)
            <tr>
                <td id="MedSlot{{ $slot }}"></td>
                <td id="MedSlot{{ $slot }}-qty"></td>
            </tr>
            @endfor
            <tr>
                <th>High Slot Module</th>
                <th>Number</th>
            <tr>
            @for ($slot = 0; $slot < 8; $slot++)
            <tr>
                <td id="HiSlot{{ $slot }}"></td>
                <td id="HiSlot{{ $slot }}-qty" width="10em"></td>
            </tr>
            @endfor
            <tr>
                <th>Rigs</th>
                <th>Number</th>
            <tr>
            @for ($slot = 0; $slot < 3; $slot++)
            <tr>
                <td id="RigSlot{{ $slot }}"></td>
                <td id="RigSlot{{ $slot }}-qty" width="10em"></td>
            </tr>
            @endfor
            </table>
            <table id="dronebay" class="table table-striped">
            <tr>
                <th>Drone Bay</th>
                <th width="10em">Number</th>
            <tr>
            </table>
            <table id="cargo" class="table table-striped">
            <tr>
                <th>Cargo Bay Contents</th>
                <th width="10em">Number</th>
            <tr>
            </table>
        </div>
    </div>
@stop

@push('javascript')
<style>
.loading {    
    background-color: #ffffff;
    background-size: 20px 20px;
    background-position:right center;
    background-repeat: no-repeat;
}
</style>

<script type="application/javascript">
    $('.loading').css('background-image', 'none');
    $('#killMailSheet').hide();
    $('#saveKillMail').hide();

    $('#readUrl').on('click', function () {
      $('.loading').css('background-image', 'url("{{ asset('web/img/spinner.gif') }}")');
      $.ajax({
        headers: function() {},
        url: "{{ route('srp.getKillMail') }}",
        dataType: 'json',
        data: 'km=' + encodeURIComponent($('#killMailUrl').val()),
        timeout: 10000,
      }).done(function (result) {
        $('.loading').css('background-image', 'none');

        if (result) {
          $('#killMailSheet').show();
          $('#saveKillMail').show();
          for (var slot in result) {
            if ((slot != "cargo") && (slot != "dronebay")) {
              $('#' + slot).html("<img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' />" + result[slot].name);
              $('#' + slot + '-qty').html(result[slot].qty);
            }
          }
          for (var slot in result["cargo"]) {
            $('#cargo').append("<tr><td><img src='https://image.eveonline.com/Type/" + slot + "_32.png' />" + result["cargo"][slot].name + "</td><td>" + result["cargo"][slot].qty + "</td><tr>\n");
          }
          for (var slot in result["dronebay"]) {
            $('#dronebay').append("<tr><td><img src='https://image.eveonline.com/Type/" + slot + "_32.png' />" + result["dronebay"][slot].name + "</td><td>" + result["dronebay"][slot].qty + "</td></tr>\n");
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
        }
        else {
          $('.loading').css('background-image', 'none');
          $('#killMailUrl').append("Killmail not Found");
        }
      }).fail(function () {
          $('.loading').css('background-image', 'none');
          $('#killMailUrl').append("Killmail not Found");
      });
    });
</script>

@endpush
