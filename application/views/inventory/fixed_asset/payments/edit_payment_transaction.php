<div class="modal inmodal fade" id="edit_transaction_payment-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("asset_payment/edit_transaction"); ?>" id="formAsset_payment">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" > Update Payment Details </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>
                    <input type="hidden" name="transaction_no" />
                    <input type="hidden" name="journal_type_id" value="29" />
                
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Transaction Date<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" name="transaction_date" placeholder="Date" value="<?php echo date("d-m-Y"); ?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Narrative / Description<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" required name="narrative" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary">Update
                            </button>
                         <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>