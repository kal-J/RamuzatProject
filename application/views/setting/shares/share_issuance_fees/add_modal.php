<div class="modal inmodal fade" id="add_share_issuance_fees-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>share_issuance_fees/Create" id="formShare_issuance_fees">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add New Share Issuance Fee";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body"><!-- Start of the modal body -->
                    <input type="hidden" name="shareproduct_id" id="shareproduct_id" value="<?php echo $share_issuance['id'] ?>" >
                    <div class="form-group row">
                        <label class="col-lg-6 col-form-label">Share&nbsp;Fee<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <select class="form-control" id="sharefee_id" name="sharefee_id" data-bind='options: share_fees, optionsText: "feename", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: feename' required data-msg-required="Share fee is required">
                            </select>
                            <div>
                              <div data-bind="with: feename"><span class="help-block-none"><div data-bind="text: (amountcalculatedas_id==2)?('Amount charged will be '+curr_format(amount*1)):('Amount charged will be '+(amount*1)+'% of Principal Amount')">Product description goes here.</div></span></div>
                          </div>                            
                          </div> 

                    </div>
                        <div class="form-group row">
                        <div class="col-lg-6">
                        <label class="col-form-label">Income Account<span class="text-danger">*</span></label>
                        <select class="form-control" name="share_fees_income_account_id" id="share_fees_income_account_id"  data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>
                       
                        <div class="col-lg-6">
                        <label class="col-form-label">Receivable Account<span class="text-danger">*</span></label>
                        <select class="form-control" name="share_fees_income_receivable_account_id" id="share_fees_income_receivable_account_id"  data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>
                    </div> 

                </div><!-- End of the modal body -->
                <div class="modal-footer"><!-- start of the modal footer -->
                <?php if((in_array('1', $share_issuance_privilege))||(in_array('3', $share_issuance_privilege))){ ?>
                    <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> 
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save";
                        }
                        ?>
                    </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>

