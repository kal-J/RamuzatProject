<div class="modal inmodal fade" id="add_gain_loss_modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="post" class="formValidate" action="<?php echo site_url("investiment/create2"); ?>" id="formGainLoss">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                   <div data-bind="with: $root.investment_details">
                    <h4 class="modal-title"> New Transaction ( <span data-bind="text: parseInt($root.investment_details().type)==1?'Fixed Deposit':'Bond'"></span> ) </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                
         <input type="hidden"  name="investment_id" data-bind="attr: {value:$root.investment_details().id}"/>
         <input type="hidden"  name="investment_account_id" data-bind="attr: {value:$root.investment_details().investment_account_id}"/>
         <input type="hidden"  name="expense_account_id" data-bind="attr: {value:$root.investment_details().expense_account_id}"/>
         <input type="hidden"  name="income_account_id" data-bind="attr: {value:$root.investment_details().income_account_id}"/>

                  </div>
              </div>
                <div class="modal-body">
                <div class="form-group row ">
                    <label class="col-lg-4 col-form-label">Transaction Type</label>

                    <div class="col-lg-3">
                        <div class="input-group">
                           <select class="form-control asset_creation m-b" id="action_method" name="transaction_type_id" data-bind='options:  actionMethodList, optionsText: "name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:action_method' style="width: 100%" required>
                                <option  value="" class="select2">--select--</option>
                            </select>
                                           
                        </div>
                    </div>
              
               
                 
                        <label class="col-lg-2 col-form-label">Transaction Date<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" name="transaction_date" placeholder="Date" value="<?php echo date("d-m-Y"); ?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar" required></i></span>
                            </div>
                        </div>
                    </div>
                
                    <!-- ko with:action_method -->
                     <div class="form-group row" data-bind="with:$root.investment_details">

                        <label class="col-lg-4 col-form-label">Amount<span class="text-danger">*</span></label>
                    
                        <div class="col-lg-4">
                            <div class="input-group"data-bind="visible:parseInt($parent.id)==parseInt(1)||parseInt($parent.id)==parseInt(2)||parseInt($parent.id)==parseInt(3)">
                                <input type="number" name="amount1" min="1" placeholder="Amount" class="form-control" data-bind="attr: {'data-rule-min':0, 'data-msg-max':'Amount must be greater than'+0}"required> 
                            </div>
                              
                           
                            <div class="input-group" data-bind="visible:parseInt($parent.id)==parseInt(4)">
                                <input type="number" name="amount2" min="1" placeholder="Amount " class="form-control" data-bind=" attr: {'data-rule-max':round(parseFloat(amount)+parseFloat(gain?gain:0),0), 'data-msg-max':'Withdrawal amount must be less than '+ curr_format(round(parseFloat(amount)+parseFloat(gain?gain:0),0))+' (Deposit+Gains) '}"required> 
                            </div>
                        
                      
                        </div>
                    </div>
                      <!-- /ko -->
                      <div class="form-group row">
                   
                        <label class="col-lg-4 col-form-label">Payment mode<span class="text-danger">*</span></label>
                        <div class="col-lg-6">

                            <select class="form-control asset_creation m-b" id="pay_with_id" name="payment_mode" data-bind='options:  paymentModeList, optionsText: "payment_mode", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:payment_mode' style="width: 100%" required>
                                <option  value="">--select--</option>
                            </select>

                        </div>
                    </div>
              
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label"><span data-bind="text: pay_label">Cash</span> Account<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select class=" form-control select2" id="account_pay_with_id" name="fund_source_account_id" data-bind='options: paymentModeAccList, optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required >
                                <option value="">--select--</option>
                            </select>
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
                        <button class="btn btn-primary">Save
                            </button>
                         <?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>