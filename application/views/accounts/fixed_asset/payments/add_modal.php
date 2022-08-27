<div class="modal inmodal fade" id="add_asset_payment-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("asset_payment/create"); ?>" id="formAsset_payment">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" data-bind="with: fixed_asset_detail"><span data-bind="text: asset_name"></span> payment</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body" data-bind="with: fixed_asset_detail">
                    <input type="hidden" name="id"/>
                        <input type="hidden" name="creditor_account_id" data-bind="attr: {value:account_pay_with_id}"/>
                        <input type="hidden" name="fixed_asset_id" data-bind="attr: {value:id}"/>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Date<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" name="transaction_date" placeholder="Date" value="<?php echo date("d-m-Y"); ?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="amount">Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="number" name="amount" min="0" placeholder="Paid amount" class="form-control" data-bind="attr: {value:round(parseFloat(purchase_cost)-parseFloat($parent.asset_paid_amount()),0)}">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Transaction Channel<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: $parent.transaction_channels, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:$parent.transaction_channel, optionsDisableDefault: true' data-msg-required="Transaction channel must be selected" style="width: 100%" required >
                            </select>
                            <!-- ko with: $parent.transaction_channel-->
                           <!--  <input type="hidden" name="cash_or_bank_account_id" id="cash_or_bank_account_id" data-bind="value: linked_account_id"/> -->
                            <!--/ko-->
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