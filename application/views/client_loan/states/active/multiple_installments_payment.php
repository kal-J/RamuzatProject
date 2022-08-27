<div class="modal inmodal fade" id="multiple_installment_payment-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" class="formValidate" action="<?php echo base_url(); ?>loan_installment_payment/multiple_loan_installment_payment" id="formInstallment_payment_multiple">
                <input type="hidden" id="multiple_installment_payment" value="0">


                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Multiple Installments Payment
                        <br>
                        <small>(2 Installments and above)</small>
                    </h4>

                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="state_id" value="10">

                    <div class="form-group row">
                        <input type="hidden" id="call_type" name="call_type" value="<?php echo (isset($case2)) ? $case2 : ''; ?>">
                        <label class="col-lg-2 col-form-label">Loan Ref No.<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select style="width:100%;" name="loan_ref_no" id="loan_ref_no" class="form-control" data-bind='options:  $root.active_loans, optionsText: function(data_item){ return ((data_item.group_name&&data_item.member_name)?(data_item.group_name+" ["+data_item.member_name+"]"):(data_item.group_name&&!data_item.member_name)?(data_item.group_name):(data_item.member_name))+"-"+((!data_item.group_loan_no)?(data_item.loan_no):(data_item.group_loan_no+" ["+data_item.loan_no+"]"));}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("loan_no"), value: $root.active_loan'>
                            </select>
                        </div>
                        <!--ko if: $root.active_loan() && $root.loan_installments() && $root.loan_installments().length < 2 -->
                        <div class="col-lg-6 d-flex justify-content-center my-auto text-small">
                            <p class="text-danger">This Loan has only 1 installment Left. <span style="cursor: pointer;" role="button" class="text-success" >Pay using Single Installment form instead</span></p>
                        </div>
                        <!--/ko -->

                        <!--ko if: $root.active_loan() && $root.loan_installments() && $root.loan_installments().length >= 2   -->

                        <label class="col-lg-2 col-form-label">Date Received <span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <div class="input-group date">
                                <input class="form-control" data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" onkeydown="return false" autocomplete="off" data-bind="datepicker: $root.installment_payment_date, value:$root.installment_payment_date" required name="payment_date" type="text"><span data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" data-bind="datepicker: $root.installment_payment_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>

                        <!--ko if: $root.active_loan() -->
                        <input data-bind="value: $root.active_loan().loan_product_id" type="hidden" name="loan_product_id">
                        <input data-bind="value: $root.active_loan().member_name" type="hidden" name="member_name">
                        <input type="hidden" name="member_id" data-bind="value: $root.active_loan().member_id">
                        <!--/ko -->

                        <!--ko if: $root.active_loan()  && $root.penalty_amount() -->

                        <div class="col-lg-12 my-1"></div>

                        <label class="col-lg-2 col-form-label">Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <input id="money-paid_total" class="form-control money-format" autocomplete="off">

                            <input id="numeric-paid_total" type="hidden" class="form-control do-not-ignore" autocomplete="off" data-bind="textInput: $root.paid_total, attr: {'data-rule-min': $root.multiple_installment_min(), 'data-msg-min': 'Amount must be greater than ' +  curr_format($root.multiple_installment_min()) } ">
                            <p class="blueText">
                                <span data-bind="">Min:</span>
                                <span data-bind="text: curr_format($root.multiple_installment_min())"></span>&nbsp;
                                <span>Total Demanded:</span>&nbsp;
                                <span class="text-danger" data-bind="text: curr_format( round($root.total_demanded_amount() + $root.overall_penalty() , 2) )"></span>&nbsp;
                                <span>Principal:</span>&nbsp;
                                <span class="text-danger" data-bind="text: curr_format(round(parseFloat($root.total_demanded_principal()),2))"></span>&nbsp;
                                <span>Interest:</span>&nbsp;
                                <span class="text-danger" data-bind="text: curr_format(round(parseFloat($root.total_demanded_interest()),2))"></span>
                                <span>Penalty:</span>&nbsp;
                                <span class="text-danger" data-bind="text: curr_format( round( parseFloat($root.overall_penalty()),2))"></span>

                            </p>
                             <!-- <div style=" font-size: 0.9em; font-weight: bold; text-align: center;"
                                                    class="text-danger"
                                                    data-bind="text: (typeof $root.penalty_amount() !== 'undefined')?$root.penalty_amount().message:''">
                                                </div> -->
                        </div>
                        

                        <div class="col-lg-6"></div>

                        <label class="col-lg-2 col-form-label text-success">Forgive Penalty?<span class="text-danger">*</span></label>

                        <div class="col-lg-4 form-group mt-2">
                            <label> <input value="0" name="forgive_penalty" type="radio" data-bind="checked: $root.forgive_penalty"> No </label>
                            <label> <input value="1" type="radio" name="forgive_penalty" data-bind="checked: $root.forgive_penalty"> Yes</label>
                        </div>

                        <label class="col-lg-3 col-form-label text-success"> Collect Interest not due? <span class="text-danger">*</span></label>

                        <div class="col-lg-3 form-group mt-2">
                            <label> <input value="0" name="with_interest" type="radio" data-bind="checked: with_interest"> No </label>
                            <label> <input value="1" type="radio" name="with_interest" data-bind="checked: with_interest"> Yes</label>
                        </div>


                        <div class="col-lg-12 my-1"></div>

                        <label class="col-lg-2 col-form-label">Payment Method <span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: $root.payment_mode, attr:{name:"payment_id"}' class="form-control" required style="width: 100%;"> </select>
                        </div>


                        <input required data-bind="value: $root.active_loan().id" name="client_loan_id" type="hidden">
                        <input required data-bind="value: $root.active_loan().loan_no" name="loan_no" type="hidden">
                        <!-- <input required data-bind="value: repayment_schedule_id" name="repayment_schedule_id" id="repayment_schedule_id" type="hidden"> -->



                        <!-- ko with: $root.payment_mode -->
                        <label data-bind="visible: id !=5" class="col-lg-2 col-form-label">Transaction Channel <span class="text-danger">*</span></label>
                        <div class="col-lg-4" data-bind="visible: id !=5">
                            <select class="form-control" required data-bind='options: $root.transaction_channel, optionsText:function(data_item){return data_item.channel_name}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"transaction_channel_id"}, value:$root.trans_channel' data-msg-required="Transaction channel must be selected" style="width: 100%">
                            </select>
                        </div>

                        <label data-bind="visible: id ==5" class="col-lg-2 col-form-label">Savings Account<span class="text-danger">*</span></label>
                        <div class="col-lg-4" data-bind="visible: id ==5">
                            <select class="form-control" required data-bind='
                                    options: $root.filtered_savingac, 
                                    optionsText: function(data_item){return data_item.account_no + " | " + data_item.member_name}, 
                                    optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),attr:{name:"savings_account_id"}' style="width: 100%">
                            </select>
                        </div>
                        <div class="col-lg-6"></div>


                        <div class="col-lg-12 my-2"></div>
                        <label class="col-lg-2 col-form-label">Narrative</label>
                        <div class="col-lg-4">
                            <textarea required rows="3" class="form-control" id="comment" name="comment"></textarea>
                        </div>

                        <div class="col-lg-6 pull-right">
                            <fieldset>
                                <legend style=" text-align: right;"> Received Amount</legend>
                                <input class="form-control" data-bind="value: $root.paid_total() ? $root.paid_total() : 0" name="paid_total" type="text" required hidden>
                                <h2 class="pull-right" data-bind="text: $root.paid_total() ? curr_format($root.paid_total()) : 0"></h2>
                            </fieldset>
                        </div>
                        <!-- payment mode -->
                        <!--/ko-->


                        <!-- active loan -->
                        <!--/ko -->



                        <!--/ko -->

                        <!-- End of installments number check -->

                    </div>




                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Save";
                        }
                        ?>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>