<style type="text/css">
    .blueText {
        color: blue;
        font-size: 10px;
    }
</style>
<!--div class="modal inmodal fade" id="add_savings_account" tabindex="-1" role="dialog" aria-hidden="true" commented out to allow the search functionality of the select2-->
<div class="modal inmodal fade" id="decline_request" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" name="formDeclinedWithdraw_requests"
                  action="<?php echo base_url(); ?>u/Withdraw_requests/decline_withdraw" id="formDeclinedWithdraw_requests" data-table-id="tblWithdraw_requests">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Decline member request
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span
                                class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" />
                  <div class="form-group row">
                    <div class="col-xs-12 col-md-5">
                        <label>Reason<span class="text-danger">*</span></label>
                    </div>
                    <div class="col-sx-12 col-md-7">
                        <textarea cols="5" rows="5" required class="form-control" name="decline_note" ></textarea>
                    </div>
                  </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Decline</button>
                </div>
            </form>
        </div>
    </div>
</div>
