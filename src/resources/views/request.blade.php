@extends('web::layouts.grids.8-4')

@section('title', trans('srp::srp.request'))
@section('page_header', trans('srp::srp.request'))

@section('left')
    <div class="box box-success box-solid">
        <div class="box-header">
            <h3 class="box-title">Request New SRP</h3>
        </div>
        <div class="box-body">
            <form role="form" action="{{ route('srp.saveKillMail') }}" method="post">
            <p>Copy and paste the link from the Character Sheet -> Interactions -> Combat Log -> Losses -> External URL into the box below.</p>
            <input type="text" class="form-control" id="killMailUrl" name="killMailUrl" size="60"/>
        </div>
        <div class="box-footer">
           <div class="btn-group pull-right" role="group">
                <input type="button" class="btn btn-default" id="readUrl" name="readUrl" value="Verify Killmail" />
                <input type="submit" class="btn btn-primary" id="saveKillMail" value="Submit Killmail" />
           </div>
                <input type="hidden" class="form-control" id="srpCharacterName" name="srpCharacterName" value="" />
                <input type="hidden" class="form-control" id="srpShipType" name="srpShipType" value="" />
                <input type="hidden" class="form-control" id="srpCost" name="srpCost" value="" />
                <input type="hidden" class="form-control" id="srpKillId" name="srpKillId" value="" />
                <input type="hidden" class="form-control" id="srpKillToken" name="srpKillToken" value="" />
                {{ csrf_field() }}
            </form>
        </div>
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
              <td><a href="https://zkillboard.com/kill/{{ $kill->kill_id }}/" target="_blank">{{ $kill->kill_id }}</a></td>
              <td>{{ $kill->character_name }}</td>
              <td>{{ $kill->ship_type }}</td>
              <td>{{ number_format($kill->cost) }} ISK</td>
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
              <td data-order="">{{ $kill->created_at }}</td>
            </tr>
            @endforeach
            </tbody>
          </table>
        </div>
    </div>
@stop

@section('right')
    <div class="box box-primary box-solid">
        <div class="box-header">
            <h3 class="box-title">Killmail Details</h3>
        </div>
        <div class="box-body" id="killMailSheet">
          <div>
            <table class="table table-condensed">
            <thead>
              <tr>
                <th class="bg-primary"><label class="label pull-right">Pilot:</label></th>
                <th class="bg-white"><label id="characterName"></label></th>
              </tr>
              <tr>
                <th class="bg-primary"><label class="label pull-right">Ship:</label></th>
                <th class="bg-white"><label id="shipType"></label></th>
              </tr>
              <tr>
                <th class="bg-primary"><label class="label pull-right">Cost:</label></th>
                <th class="bg-white"><label id="price"></label></th>
              </tr>
            </thead>
          </div>
            <table id="killReport" class="table table-condensed" width="100%">
            <tr>
                <th>Low Slot Module</th>
            <tr>
            @for ($slot = 0; $slot < 8; $slot++)
            <tr>
                <td id="LoSlot{{ $slot }}"></td>
            </tr>
            @endfor
            <tr>
                <th>Mid Slot Module</th>
            <tr>
            @for ($slot = 0; $slot < 8; $slot++)
            <tr>
                <td id="MedSlot{{ $slot }}"></td>
            </tr>
            @endfor
            <tr>
                <th>High Slot Module</th>
            <tr>
            @for ($slot = 0; $slot < 8; $slot++)
            <tr>
                <td id="HiSlot{{ $slot }}"></td>
            </tr>
            @endfor
            <tr>
                <th>Rigs</th>
            <tr>
            @for ($slot = 0; $slot < 3; $slot++)
            <tr>
                <td id="RigSlot{{ $slot }}"></td>
            </tr>
            @endfor
            </table>
            <table id="dronebay" class="table table-condensed">
            <tr>
                <th>Drone Bay</th>
                <th width="10em">Number</th>
            <tr>
            </table>
            <table id="cargo" class="table table-condensed">
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
  $(function () {
    $('#srps').DataTable()
    $('#example2').DataTable({
      'paging'      : true,
      'lengthChange': false,
      'searching'   : false,
      'ordering'    : true,
      'info'        : true,
      'autoWidth'   : false
    })
  })
</script>

<script type="application/javascript">
    $('.overlay').hide();
    $('#killMailSheet').hide();
    $('#saveKillMail').hide();

    $('#readUrl').on('click', function () {
      $('.overlay').show();
      $.ajax({
        headers: function() {},
        url: "{{ route('srp.getKillMail') }}",
        dataType: 'json',
        data: 'km=' + encodeURIComponent($('#killMailUrl').val()),
        timeout: 10000,
      }).done(function (result) {
        $('.overlay').hide();

        if (result) {
          $('#killMailSheet').show();
          $('#saveKillMail').show();
          for (var slot in result) {
            if ((slot != "cargo") && (slot != "dronebay")) {
              $('#' + slot).html("<img src='https://image.eveonline.com/Type/" + result[slot].id + "_32.png' height='16' />" + result[slot].name);
            }
          }
          for (var slot in result["cargo"]) {
            $('#cargo').append("<tr><td><img src='https://image.eveonline.com/Type/" + slot + "_32.png' height='16' />" + result["cargo"][slot].name + "</td><td>" + result["cargo"][slot].qty + "</td><tr>\n");
          }
          for (var slot in result["dronebay"]) {
            $('#dronebay').append("<tr><td><img src='https://image.eveonline.com/Type/" + slot + "_32.png' height='16' />" + result["dronebay"][slot].name + "</td><td>" + result["dronebay"][slot].qty + "</td></tr>\n");
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
          $('.overlay').hide();
          $('#killMailUrl').append("Killmail not Found");
        }
      }).fail(function () {
          $('.overlay').hide();
          $('#killMailUrl').append("Killmail not Found");
      });
    });
</script>

@endpush
