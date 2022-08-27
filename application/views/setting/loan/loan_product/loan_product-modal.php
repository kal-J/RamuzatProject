<div class="modal inmodal fade" id="add_loan_product-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>loan_product/Create" id="formLoan_product">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add New Loan Product";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <!-- Start of the modal body -->
                    <input type="hidden" name="id" id="id">
                    <fieldset class="col-lg-12">
                        <legend>Product Details</legend>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Product Name<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input placeholder="" required class="form-control" name="product_name" type="text">
                            </div>
                            <label class="col-lg-2 col-form-label">Available To<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" name="available_to_id">
                                    <option value="" selected>--Select--</option>
                                    <?php
                                    foreach ($available_to as $value) {
                                        echo "<option value='" . $value['id'] . "'>" . $value['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="product_code" class="form-label col-lg-2" >Product Code</label>
                             <div class="col-lg-4 form-group">
                                <input type="text" name="product_code" id="product_code" class="form-control">
                            </div>
                        </div>
                        <!--/row -->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Min. Guarantor<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group">
                                <input min="0" required class="form-control" name="min_guarantor" type="number">
                            </div>
                            <label class="col-lg-3 col-form-label">Loan Security<br><small>(in percentage)</small><span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group">
                                <input min="0" step="0.01" max="999.99" required class="form-control" name="min_collateral" type="number">
                            </div>
                        </div>
                        <!--/row -->
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Description</label>
                            <div class="col-lg-10 form-group">
                                <textarea class="form-control" rows="3" name="description" id="description"></textarea>
                            </div>
                        </div>
                        <!--/row-->
                    </fieldset>
                    <fieldset class="col-lg-12">
                        <legend>Amount</legend>
                        <div class="form-group row">
                            <label class="col-lg-1 col-form-label">Min.<span class="text-danger">*</span></label>

                            <div class="col-lg-3">
                                <input min="0" step="0.01" required class="form-control" name="min_amount" type="number">
                            </div>

                            <label class="col-lg-1 col-form-label">Max.<span class="text-danger">*</span></label>

                            <div class="col-lg-3">
                                <input min="0" step="0.01" required class="form-control" name="max_amount" type="number">
                            </div>
                            <label class="col-lg-1 col-form-label">Default<span class="text-danger">*</span></label>

                            <div class="col-lg-3">
                                <input min="0" step="0.01" required class="form-control" name="def_amount" type="number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Loan Portfolio Account (Receivable)</label>
                            <div class="col-lg-3 form-group">
                                <select class="form-control loan_product_fees_selects" name="loan_receivable_account_id" data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                                </select>
                            </div>
                            <label class="col-lg-3 col-form-label">Select Fund Source A/C<span class="text-danger">*</span></label>
                            <div class="col-lg-3">
                                <select name="fund_source_account_id" class="form-control" data-bind='options: $root.select2accounts([3,4,5]), optionsText: $root.formatAccount2, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id")' style="width: 100%" required></select>
                            </div>
                        </div>
                    </fieldset>
                    <!--/col-lg-12 -->
                    <fieldset class="col-lg-12">
                        <legend>Interest Rate <br /><small><em>All rates entered should be in per annum</em></small></legend>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Minimum<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" required class="form-control" name="min_interest" type="number">
                            </div>

                            <label class="col-lg-2 col-form-label">Maximum<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" required class="form-control" name="max_interest" type="number">
                            </div>
                            <label class="col-lg-2 col-form-label">Default<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" required class="form-control" name="def_interest" type="number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Interest Income Account</label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control loan_product_fees_selects" name="interest_income_account_id" data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                                </select>
                            </div>
                            <label class="col-lg-3 col-form-label">Interest Income Receivable Account</label>
                            <div class="col-lg-3 form-group">
                                <select class="form-control loan_product_fees_selects" name="interest_receivable_account_id" data-bind='options: select2accounts(2), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                                </select>
                            </div>

                        </div>
                        <div class="form-group row">
                            <label class="col-lg-4 col-form-label">Interest Calculated<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" id="product_type_id" name="product_type_id" data-bind='options: product_types, optionsText: "type_name", optionsCaption: "Select...", optionsAfterRender: setOptionValue("id"), value: product_type' required data-msg-required="Loan product type is required">
                                </select>
                                <div data-bind="with: product_type"><span class="help-block-none"><small data-bind="text: description">Description goes here.</small></span></div>
                            </div>
                        </div>
                    </fieldset>
                    <!--/col-lg-12 -->
                    <fieldset class="col-lg-12">
                        <legend>Collateral Security</legend>
                        <div class="form-group row">
                            <div class="col-lg-5 d-flex align-items-center">
                                <label class="col-form-label">Use Shares as Security? <span class="text-danger">*</span></label>&nbsp;&nbsp;
                                <span class="mt-2">
                                    <center>
                                        <label> <input checked value="0" name="use_shares_as_security" type="radio" data-bind="checked: use_shares_as_security"> No </label>
                                        <label> <input value="1" type="radio" name="use_shares_as_security" data-bind="checked: use_shares_as_security"> Yes</label>
                                    </center>
                                </span>
                            </div>

                            <div class="col-lg-6 d-flex align-items-center">
                                <label class="col-form-label">Use Savings as Security? <span class="text-danger">*</span></label>&nbsp;&nbsp;
                                <span class="mt-2">
                                    <center>
                                        <label> <input checked value="0" name="use_savings_as_security" type="radio" data-bind="checked: use_savings_as_security"> No </label>
                                        <label> <input value="1" type="radio" name="use_savings_as_security" data-bind="checked: use_savings_as_security"> Yes</label>
                                    </center>
                                </span>


                            </div>

                        </div>

                        <input class="d-none" type="number" name="use_savings_as_security" data-bind="textInput: use_savings_as_security" disabled>
                        <input class="d-none" type="number" name="use_shares_as_security" data-bind="textInput: use_shares_as_security" disabled>

                        <!--ko if: parseInt(use_savings_as_security()) == 1 || parseInt(use_shares_as_security()) == 1  -->
                        <div class="form-group row">
                            <label class="col-lg-5 col-form-label">Mandatory savings or shares collateral?<span class="text-danger">*</span></label>
                            <span class="mt-2">
                                <center>
                                    <label> <input checked value="0" name="mandatory_sv_or_sh" type="radio" data-bind="checked: mandatory_sv_or_sh"> No </label>
                                    <label> <input value="1" type="radio" name="mandatory_sv_or_sh" data-bind="checked: mandatory_sv_or_sh"> Yes</label>
                                </center>
                            </span>

                        </div>
                        <!--/ko -->

                    </fieldset>
                    <fieldset class="col-lg-12">
                        <legend>Offset Period</legend>
                        <div class="form-group row">
                            <label class="col-lg-1 col-form-label">Min.<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input placeholder="" required class="form-control" name="min_offset" type="number">
                            </div>

                            <label class="col-lg-1 col-form-label">Max.<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input placeholder="" required class="form-control" name="max_offset" type="number">
                            </div>
                            <label class="col-lg-2 col-form-label">Default<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input placeholder="" required class="form-control" name="def_offset" type="number">
                            </div>
                            <div class="col-lg-2">
                                <select class="form-control" name="offset_made_every">
                                    <option value="">--Select--</option>
                                    <?php
                                    foreach ($repayment_made_every as $value) {
                                        echo "<option value='" . $value['id'] . "'>" . $value['made_every_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <!--/col-lg-12 -->

                </div><!-- End of the modal body -->
                <div class="modal-footer">
                    <!-- start of the modal footer -->
                    <?php if ((in_array('1', $loan_product_privilege)) || (in_array('3', $loan_product_privilege))) { ?>
                        <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                            <i class="fa fa-check"></i>
                            <?php
                            if (isset($saveButton)) {
                                echo $saveButton;
                            } else {
                                echo "Save";
                            }
                            ?>
                        </button>
                    <?php } ?>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>