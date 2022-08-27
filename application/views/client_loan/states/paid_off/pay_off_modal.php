<div class="modal inmodal fade" id="pay_off-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form method="post" class="formValidate" action="<?php echo base_url(); ?>loan_installment_payment/pay_off" id="formPay_off">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        <?php
                        if (isset($modalTitle1)) {
                            echo $modalTitle;
                        } else {
                            echo "Paying off Loan";
                        }
                        ?></h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <!-- Start of the modal body -->
                    <!-- ko with: loan_details -->
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Client</label>
                        <div class="col-lg-4">
                            <span class="form-control" data-bind="text: member_name"></span>
                        </div>
                        <label class="col-lg-2 col-form-label">Loan Ref No.</label>
                        <div class="col-lg-4">
                            <input type="hidden" name="loan_ref_no" data-bind="attr:{value: loan_no}">
                            <input type="hidden" name="member_id" data-bind="attr:{value: member_id}">
                            <span class="form-control" data-bind="text: loan_no"></span>
                        </div>
                    </div>
                    <!--/ko-->
                    <div class="form-group row">
                        <!-- ko with: loan_details -->

                        <label class="col-lg-2 col-form-label">Date of Paying<span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <div class="input-group date">
                                <input data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" onkeydown="return false" autocomplete="off" data-bind="datepicker: $root.pay_off_action_date" required class="form-control" onkeydown="return false" autocomplete="off" name="payment_date" type="text"><span data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>" data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>" data-bind="datepicker: $root.pay_off_action_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                        <div class="col-lg-6 text-center">
                            <span class="d-flex align-items-center my-1">
                                Forgive Penalty? &nbsp;&nbsp;<input type="checkbox" data-bind="checked: $root.forgive_payoff_penalty">
                            </span>
                        </div>

                        <label class="col-lg-2 col-form-label">Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-4" data-bind="with: $root.pay_off_data">
                            <input id="payoff_paid_total" class="form-control" autocomplete="off" type="number" name="paid_total" required data-bind="attr: {'data-rule-max': $root.forgive_payoff_penalty() ? $root.forgive_payoff_penalty_max() : $root.pay_off_max(),

                            'data-msg-max': 'Amount is greater than ' + $root.forgive_payoff_penalty() ? curr_format($root.forgive_payoff_penalty_max()) : curr_format($root.pay_off_max()),
                            
                            'data-rule-min': $root.forgive_payoff_penalty() ? $root.forgive_payoff_penalty_min() : $root.pay_off_min(),
                            'data-msg-min':'Amount is less than ' +  $root.forgive_payoff_penalty() ? curr_format($root.forgive_payoff_penalty_min()) : curr_format($root.pay_off_min())}">
                            <p class="blueText">
                                <span data-bind="">Min: </span>
                                <span data-bind="text: $root.forgive_payoff_penalty() ? curr_format($root.forgive_payoff_penalty_min()) : curr_format($root.pay_off_min())"></span>&nbsp;
                                <span data-bind=''>Max: </span>
                                <span data-bind="text:  $root.forgive_payoff_penalty() ? curr_format($root.forgive_payoff_penalty_max()) : curr_format($root.pay_off_max())"></span>
                            </p>
                        </div>
                        <input required data-bind="value: loan_no" name="loan_no" type="hidden">

                        <!--/ko-->

                    </div>
                    <div class="row form-group">
                        <label class="col-lg-2 col-form-label">Payment Method <span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: payment_mode, attr:{name:"payment_id"}' class="form-control" required style="width: 100%;"> </select>
                        </div>

                        <!-- ko with: payment_mode -->
                        <label data-bind="visible: id ==5" class="col-lg-2 col-form-label">Savings Account<span class="text-danger">*</span></label>
                        <div class="col-lg-4" data-bind="visible: id ==5">
                            <select class="form-control" data-bind='
                                options: $root.member_savings_accounts, 
                                optionsText: function(data_item){return data_item.account_no + " | " + data_item.member_name}, 
                                optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),attr:{name:"savings_account_id"},
                                value: $root.selected_ac' style="width: 100%">
                            </select>
                        </div>
                        <label data-bind="visible: id !=5" class="col-lg-2 col-form-label">Transaction channel<span class="text-danger">*</span></label>
                        <div data-bind="visible: id !=5" class="form-group col-lg-4">
                            <select class="form-control" id="transaction_channel_id" name="transaction_channel_id" data-bind='options: $root.transaction_channel, optionsText: "channel_name", optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), value:$root.tchannels' data-msg-required="Transaction must be selected" style="width: 100%" required>
                            </select>
                        </div>

                        <!--/ko-->
                    </div>

                    <!-- ko with: loan_details -->
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Comment<span class="text-danger">*</span></label>

                        <div class="col-lg-4 form-group">
                            <textarea class="form-control" rows="3" name="comment" id="comment" required></textarea>
                        </div>
                    </div>
                    <!--/row -->
                    <input type="hidden" name="state_id" value="9">
                    <input type="hidden" name="client_loan_id" data-bind="attr:{value: id}" id="client_loan_id">
                    <input type="hidden" name="group_loan_id" data-bind="attr:{value: (typeof group_loan_id !='undefined')?group_loan_id:''}" id="group_loan_id">
                    <!--/ko--> 
                    
                        <!-- ko with: pay_off_data -->

                        <fieldset class="col-lg-12">
                            <legend class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center;">
                                Pay Off Fees</legend>
                            <table class="table table-striped table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th>Fee</th>
                                        <th>Type</th>
                                        <th>Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody data-bind='foreach: $root.available_loan_fees'>
                                    <tr data-bind="if:parseInt(chargetrigger_id)==7">

                                        <td>
                                            <span data-bind="text:feename"></span>
                                        </td>
                                        <td>
                                            <span data-bind="text:feetype"></span>
                                        </td>
                                        <td>
                                            <label data-bind="if: parseInt(amountcalculatedas_id) == 2">
                                                <span data-bind="text: amount"></span>
                                            </label>

                                            <label data-bind="if: parseInt(amountcalculatedas_id) == 1">
                                                <span data-bind="text: Math.ceil((amount/100) * (parseInt($root.pay_off_data().total_interest_sum) - parseInt($root.pay_off_data().to_date_interest_sum)) / 100) * 100">
                                                </span>
                                            </label>
                                            <input name="pay_off_charge" type="hidden" data-bind="Math.ceil((amount/100) * (parseInt($root.pay_off_data().total_interest_sum) - parseInt($root.pay_off_data().to_date_interest_sum)) / 100) * 100">

                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][amount]"}, value: Math.ceil((amount/100) * (parseInt($root.pay_off_data().total_interest_sum) - parseInt($root.pay_off_data().to_date_interest_sum)) / 100) * 100' />

                                            <input type="hidden" data-bind='attr:{name:"loanFees["+$index()+"][loan_product_fee_id]"}, value: id' />

                                            <input type="hidden" value="0" data-bind='attr:{name:"loanFees["+$index()+"][paid_or_not]"}' />
                                        </td>

                                        <td>
                                            <div data-bind="if:parseInt(feetype_id)===1">
                                                <input type="hidden" value="on" data-bind='attr:{name:"loanFees["+$index()+"][remove_or_not]"}' />
                                            </div>
                                            <div data-bind="if:parseInt(feetype_id)===2">
                                                <input type="checkbox" checked="checked" data-bind='attr:{name:"loanFees["+$index()+"][remove_or_not]"}' />
                                            </div>
                                        </td>

                                    </tr>
                                </tbody>
                            </table>
                        </fieldset>

                        <!--/ko-->


                    <!-- ko with: pay_off_data -->
                    <div class="row">
                        <div class="col-lg-7">
                            <fieldset class="">
                                <legend style=" text-align: right;"> Loan Info</legend>
                                <table class='table table-hover'>
                                    <thead>
                                        <tr>
                                            <th class="border-right">#</th>
                                            <th>Particular</th>
                                            <th>Amount(UGX)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border-right">1</td>
                                            <td>
                                                <span class="input-xs">Disbursed Amount</span>
                                            </td>
                                            <td data-bind="text:curr_format(($root.loan_details().amount_approved)*1)"></td>
                                        </tr>
                                        <tr>
                                            <td class="border-right">2</td>
                                            <td>
                                                <span class="input-xs">Intrest Payable <small>(Total bal)</small></span>
                                            </td>
                                            <input required min="0" step="0.01" type="hidden" name="paid_interest" data-bind="value: ((parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount)) >0)?parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount):0">

                                            <input required min="0" step="0.01" type="hidden" name="expected_interest" data-bind="value: ((parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount)) >0)?parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount):0">
                                            <td data-bind="text: ((parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount)) >0)?curr_format((parseFloat(to_date_interest_sum)-parseFloat(already_interest_amount))*1):'0'"></td>
                                        </tr>
                                        <tr>
                                            <td class="border-right">3</td>
                                            <td>
                                                <span class="input-xs">Principal Payable <small>(Total bal)</small></span>
                                            </td>
                                            <input required min="0" step="0.000001" type="hidden" name="paid_principal" data-bind="value: parseFloat(principal_sum)-parseFloat(already_principal_amount)">
                                            <input required min="0" step="0.000001" type="hidden" name="expected_principal" data-bind="value: parseFloat(principal_sum)-parseFloat(already_principal_amount)">
                                            <td data-bind="text: curr_format((parseFloat(principal_sum)-parseFloat(already_principal_amount))*1)"></td>
                                        </tr>
                                        <tr>
                                        <tr>
                                            <td class="border-right">4</td>
                                            <td>
                                                <span class="input-xs">Paid Amount <small>(Principal & Interest)</small></span>
                                            </td>
                                            <td data-bind="text: curr_format((already_paid_sum)*1)"></td>
                                        </tr>
                                        <tr>
                                            <td class="border-right">5</td>
                                            <td>
                                                <span class="input-xs">Charges</span>
                                            </td>
                                            <td data-bind="text: (penalty_value)?curr_format(parseFloat(penalty_value)):'No charge'"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td><span class="pull-right ">Total Loan Amount bal <small>Payable</small> </span>

                                            </td>
                                            <input data-bind="value: (penalty_value)?round( ((parseFloat(penalty_value)+parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2) : round( ((parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2)" type="hidden" name="expected_total" required>
                                            <th data-bind="text: (penalty_value)? curr_format(round( ((parseFloat(penalty_value)+parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2)) :
                                                    curr_format(round( ((parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1),2))">

                                            </th>
                                            <input type="hidden" name="un_paid_interest" data-bind="value: round((parseFloat(total_interest_sum)-parseFloat(to_date_interest_sum)),2)">
                                        </tr>
                                    </tfoot>
                                </table>
                            </fieldset>
                        </div>
                        <div class="col-lg-5">
                            <fieldset>
                                <legend style="min-width:250px;">Charges</legend>
                                <table class='table table-hover' width="100%">
                                    <thead>
                                        <tr>
                                            <th class="border-right">#</th>
                                            <th>Name</th>
                                            <th>Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border-right">1</td>
                                            <td>
                                                <span class="input-xs">Penalty</span>
                                                <input min="0" step="0.000001" type="hidden" name="paid_penalty" data-bind="value:  $root.forgive_payoff_penalty() ? 0 : penalty_value">
                                            </td>
                                            <td data-bind="text: (penalty_value)?curr_format(parseFloat(penalty_value)):'No penalty charged'"></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td><span class="pull-right ">Total Charges :</span></td>
                                            <th data-bind="text: (penalty_value)?curr_format(parseFloat(penalty_value)):'No charge'"></th>
                                        </tr>
                                        <tr>
                                            <td style=" font-size: 0.9em; font-weight: bold; text-align: center;" class="text-danger" colspan="3" data-bind="text: penalty_message">
                                            </td>
                                            <input type="hidden" data-bind="value: round(((to_date_interest_sum+principal_sum)*1)-((already_paid_sum)*1),2)" name="expected_total">
                                        </tr>
                                    </tfoot>
                                </table>
                            </fieldset>
                        </div>
                    </div>
                    <!--/ko-->

                </div><!-- End of the modal body -->
                <div class="modal-footer">
                    <!-- start of the modal footer -->
                    <button id="btn-submit" type="submit" class="btn btn-success btn-sm save_data">
                        <i class="fa fa-check"></i>
                        <?php
                        if (isset($saveButton)) {
                            echo $saveButton;
                        } else {
                            echo "Pay off";
                        }
                        ?>
                    </button>
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                </div><!-- End of the modal footer -->
            </form>
        </div>
    </div>
</div>