<div class="modal fade" tabindex="-1" role="dialog" id="srp-reason-edit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Reason</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form role="form" action="{{ route('srp.addReason') }}" method="post">

                    <div class="form-group">
                        <label for="srpPingContent">Reason</label>
                        <input id="reasonContent" class="form-control" name="srpReasonContent" rows="3" placeholder="Reason"></textarea>
                    </div>
                    <input type="hidden" class="form-control" id="srpKillId" name="srpKillId" value="0" />
                    {{ csrf_field() }}
                    <input type="submit" class="btn btn-primary" id="saveReasson" value="Save Reason" />
                </form>
            </div>
        </div>
    </div>
</div>