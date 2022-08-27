<style type="text/css">
    .blueText {
        color: blue;
        font-size: 10px;
    }
</style>
<!--div class="modal inmodal fade" id="add_savings_account" tabindex="-1" role="dialog" aria-hidden="true" commented out to allow the search functionality of the select2-->
<div class="modal inmodal fade" id="accept_withdraw" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" name="formWithdraw_requests"
                  action="<?php echo base_url(); ?>u/Withdraw_requests/accept_withdraw" id="formWithdraw_requests">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Accept the member request
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span
                                class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" />
                    <input type="hidden" name="account_no_id" />
                    <input type="hidden" name="transaction_channel_id" value="1"/>
                    <input type="hidden" name="payment_id" value="2"/>
                    
                    <!-- <input type="hidden" name="member_id" />
                    <input type="hidden" name="transaction_channel_id" value="1" />
                    <input type="hidden" name="transaction_type_id" value="1" />
                    <input type="hidden" name="payment_id" value="1" />
                    <input type="hidden" name="state_id" value="7" />
                    <input type="hidden" name="client_type" value="1" />
                    <input type="hidden" name="transaction_date" value="<?php //echo date('d-m-Y'); ?>" /> -->
                    
                    

                    <div class="form-group row">
                        <div class="col-xs-12 col-md-5">
                            <label>Amount</label>
                        </div>
                        <div class="col-sx-12 col-md-7">
                            <input class="form-control" readonly name="amount" >
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-xs-12 col-md-5">
                            <label>Narrative<span class="text-danger">*</span></label>
                        </div>
                        <div class="col-sx-12 col-md-7">
                            <textarea cols="5" rows="5" class="form-control" name="narrative"></textarea>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Accept</button>
                </div>
            </form>
        </div>
    </div>
</div>
