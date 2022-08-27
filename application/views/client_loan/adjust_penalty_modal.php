<div class="modal inmodal fade" id="adjust_penalty_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form id="form_adjust_penalty_modal" method="post" action="<?php echo site_url("client_loan/adjust_penalty"); ?>">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Adjust Penalty Payable</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="client_loan_id" data-bind="value:$root.loan_detail().id" />
                    <div class="form-group row d-flex flex-column container">
                        <label class="col-form-label" for="new_penalty_amount">New Penalty Amount</label>
                        <div class="form-group">
                            <input type="number" class="form-control" name="new_penalty_amount" />
                        </div>
                        <p class="blueText d-flex my-0">
                            <span data-bind="text: 'Current penalty payable : ' + curr_format($root.loan_detail().total_penalty)"></span>
                        </p>
                        
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <?php if (in_array('6', $client_loan_privilege)) { ?>
                        <button type="submit" class="btn btn-primary btn-sm save_data">Update</button>
                    <?php } ?>
                </div>
            </div>
        </form>
    </div>
</div>