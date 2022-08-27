<div class="modal inmodal fade" id="reverse-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("loan_reversal/reverse"); ?>" id="formReverseTransaction">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title"> Reverse Transaction </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="client_loan_id" id="client_loan_id" />
                    <input type="hidden" name="unique_id" id="unique_id" />

                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Reason?<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" name="reverse_msg" ></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Reverse
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>