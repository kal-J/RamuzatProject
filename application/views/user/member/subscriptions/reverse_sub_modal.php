<div class="modal inmodal fade" id="reverse_sub-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("Client_subscription/reverse_transaction"); ?>" id="formReverseClient_subscription">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" > Reverse  Payment </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="text" name="id" />
                    <input type="text" name="transaction_no" />
                    <input type="text" name="payment_id" />
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Reason?<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" required name="reverse_msg" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if ((in_array('1', $share_privilege)) || (in_array('3', $share_privilege))) { ?>
                        <button type="submit" class="btn btn-danger">Reverse
                            </button>
                         <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>