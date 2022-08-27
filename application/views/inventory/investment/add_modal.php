<div class="modal inmodal fade" id="add_investment_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo site_url("investiment/create"); ?>" id="formInvestment">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" > New Investment </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id" />
                <div class="row form-group">
                    <label class="col-lg-4 col-form-label">Investment Type</label>
                    <div class="col-lg-3">
                        <div class="input-group">
                            <select name="type" id="type" class="form-control">
                                <option value="1">Fixed Deposit</option> 
                                <option value="2">Bond</option>  
                              
                            </select>
                                                     
                        </div>
                    </div>
                      <label class="col-lg-2 col-form-label">Period<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <div class="input-group">
                                <input type="text" name="tenure" placeholder="Period" value="" class="form-control" required><span class="input-group-addon"><i class="fa fa-clock-o"></i></span>
                            </div>
                        </div>
                   
                     
                      
                       
                    </div>
                      <div class="form-group row">
                          <label class="col-lg-4 col-form-label">Investment Account <span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class="form-control m-b asset_creation" id="investment_account_id" name="investment_account_id" data-bind='options: $root.select2accounts(6), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                        
                        </div>   
                        <label class="col-lg-2 col-form-label">Income account<span class="text-danger">*</span></label>  
                         <div class="col-lg-3">  
                          <select class="form-control m-b asset_creation" id="income_account_id" name="income_account_id" data-bind='options: $root.select2accounts(12,13), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                     </div>
                      
                        
                    </div>

                   
                     <div class="form-group row">
                          <label class="col-lg-4 col-form-label">Expense Account ID<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class="form-control m-b asset_creation" id="expense_account_id" name="expense_account_id" data-bind='options: $root.select2accounts(15), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                        
                        </div>
                      
                         <label class="col-lg-2 col-form-label">Date<span class="text-danger">*</span></label>
                         <div class="col-lg-3">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" name="transaction_date" placeholder="Date" value="<?php echo date("d-m-Y"); ?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>

                   
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Narrative / Description<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" required name="description" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary">Save
                            </button>
                         <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>