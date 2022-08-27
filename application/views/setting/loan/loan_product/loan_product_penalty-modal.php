<div class="modal inmodal fade" id="add_loan_product_penalty-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>loan_product/create" id="formLoan_product_penalty">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Set Loan Product Repayment and Penalty
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <!-- Start of the modal body -->
                    <input type="hidden" name="id" id="id">
                    <fieldset class="col-lg-12">
                        <legend>Disbursement Info</legend>
                        <!--  <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Tax Rate Source<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <input placeholder="" required class="form-control" name="tax_rate_source" type="text">
                            </div>
                            <label class="col-lg-2 col-form-label">Tax Calculation Method<span class="text-danger">*</span></label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" name="product_type">
                                    <option value="1" selected>A</option>
                                    <option value="2">B</option>
                                    <option value="3">C</option>
                                </select>
                            </div>       
                        </div> -->
                        <!--/row -->
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Max Tranches<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group">
                                <input placeholder="" required class="form-control" name="max_tranches" type="text">
                            </div>
                            <label class="col-lg-2 col-form-label">Days in a year</label>
                            <div class="col-lg-2 form-group">
                                <select class="form-control" name="days_of_year">
                                    <option value="" selected>--Select the days--</option>
                                    <option value="1">365</option>
                                    <option value="2">360</option>
                                </select>
                            </div>
                            <?php if ((in_array('6', $modules)) && (in_array('5', $modules))) { ?>
                                <label class="col-lg-2 col-form-label">Link to Savings A/C<span class="text-danger">*</span></label>
                                <div class="col-lg-2 form-group">
                                    <center>
                                        <label> <input checked value="1" name="link_toDeposit_account" type="radio"> Yes</label>
                                        <label> <input value="0" name="link_toDeposit_account" type="radio"> No</label>
                                    </center>
                                </div>
                            <?php } ?>
                        </div>
                        <!--/row -->
                    </fieldset>
                    <fieldset class="col-lg-12">
                        <legend>Repayment Installments</legend>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Minimum<span class="text-danger">*</span></label>
                            <div class="col-lg-2">
                                <input placeholder="" required class="form-control" name="min_repayment_installments" type="number">
                            </div>
                            <label class="col-lg-2 col-form-label">Maximum<span class="text-danger">*</span></label>
                            <div class="col-lg-2">
                                <input placeholder="" required class="form-control" name="max_repayment_installments" type="number">
                            </div>
                            <label class="col-lg-2 col-form-label">Default<span class="text-danger">*</span></label>
                            <div class="col-lg-2">
                                <input required class="form-control" name="def_repayment_installments" type="number">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Repayment Made Every<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group">
                                <input placeholder="" required class="form-control" name="repayment_frequency" type="number">
                            </div>
                            <select class="col-lg-3 form-control" name="repayment_made_every">
                                <option value="" selected>--Select the frequency--</option>
                                <?php
                                foreach ($repayment_made_every as $value) {
                                    echo "<option value='" . $value['id'] . "'>" . $value['made_every_name'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Bad Debt Loan A/C</label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" name="written_off_loans_account_id" data-bind='options: select2accounts(15), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                                </select>
                            </div>
                            <label class="col-lg-3 col-form-label">Miscellaneous Account</label>
                            <div class="col-lg-3 form-group">
                                <select class="form-control" name="miscellaneous_account_id" data-bind='options: select2accounts(13), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <!--/col-lg-12 -->
                    <fieldset class="col-lg-12">
                        <legend>Penalty Details</legend>
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Penalty Applicable<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group mt-1">
                                <center>
                                    <label> <input checked value="1" name="penalty_applicable" type="radio" data-bind="checked: penaltyApplicable"> Yes</label>
                                    <label> <input value="0" name="penalty_applicable" type="radio" data-bind="checked: penaltyApplicable"> No</label>
                                </center>
                            </div>
                            
                            <!-- ko if: parseInt(penaltyApplicable())===1 -->
                            <label class="col-lg-4 col-form-label">Apply penalty after Loan endDate<span class="text-danger">*</span></label>
                            <div class="col-lg-2 form-group mt-1">
                                <center>
                                    <label> <input checked value="1" name="penalty_applicable_after_due_date" type="radio" data-bind="checked: penalty_applicable_after_due_date"> Yes</label>
                                    <label> <input value="0" name="penalty_applicable_after_due_date" type="radio" data-bind="checked: penalty_applicable_after_due_date"> No</label>
                                </center>
                            </div>
                            <!-- /ko -->

                        </div>
                        <!--/row-->

                        <!-- ko if: parseInt(penaltyApplicable())===1 -->
                        <div class="form-group row">
                            <label class="col-lg-3 col-form-label">Penalty Calculation Method</label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" name="penalty_calculation_method_id" data-bind="options: penalty_calculation_method,  optionsText: 'method_description', optionsCaption: '---select---', optionsAfterRender: setOptionValue('id'), value: penalty_calculation_method_id" style="width: 100%" required data-msg-required="Select an option"></select>
                            </div>

                            <!-- ko if: penalty_calculation_method_id() && parseInt(penalty_calculation_method_id().id) === 2 -->
                            <label class="col-lg-2 col-form-label">Amount</label>
                            <div class="col-lg-3 form-group">
                                <input min="0" class="form-control" name="fixed_penalty_amount" type="number">
                            </div>
                            <!-- /ko -->

                        </div>
                        <!-- /ko -->

                        <!-- ko if: penalty_calculation_method_id() && parseInt(penalty_calculation_method_id().id) === 1 -->
                        <div class="form-group row" data-bind="visible: parseInt(penaltyApplicable())===1">
                            <label class="col-lg-2 col-form-label">Min. Rate<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" class="form-control" name="min_penalty_rate" type="number">
                            </div>

                            <label class="col-lg-2 col-form-label">Max. Rate<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input placeholder="" min="0" step="0.01" class="form-control" name="max_penalty_rate" type="number">
                            </div>
                            <label class="col-lg-2 col-form-label">Default Rate<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" class="form-control" name="def_penalty_rate" type="number">
                            </div>
                        </div>
                        <!-- /ko -->

                        <!-- ko if: penalty_calculation_method_id() && parseInt(penalty_calculation_method_id().id) -->
                        <div class="form-group row" data-bind="visible: parseInt(penaltyApplicable())===1">
                            <label class="col-lg-3 col-form-label">Charged Per<span class="text-danger">*</span></label>
                            <div class="col-lg-3 form-group">
                                <select class="form-control" name="penalty_rate_chargedPer" style="width: 100%">
                                    <option value="" selected>--Select--</option>
                                    <?php
                                    foreach ($repayment_made_every as $value) {
                                        echo "<option value='" . $value['id'] . "'>" . $value['made_every_name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            
                        </div>
                        <!-- /ko -->

                        <!--/row-->
                        <div class="form-group row" data-bind="visible: parseInt(penaltyApplicable())===1">
                            <label class="col-lg-2 col-form-label">Min.Tolerance<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" required class="form-control" name="min_grace_period" type="number">
                            </div>

                            <label class="col-lg-2 col-form-label">Max.Tolerance<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" required class="form-control" name="max_grace_period" type="number">
                            </div>
                            <label class="col-lg-2 col-form-label">Def.Tolerance<span class="text-danger">*</span></label>

                            <div class="col-lg-2">
                                <input min="0" step="0.01" required class="form-control" name="def_grace_period" type="number">
                            </div>
                        </div>
                        <div class="form-group row" data-bind="visible: parseInt(penaltyApplicable())===1">
                            <label class="col-lg-2 col-form-label">Income Account</label>
                            <div class="col-lg-4 form-group">
                                <select class="form-control" name="penalty_income_account_id" data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                                </select>
                            </div>
                            <label class="col-lg-3 col-form-label">Income Receivable Account</label>
                            <div class="col-lg-3 form-group">
                                <select class="form-control" name="penalty_receivable_account_id" data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
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