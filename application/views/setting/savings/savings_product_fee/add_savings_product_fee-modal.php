<div class="modal inmodal fade" id="add_savings_product_fee-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>Savings_product_fee/Create" id="formSavings_product_fee">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                       Add new saving product fee
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body"><!-- Start of the modal body -->
                    <input type="hidden" name="id">
                    <div data-bind="with:product">
                         <input type="hidden" name="saving_product_id" id="saving_product_id" data-bind="value:(id)?id:'None'" >
                    </div>
                    <div class="form-group row">
                    <label class="col-lg-6 col-form-label">Saving fee<span class="text-danger">*</span></label>
                    <div class="col-lg-6 form-group">
                        <select class="form-control" id="savings_fees_id" name="savings_fees_id" data-bind='options: savingspdtfeesOption, optionsText: "feename", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: savingspdtfees' required data-msg-required="Savings fee is required">
                        </select>
                        <div>
                        </div>                            
                    </div> 
                    </div><!--/row -->
                    <div class="form-group row">
                        <div class="col-lg-6">
                        <label class="col-form-label">Savings fees Income Account<span class="text-danger">*</span></label>
                        <select class="form-control" name="savings_fees_income_account_id" id="savings_fees_income_account_id"  data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>
                       
                        <div class="col-lg-6">
                        <label class="col-form-label">Savings fees Income Receivable Account<span class="text-danger">*</span></label>
                        <select class="form-control" name="savings_fees_income_receivable_account_id" id="savings_fees_income_receivable_account_id"  data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>
                    </div>     
                </div><!-- End of the modal body -->
                <div class="modal-footer"><!-- start of the modal footer -->
                 <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-default btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                    <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i> 
                       Save
                    </button>
                   
                </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>
