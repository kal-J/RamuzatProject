<style type="text/css">
    .blueText {
        color: blue;
        font-size: 10px;
    }
</style>
<!--div class="modal inmodal fade" id="add_savings_account" tabindex="-1" role="dialog" aria-hidden="true" commented out to allow the search functionality of the select2-->
<div class="modal inmodal fade" id="add_fixed_amount" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" class="formValidate" name="formFixed_savings"
                  action="<?php echo base_url(); ?>Fixed_savings/set" id="formFixed_savings">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                                class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Fix Savings Account
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span
                                class="text-danger">*</span></small>
                </div>
                <div class="modal-body">

                    <div class="form-group row">
                        <fieldset class="col-lg-12">
                            <legend>Savings Account info</legend>
                            <div class="col-lg-12 row no-gutter">

                                <div class="form-group col-lg-8">

                                    <span class="text-muted">Enter Amount to fix</span>
                                    <select class="form-control" id="selectBox" name="type" required
                                            onchange="changeFunc()">
                                        <option value="">select</option>
                                        <option value="1">Savings Account</option>
                                        <option value="0">Fix Amount</option>
                                    </select>
                                    <div class="form-group">
                                        <input type="hidden" name="">
                                    </div>


                                    <div class="form-group row" style="display: none" id="textboxes">
                                        <label class="col-lg-2 col-form-label"><span class="text-danger">*</span>Qualifying
                                            Amount</label>
                                        <div class="col-lg-10">
                                            <input type="text" class="form-control" id="qualifying_amount"
                                                   name="qualifying_amount" required/>
                                        </div>

                                    </div>
                                </div>
                                <div class="col-lg-4 border-left" data-bind="with: selected_account">
                                    <input type="hidden" name="savings_account_id" data-bind="value:id">
                                    <input type="hidden" name="id" data-bind="value:id">
                                    <div style="text-align: center;">
                                        <span class="text-muted">Amount available (Savings)</span>
                                        <?php if (!isset($selected_account)) { ?>
                                            <!-- ko with: accountw -->
                                            <h2 data-bind="text: (parseInt(bal_cal_method_id)===parseInt(1))?curr_format(parseFloat(cash_bal)- ((parseFloat(cash_bal)*parseFloat(min_balance))/100) ): curr_format(parseFloat(cash_bal)-parseFloat(min_balance))"></h2>
                                            <!--/ko-->
                                        <?php } else { ?>
                                            <h2 data-bind="text: (parseInt(bal_cal_method_id)===parseInt(1))?curr_format(parseFloat(cash_bal)- ((parseFloat(cash_bal)*parseFloat(min_balance))/100) ): curr_format(parseFloat(cash_bal)-parseFloat(min_balance))">
                                            </h2>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="">

                        <div class="form-group row">
                            <label class="col-lg-2 col-form-label">Start Date <span class="text-danger">*</span></label>
                            <div class="form-group col-lg-4">
                                <div class="input-group date"
                                     >
                                    <input autocomplete="off" placeholder="DD-MM-YYYY" type="text" class="form-control"
                                           onkeydown="return false" name="start_date" required/>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                            <label class="col-lg-2 col-form-label">End Date <span class="text-danger">*</span></label>
                            <div class="form-group col-lg-4">
                                <div class="input-group date">
                                    <input autocomplete="off" placeholder="DD-MM-YYYY" type="text" class="form-control"
                                           onkeydown="return false" name="end_date" required/>
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Fix Savings";
                        }
                        ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
