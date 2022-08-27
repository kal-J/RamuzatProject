<style type="text/css">
    .blueText {
        color: blue;
        font-size: 10px;
    }
</style>
<div class="modal inmodal fade" id="add_locked_amount" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form method="post" class="formValidate" enctype="multipart/form-data" action="<?php echo base_url(); ?>Lock_savings/Create" id="formLock_savings">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Lock Savings
                    </h4>


                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="id">
                    <!--withdraw-->
                    <div class="form-group row">
                        <fieldset class="col-lg-12">
                            <legend>Savings Account info</legend>
                            <div class="col-lg-12 row no-gutter" data-bind="with: selected_account">
                                <input type="hidden" name="saving_account_id" data-bind="value:id">

                                <div class="col-lg-12">
                                    <center>
                                        <span class="text-muted">Amount available (Savings)</span>
                                        <?php if (!isset($selected_account)) { ?>
                                            <!-- ko with: accountw -->
                                            <h2 data-bind="text: (parseInt(bal_cal_method_id)===parseInt(1))?curr_format(parseFloat(cash_bal)- ((parseFloat(cash_bal)*parseFloat(min_balance))/100) ): curr_format(parseFloat(cash_bal)-parseFloat(min_balance))"></h2>
                                            <!--/ko-->
                                        <?php } else { ?>
                                            <h2 data-bind="text: (parseInt(bal_cal_method_id)===parseInt(1))?curr_format(parseFloat(cash_bal)- ((parseFloat(cash_bal)*parseFloat(min_balance))/100) ): curr_format(parseFloat(cash_bal)-parseFloat(min_balance))">
                                            </h2>
                                        <?php } ?>
                                    </center>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Lock as </label>
                        <div class="col-lg-10">
                            <select data-bind='options: $root.amountCalOptions, optionsText: function(item){return item.amountcalculatedas},attr:{name:"amountcalculatedas_id"}, optionsAfterRender: setOptionValue("amountcalculatedas_id"), optionsCaption: "-- select --",value:amountcalculatedas' class="form-control">
                            </select>
                        </div>
                        <!--ko with: amountcalculatedas -->
                        
                            <!--ko if: parseInt(amountcalculatedas_id) ==2-->
                            <div class="col-lg-12 my-2"></div>
                            <label class="col-lg-2 col-form-label">Amount</label>
                            <div class="col-lg-10">
                                <div>
                                    


                                            <!-- ko with: $root.accountw() -->
                                            <input data-bind="attr : {
                                                'data-rule-max': (parseInt($root.accountw().bal_cal_method_id)===parseInt(1))? (parseFloat($root.accountw().cash_bal)- ((parseFloat($root.accountw().cash_bal)*parseFloat($root.accountw().min_balance))/100) ): (parseFloat($root.accountw().cash_bal)-parseFloat($root.accountw().min_balance)),
                                                'data-msg-max': 'Amount can not be greater than available balance'}" class="form-control" min="1" id="amount" name="amount" type="number" required>
                                            <!--/ko-->
                                        
                                </div>
                            </div>
                            <!--/ko -->

                            <!--ko if: parseInt(amountcalculatedas_id) ==1 -->
                            <div class="col-lg-12 my-2"></div>
                            <label class="col-lg-2 col-form-label">Percentage</label>
                            <div class="col-lg-10">
                                <input class="form-control" min="0" max="100" type="number" name="percentage" required>
                            </div>
                            <!--/ko -->

                        <!--/ko-->
                    </div>

                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Date<span class="text-danger">*</span></label>
                        <div class="form-group col-lg-10">
                            <div class="input-group date" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>">
                                <input type="text" class="form-control" onkeydown="return false" name="locked_date" value="<?php echo mdate("%d-%m-%Y"); ?>" data-bind="datepicker: $root.transaction_date_wi" required />
                                <span data-bind="datepicker: $root.transaction_date_wi" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <label class="col-lg-2 col-form-label"></label>
                        <div class="col-lg-10" data-bind="with: selected_account">
                        <!-- <input name="available_balance" > -->
                        <?php if (!isset($selected_account)) { ?>
                                            <!-- ko with: accountw -->
                                            <input type="hidden" name="available_balance" data-bind="value: (parseInt(bal_cal_method_id)===parseInt(1))?parseFloat(cash_bal)- ((parseFloat(cash_bal)*parseFloat(min_balance))/100) : parseFloat(cash_bal)-parseFloat(min_balance)">
                                            <!--/ko-->
                                        <?php } else { ?>
                                            <input type="hidden" name="available_balance" data-bind="value: (parseInt(bal_cal_method_id)===parseInt(1))?parseFloat(cash_bal)- ((parseFloat(cash_bal)*parseFloat(min_balance))/100) : parseFloat(cash_bal)-parseFloat(min_balance)">
                                           
                                        <?php } ?>
                        
                                        
                            
                        </div>

                        <label class="col-lg-2 col-form-label">Narrative</label>
                        <div class="col-lg-10">
                            <textarea placeholder="" required class="form-control" id="narrative" name="narrative"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <!--ko if:(parseInt($root.account_balance()>0)-->
                    <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save";
                        }
                        ?>
                    </button>
                    <!--/ko-->
                </div>
            </form>
        </div>
    </div>
</div>