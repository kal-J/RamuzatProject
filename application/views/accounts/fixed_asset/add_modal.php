<div class="modal inmodal fade" id="add_asset-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" >
        <div class="modal-content" style="width: 800px;">
        <form method="post" class="formValidate" action="<?php echo base_url("Fixed_asset/Create"); ?>" id="formFixed_asset">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "New Asset";
                        }
                        ?>
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id"/>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Asset Name<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input placeholder="" required class="form-control" name="asset_name" type="text">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Identification No or S/N:<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input placeholder="" required class="form-control" name="identity_no" type="text">
                        </div>
                        <label class="col-lg-2 col-form-label">Purchase Date<span class="text-danger">*</span></label>
                        <div class="col-lg-3 form-group">
                            <div class="input-group date" data-date-start-date="<?php echo $fiscal_year['start_date2']; ?>" data-date-end-date="+0d">
                                <input class="form-control" required name="purchase_date" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>

                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Purchase Cost<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <input type="number" id="purchase_cost" name="purchase_cost" class="form-control" required>
                        </div>
                        <label class="col-lg-3 col-form-label">Date When Put To Use<span class="text-danger">*</span></label>
                        <div class="col-lg-3 form-group">
                            <div class="input-group date">
                                <input class="form-control" required name="date_when" type="text"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label" title="Salvage Value">Sell-Off Value<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <input type="number" id="salvage_value" name="salvage_value" class="form-control" required>
                        </div>
                        <label class="col-lg-3 col-form-label">Expected Age (years)<span class="text-danger">*</span></label>
                        <div class="col-lg-3 form-group">
                                <input type="number" class="form-control" id="expected_age" name="expected_age" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Depreciation Method<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class="form-control asset_creation m-b" name="depreciation_method_id" style="width: 100%" required>
                                <option value="">-- select --</option>
                                <?php
                                foreach ($depreciation_method as $method) {
                                    echo "<option value='" . $method['id'] . "'>" . $method['method_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <label class="col-lg-3 col-form-label">Depreciation Rate<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <input type="number" placeholder="" class="form-control" name="depreciation_rate" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="depreciation_account_id">Depreciation Account<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-3">
                            <select class="form-control m-b asset_creation" id="depreciation_account_id" name="depreciation_account_id" data-bind='options: select2accounts(2), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                            <small><i>(Accumulated Depreciation)</i></small>
                        </div>
                        <div class="col-lg-1">
                            <button type="button" class="input-group-addon btn btn-info" data-toggle="modal" href="#add_account-modal" title="Add another account"  data-bind="click: form_origin(3)"><i class="fa fa-plus"></i></button>
                        </div>
                        <label class="col-lg-2 col-form-label" for="expense_account_id">Expense Account<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class="form-control m-b asset_creation" id="expense_account_id" name="expense_account_id" data-bind='options: select2accounts(15), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                            <small><i>(Depreciation expense)</i></small>
                        </div>
                            <div class="col-lg-1">
                            <button type="button" class="input-group-addon btn btn-info" data-toggle="modal" data-target="#add_account-modal" title="Add another account"  data-bind="click: form_origin(3)"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Asset Account (Debit)<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class="form-control m-b asset_creation" id="asset_account_id" name="asset_account_id" data-bind='options: select2accounts(6), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <button type="button" class="input-group-addon btn btn-info" data-toggle="modal" data-target="#add_account-modal" title="Add another account"  data-bind="click: form_origin(3)"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Payment mode<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class="form-control asset_creation m-b" id="pay_with_id" name="payment_mode_id" data-bind='options: paymentModeList, optionsText: "payment_mode", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:payment_mode' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>

                        </div>
                        <label class="col-lg-2 col-form-label"><span data-bind="text: pay_label">Cash</span> Account<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class=" form-control  asset_creation" id="account_pay_with_id" name="account_pay_with_id" data-bind='options: paymentModeAccList, optionsText: formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required >
                                <option value="">--select--</option>
                            </select>
                        </div>
                            <div class="col-lg-1">
                            <button type="button" class="input-group-addon btn btn-info" data-toggle="modal" data-target="#add_account-modal" title="Add another account"  data-bind="click: form_origin(3)"><i class="fa fa-plus"></i></button>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Description<span class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <textarea class="form-control" rows="2" required name="description" id="description"></textarea>
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
           </form>
            </div>  
    </div>
</div>
