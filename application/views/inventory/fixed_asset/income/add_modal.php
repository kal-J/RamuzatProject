<div class="modal inmodal fade" id="add_income-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("asset_income/create"); ?>" id="formAddAsset_income">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" data-bind="with: fixed_asset_detail"> <span data-bind="text: asset_name"> </span> Income </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <div  data-bind="with: fixed_asset_detail" >
                       <input type="hidden" name="asset_id" data-bind="attr: {value:id}"/>
                    </div>
                    <input type="hidden" name="id"/>
                <div class="row form-group">
                    <label class="col-lg-4 col-form-label">Income Type</label>
                    <div class="col-lg-8">
                        <div class="input-group">
                            <select name="income_type_id" data-bind='options: $root.income_items, optionsText: function(item){return item.income_type},  
                            optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id")' class="form-control" > 
                            </select>
                        </div>
                    </div>
                </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Transaction Date<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" name="transaction_date" placeholder="Date" value="<?php echo date("d-m-Y"); ?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label" for="amount">Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <input type="number" name="amount" min="1" placeholder="Income Amount" class="form-control">
                        </div>
                    </div>
                   <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Income Account (Credit)<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <select class="form-control m-b asset_creation" id="income_account_id" name="income_account_id" data-bind='options: $root.select2accounts([12,13]), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-4 col-form-label">Transaction Channel<span class="text-danger">*</span></label>
                        <div class="col-lg-8">
                            <select  class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: transaction_channels, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:transaction_channel, optionsDisableDefault: true' data-msg-required="Transaction channel must be selected" style="width: 100%" required >
                            </select>
                          
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