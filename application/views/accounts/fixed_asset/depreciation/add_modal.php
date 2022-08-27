<div class="modal inmodal fade" id="add_depreciation-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <form method="post" class="formValidate" action="<?php echo site_url("depreciation/create"); ?>" id="formDepreciation">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title" data-bind="with: fixed_asset_detail">
                        <span data-bind="text: asset_name"></span> depreciation, FY <?php $current_fin_year = date("Y"); echo $current_fin_year; ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body" data-bind="with: fixed_asset_detail">
                    <input type="hidden" name="id"/>
                        <input type="hidden" name="expense_account_id" data-bind="attr: {value:expense_account_id}"/>
                        <input type="hidden" name="depreciation_account_id" data-bind="attr: {value:depreciation_account_id}"/>
                        <input type="hidden" name="fixed_asset_id" data-bind="attr: {value:id}"/>
                    <div class="form-group row">
                        <label class="col-lg-6 col-form-label">Date<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input type="text" name="transaction_date" placeholder="Date" value="<?php echo date("d-m-Y"); ?>" class="form-control" required><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-6 col-form-label" for="amount">Depreciation Amount (Debit)<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="number" name="amount" min="0" placeholder="Depreciation amount" class="form-control" data-bind="attr: {value:round(depreciation_method_id==1?((parseFloat(purchase_cost)-parseFloat(salvage_value))*parseFloat(depreciation_rate)/(100*(expected_age>0?parseInt(expected_age):1))):((parseFloat(purchase_cost)-$parent.depreciation_amount())*parseFloat(depreciation_rate)*2/100),0)}" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-6 col-form-label">Financial Year<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <input type="number" name="financial_year_id" value="<?php echo $current_fin_year; ?>" min="0" placeholder="Financial year" class="form-control" readonly="readonly">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Narrative<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
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