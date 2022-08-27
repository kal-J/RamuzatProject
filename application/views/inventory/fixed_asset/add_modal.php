<div class="modal inmodal fade" id="add_asset-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" >
        <div class="modal-content" >
        <form method="post" class="formValidate" action="<?php echo base_url("inventory/create_asset"); ?>" id="formInventory">
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
                        <label class="col-lg-3 col-form-label" title="Salvage Value">Salvage Value<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <input type="number" id="salvage_value" name="salvage_value" class="form-control" required>
                        </div>
                        <label class="col-lg-3 col-form-label">Expected Age (years)</label>
                        <div class="col-lg-3 form-group">
                                <input type="number" class="form-control" id="expected_age" name="expected_age" >
                        </div>
                    </div>
                     <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Asset Account (Debit)<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control m-b " id="asset_account_id" name="asset_account_id" data-bind='options: select2accounts([6]), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Depreciation / Appreciation <span class="text-danger">*</span></label>
                          <div class="col-lg-3 form-group">
                                <select class="form-control" id="depre_appre_id" name="depre_appre_id" data-bind='options: depre_appre_type, optionsText: "depre_appre", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: depre_appre' required data-msg-required="Depreciation / Appreciation is required">
                                </select>
                          </div>
                    </div>
                    <!-- ko with: depre_appre  -->
                    <fieldset  class="col-lg-12" data-bind="visible:  parseInt(id) ==parseInt(1)"><legend>Depreciation</legend>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Depreciation Method<span class="text-danger">*</span></label>
                        <div class="col-lg-3">
                            <select class="form-control m-b" name="depreciation_method_id" id="depreciation_method_id" style="width: 100%" required>
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
                            <input type="number" placeholder="" class="form-control" name="depreciation_rate" id="depreciation_rate" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label" for="depreciation_account_id">Depreciation Account<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-4">
                            <select class="form-control m-b" id="depreciation_account_id" name="depreciation_account_id" data-bind='options: $root.select2accounts(6), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                            <small><i>(Accumulated Depreciation)</i></small>
                        </div>
                        
                        <label class="col-lg-2 col-form-label" for="expense_account_id">Expense Account<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control m-b" id="expense_account_id" name="expense_account_id" data-bind='options: $root.select2accounts(15), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                            <small><i>(Depreciation expense)</i></small>
                        </div>
                          
                    </div>
                     <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Depreciation Loss Account <span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                             <select class="form-control m-b" id="depreciation_loss_account_id" name="depreciation_loss_account_id" data-bind='options: $root.select2accounts(15), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Depreciation Gain Account <span class="text-danger">*</span></label>
                          <div class="col-lg-3 form-group">
                               <select class="form-control m-b" id="depreciation_gain_account_id" name="depreciation_gain_account_id" data-bind='options: $root.select2accounts([13,4,12]), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                          </div>
                    </div>

                   </fieldset>
                   <fieldset  class="col-lg-12" data-bind="visible:  parseInt(id) ==parseInt(2)"><legend>Appreciation</legend>
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Appreciation Rate<span class="text-danger">*</span></label>
                        <div class="col-lg-2">
                            <input type="number" placeholder="" class="form-control" name="appreciation_rate" id="appreciation_rate" required>
                        </div>
                        <label class="col-lg-2 col-form-label" for="appreciation_account_id">Appreciation Account<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-5">
                            <select class="form-control m-b" id="appreciation_account_id" name="appreciation_account_id" data-bind='options: $root.select2accounts(6), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                            </select>
                            <small><i>(Accumulated Appreciation)</i></small>
                        </div>
                    </div>
                    <div class="form-group row">    
                        <label class="col-lg-3 col-form-label" for="income_account_id">Income Account<span class="text-danger">*</span></label>
                        <div class="col-lg-5">
                            <select class="form-control m-b" id="income_account_id" name="income_account_id" data-bind='options: $root.select2accounts(4), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                            <small><i>(Appreciation Income)</i></small>
                        </div>
                          
                    </div>
                        <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Appreciation Loss Account <span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select class="form-control m-b" id="appreciation_loss_account_id" name="appreciation_loss_account_id" data-bind='options: $root.select2accounts(15), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Appreciation Gain Account <span class="text-danger">*</span></label>
                          <div class="col-lg-3 form-group">
                                <select class="form-control m-b" id="appreciation_gain_account_id" name="appreciation_gain_account_id" data-bind='options: $root.select2accounts([12,13]), optionsText: $root.formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%">
                                <option  value="">--select--</option>
                            </select>
                          </div>
                    </div>
                </fieldset><br>
                   <!--/ko -->
                
                  
                   <br>
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
