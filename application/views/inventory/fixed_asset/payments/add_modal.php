<div class="modal inmodal fade" id="add_asset_payment-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("asset_payment/create"); ?>" id="formAddAsset_payment">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" data-bind="with: fixed_asset_detail"><span data-bind="text: asset_name"></span> payment</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body" >
                    <input type="hidden" name="id"/>
                    <div data-bind="with: fixed_asset_detail">
                        <input type="hidden" name="asset_account_id" data-bind="attr: {value:asset_account_id}"/>
                        <input type="hidden" name="asset_id" data-bind="attr: {value:id}"/>
                        </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Transaction Date<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" name="transaction_date" placeholder="Date" value="<?php echo date("d-m-Y"); ?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row" data-bind="with: fixed_asset_detail">
                        <label class="col-lg-4 col-form-label" for="amount">Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="number" name="amount" min="1" placeholder="Paid amount" class="form-control" data-bind="attr: {'data-rule-max':round(parseFloat(purchase_cost)-parseFloat($parent.asset_paid_amount()?$parent.asset_paid_amount():0),0), 'data-msg-max':'Amount must be less or equal to '+ curr_format(round(parseFloat(purchase_cost)-parseFloat($parent.asset_paid_amount()?$parent.asset_paid_amount():0),0))+' ( Unpaid balance )'}">
                        </div>
                    </div>
                   <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Payment mode<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <select class="form-control asset_creation m-b" id="pay_with_id" name="payment_id" data-bind='options:  paymentModeList, optionsText: "payment_mode", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:payment_mode' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label"><span data-bind="text: pay_label">Cash</span> Account<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <select class=" form-control  asset_creation" id="account_pay_with_id" name="fund_source_account_id" data-bind='options: paymentModeAccList, optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required >
                                <option value="">--select--</option>
                            </select>
                        </div>   
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Narrative<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <textarea class="form-control" rows="2" required name="narrative" required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <?php if ((in_array('1', $accounts_privilege)) || (in_array('3', $accounts_privilege))) { ?>
                        <button type="submit" class="btn btn-primary"><?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?></button>
<?php } ?>
                </div>
            </div>  
        </form>
    </div>
</div>