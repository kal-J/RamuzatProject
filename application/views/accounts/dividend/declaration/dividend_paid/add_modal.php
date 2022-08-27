<div class="modal inmodal fade" id="pay_dividend-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <form method="post" class="formValidate" action="<?php echo site_url("dividend_declaration/payout"); ?>" id="formDividend_payment">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Dividends Payment</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="dividends_payable_acc_id" value="<?php echo $dividend_declaration['dividends_payable_acc_id'];?>" />
                    <input type="hidden" name="dividends_cash_acc_id" value="<?php echo $dividend_declaration['dividends_cash_acc_id'];?>" />
                    <input type="hidden" name="dividend_declaration_id" value="<?php echo $dividend_declaration['id'];?>" />
                    <input type="hidden" name="declaration_date" value="<?php echo $dividend_declaration['declaration_date'];?>" />
                    <input type="hidden" name="record_date" value="<?php echo $dividend_declaration['record_date'];?>" />
                    <input type="hidden" name="payment_type" value="<?php echo $dividend_declaration['cash_stock'];?>" />
                    <input type="hidden" name="share_issuance_id" value="<?php echo $dividend_declaration['share_issuance_id'];?>" />

                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="total_dividends">Total Declared Dividends</label>
                        <div class="form-group col-lg-8">
                            <input type="number" id="total_dividends" name="total_dividends" class="form-control" value="<?php echo $dividend_declaration['total_dividends']; ?>" readonly="readonly"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="total_dividends">Dividends Per Share</label>
                        <div class="form-group col-lg-8">
                            <input type="number" id="dividend_per_share" name="dividend_per_share" class="form-control" value="<?php echo $dividend_declaration['dividend_per_share']; ?>" readonly="readonly"/>
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
                        <label class="col-lg-4 col-form-label" for="payment_type">Payment Type</label>
                        <div class="form-group col-lg-8">
                            <input type="text" id="payment_type" name="payment_type" class="form-control" readonly="readonly" value="<?php echo $dividend_declaration['cash_stock']==1 ?'Cash':'Stock';?>">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="payment_date">Payment date<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-8">
                            <div class="input-group date" data-date-start-date="<?php echo date('d-m-Y', strtotime($fiscal_year['start_date'])); ?>">
                                <input type="text" id="transaction_date" name="transaction_date" class="form-control" value="" required ><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                  
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Narative</label>
                        <div class="col-lg-8">
                            <textarea class="form-control" required rows="2" name="narrative"></textarea>
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