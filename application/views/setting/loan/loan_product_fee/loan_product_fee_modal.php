<div class="modal inmodal fade" id="add_loan_product_fee-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>loan_product_fee/Create" id="formLoan_product_fee">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                       Add New Loan Product Fee
                   </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body"><!-- Start of the modal body -->
                    <input type="hidden" name="id">
                    <input type="hidden" name="loanproduct_id" id="loanproduct_id" value="<?php echo $loan_product['id'] ?>" >
                    <div class="form-group row">
                        <label class="col-lg-6 col-form-label">Loan&nbsp;Fee<span class="text-danger">*</span></label>
                        <div class="col-lg-6 form-group">
                            <select class="form-control" id="loanfee_id" name="loanfee_id" data-bind='options: loan_fees, optionsText: "feename", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: feename' required data-msg-required="Loan fee is required">
                            </select>
                            <div>
                              <div data-bind="with: feename"><span class="help-block-none"><div data-bind="text: (amountcalculatedas_id==2)?('Amount charged will be '+curr_format(amount*1)):('Amount charged will be '+(amount*1)+'% of Principal Amount')">Product description goes here.</div></span></div>
                          </div>                            
                          </div> 

                    </div><!--/row -->
                    <div class="form-group row">
                        <label class="col-lg-6 col-form-label">Loan fee (Income) A/C</label>
                        <div class="col-lg-6 form-group">
                            <select class="form-control" name="income_account_id" data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>   

                        <label class="col-lg-6 col-form-label">Loan fee (Income) Receivable A/C</label>
                        <div class="col-lg-6 form-group">
                            <select class="form-control" name="income_receivable_account_id"  data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>                                
                            </div>

                </div><!-- End of the modal body -->
                <div class="modal-footer"><!-- start of the modal footer -->
                <?php if((in_array('1', $loan_product_privilege))||(in_array('3', $loan_product_privilege))){ ?>
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
