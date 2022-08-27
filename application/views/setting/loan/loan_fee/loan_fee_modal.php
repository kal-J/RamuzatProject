<div class="modal inmodal fade" id="add_loan_fee-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>loan_fee/Create" id="formLoan_fee">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle)) {
                            echo $modalTitle;
                        } else {
                            echo "Add New Loan Fee";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <!-- Start of the modal body -->
                    <input type="hidden" name="id" id="id">
                    <!--input type="hidden" name="tbl" id="tbl" value="tblBranch" -->
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Fee&nbsp;Name<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <input placeholder="" required class="form-control" name="feename" id="feename" type="text">
                        </div>
                        <label class="col-lg-2 col-form-label">Fee&nbsp;Type<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <select id='feetype_id' class="form-control required" name="feetype_id">
                                <option selected>--Select--</option>
                                <?php
                                foreach ($feetypes as $feetype) {
                                    echo "<option value='" . $feetype['feetype_id'] . "'>" . $feetype['feetype'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <!--/row -->

                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label">Amount&nbsp;Calculated&nbsp;As<span class="text-danger">*</span></label>
                        <div class="col-lg-3 form-group">
                            <select data-bind='options: $root.amountCalOptionsOther, optionsText: "amountcalculatedas", optionsCaption: "-- select --",optionsAfterRender: setOptionValue("amountcalculatedas_id"), attr:{name:"amountcalculatedas_id"}, value: amountcalculatedas' class="form-control" style="width: 170px;"> </select>
                        </div>
                        <!-- ko with:amountcalculatedas  -->
                        <label class="col-lg-2 col-form-label" data-bind="visible:  amountcalculatedas !=='Range'">Rate/Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group" data-bind="visible:  amountcalculatedas !=='Range'">
                            <input id="amount" type="number" placeholder="" required class="form-control" name="amount" />
                        </div>
                        <!--/ko -->
                    </div>
                    <!--/row -->
                    <!-- ko with:amountcalculatedas  -->
                    <div class="row col-lg-12" data-bind="visible:  amountcalculatedas ==='Range'">
                        <div class="table-responsive">
                            <table class="table table-striped table-condensed table-hover m-t-md">
                                <thead>
                                    <tr>
                                        <th>Min</th>
                                        <th>Max</th>
                                        <th>Fee type</th>
                                        <th>Rate/Amount</th>
                                        <th>&nbsp;</th>
                                    </tr>
                                </thead>
                                <tbody data-bind='foreach:$root.loan_range_fees'>
                                    <tr>
                                        <td>
                                            <input type="text" data-bind='attr:{name:"rangeFees["+$index()+"][min_range]"},value:min_range' class="form-control" />
                                            <input type="hidden" data-bind='attr:{name:"rangeFees["+$index()+"][id]"},value:id' class="form-control" />
                                        </td>
                                        <td>
                                            <input type="text" data-bind='attr:{name:"rangeFees["+$index()+"][max_range]"},value:max_range' class="form-control" />
                                        </td>
                                        <td>
                                            <select data-bind='options: $root.amountCalOptions, optionsText: function(item){return item.amountcalculatedas},attr:{name:"rangeFees["+$index()+"][calculatedas_id]"}, optionsAfterRender: setOptionValue("amountcalculatedas_id"), optionsCaption: "-- select --",value:calculatedas_id' class="form-control" style="width: 170px;"> </select>
                                        </td>
                                        <td>
                                            <input type="text" data-bind='attr:{name:"rangeFees["+$index()+"][range_amount]"},value:range_amount' class="form-control" />
                                        </td>
                                        <td>
                                            <span title="Remove item" class="btn text-danger" data-bind='click: $root.removeRangeFee'><i class="fa fa-minus"></i></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button data-bind='click: $root.addRangeFee' class="btn-white btn-sm pull-right"><i class="fa fa-plus"></i> Add Another Range</button>

                        </div>
                    </div>
                    <!--/ko -->
                    <br>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Loan fee (Income) A/C</label>
                        <div class="col-lg-4 form-group">
                            <select class="form-control" name="income_account_id" data-bind='options: select2accounts(12), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>

                        <label class="col-lg-3 col-form-label">Loan fee (Income) Receivable A/C</label>
                        <div class="col-lg-3 form-group">
                            <select class="form-control" name="income_receivable_account_id" data-bind='options: select2accounts(1), optionsText: formatAccount2, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id")' style="width: 100%" required data-msg-required="Select an option">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Charge Trigger<span class="text-danger">*</span></label>
                        <div class="col-lg-3 form-group">
                            <select name="chargetrigger_id" data-bind='options: $root.loan_charge_trigger, optionsText: "charge_trigger_name", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"), value: charge_trigger_name' class="form-control"> </select>
                            <div data-bind="with: charge_trigger_name"><span class="help-block-none"><span data-bind="text: charge_trigger_description">Charge description</span></span></div>
                        </div>
                        
                       

                        <?php if(isset($organisation['topup_loan_termination_fees']) && $organisation['topup_loan_termination_fees'] == 1) { ?>

                            <input type="hidden" name="applied_to_id" value="1" />

                        <?php } else { ?>
                            <input type="hidden" name="applied_to_id" value="0" />
                        <?php } ?>
                    </div>
                    <!--/row -->

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