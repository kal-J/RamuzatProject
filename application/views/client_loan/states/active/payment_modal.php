<div class="modal inmodal fade" id="installment_payment-modal" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="post" enctype="multipart/form-data" class="formValidate"
                action="<?php echo base_url(); ?>loan_installment_payment/single_loan_installment_payment"
                id="formInstallment_payment">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span
                            class="sr-only">Close</span></button>
                    <h4 class="modal-title">
                        Installment Payment
                    </h4>
                    <small class="font-bold">Note: Required fields are marked with <span
                            class="text-danger">*</span></small>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="state_id" value="10">
                    <div class="form-group row">
                        <input type="hidden" id="call_type" name="call_type"
                            value="<?php echo (isset($case2)) ? $case2 : ''; ?>">
                        <label class="col-lg-2 col-form-label">Loan Ref No.<span class="text-danger">*</span></label>
                        <div class="col-lg-6">
                            <select style="width:100%;" name="loan_ref_no" id="loan_ref_no" class="form-control"
                                data-bind='options: $root.active_loans, optionsText: function(data_item){ return ((data_item.group_name&&data_item.member_name)?(data_item.group_name+" ["+data_item.member_name+"]"):(data_item.group_name&&!data_item.member_name)?(data_item.group_name):(data_item.member_name))+"-"+((!data_item.group_loan_no)?(data_item.loan_no):(data_item.group_loan_no+" ["+data_item.loan_no+"]"));}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("loan_no"), value: $root.active_loan'>
                            </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Installment #<span class="text-danger">*</span></label>
                        <div class="col-lg-2">
                            <div class="input-group">

                                <!-- multipleselect2_demo_2 -->
                                <select style="width:100%;" id="installment_number" class=" form-control"
                                    aria-hidden="true" data-bind='selectedOptions: $root.selected_installment, optionsAfterRender: setOptionValue("installment_number"), value: $root.loan_installment, 
                                '>
                                    <option value="">Select</option>
                                    <!-- ko foreach: $root.loan_installments().filter((v,i) => i === 0) -->
                                    <option data-bind="value: $data, text: $data.installment_number"></option>
                                    <!-- /ko -->
                                    <!-- ko foreach: $root.loan_installments().filter((v,i) => i > 0) -->
                                    <option disabled data-bind="value: $data, text: $data.installment_number"></option>
                                    <!-- /ko -->
                                </select>
                                <!-- ko if: $root.loan_installment -->
                                <input name="installment_number" type="hidden"
                                    data-bind="value: $root.loan_installment().installment_number">
                                <!-- /ko -->

                            </div>
                        </div>
                    </div>

                    <!--ko if: $root.active_loan() -->
                    <input data-bind="value: $root.active_loan().loan_product_id" type="hidden" name="loan_product_id">
                    <input data-bind="value: $root.active_loan().member_name" type="hidden" name="member_name">
                    <!--/ko -->



                    <!-- ko if:( (typeof $root.payment_data() !=='undefined') && $root.loan_installment) -->
                    <!-- ko with: payment_data -->
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Payment Method <span class="text-danger">*</span></label>
                        <div class="col-lg-4">
                            <select
                                data-bind='options: $root.payment_modes, optionsText: "payment_mode", optionsCaption: "-- select --" ,optionsAfterRender: setOptionValue("id"),value: $root.payment_mode, attr:{name:"payment_id"}'
                                class="form-control" required style="width: 100%;"> </select>
                        </div>
                        <label class="col-lg-2 col-form-label">Date Received <span class="text-danger">*</span></label>
                        <div class="col-lg-4 form-group">
                            <div class="input-group date">
                                <input class="form-control"
                                    data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>"
                                    data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>"
                                    onkeydown="return false" autocomplete="off"
                                    data-bind="datepicker: $root.installment_payment_date" required name="payment_date"
                                    type="text"><span
                                    data-date-start-date="<?php echo isset($active_month) ? date('d-m-Y', strtotime($active_month['month_start'])) : date('d-m-Y', strtotime($fiscal_active['start_date'])); ?>"
                                    data-date-end-date="<?php echo isset($active_month) ? ((strtotime(date('d-m-Y')) < (strtotime($active_month['month_end']))) ? date('d-m-Y') : date('d-m-Y', strtotime($active_month['month_end']))) : ((strtotime(date('d-m-Y')) < (strtotime($fiscal_active['end_date']))) ? date('d-m-Y') : date('d-m-Y', strtotime($fiscal_active['end_date']))); ?>"
                                    data-bind="datepicker: $root.installment_payment_date" class="input-group-addon"><i
                                        class="fa fa-calendar"></i></span>
                            </div>
                        </div>
                    </div>
                    <input required data-bind="value: id" name="client_loan_id" id="client_loan_id" type="hidden">
                    <input required data-bind="value: loan_no" name="loan_no" id="client_loan_id" type="hidden">
                     <input type="hidden" name="member_id" data-bind="attr:{value: $root.member_id}">
                    <input required data-bind="value: repayment_schedule_id" name="repayment_schedule_id"
                        id="repayment_schedule_id" type="hidden">
                    <input required data-bind="value: moment(repayment_date,'YYYY-MM-DD').format('DD-MM-YYYY')"
                        name="repayment_date" id="repayment_date" type="hidden">
                    <!-- ko if: $root.installment_payment_date() -->

                    <div class="col-lg-12 form-group row mb-5">
                        <div class="col-lg-12">
                            <fieldset class="col-lg-12">
                                <legend style=" text-align: right;">Installment Details</legend>
                                <table class='table table-hover'>
                                    <thead>
                                        <tr>
                                            <th class="border-right">#</th>
                                            <th>Principal Payable</th>
                                            <th>Intrest Payable</th>
                                            <th>Penalty</th>
                                            <th>Total Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="border-right"></td>
                                            <td>
                                                <span class="input-xs"
                                                    data-bind="text: curr_format( round(parseFloat(remaining_principal) , 2) )"></span>
                                                <input required min="0" step="0.000001" type="hidden"
                                                    name="expected_principal"
                                                    data-bind="value: round(parseFloat(remaining_principal) , 2)">
                                            </td>
                                            <td>
                                                <span class="input-xs"
                                                    data-bind="text: curr_format( round(parseFloat(remaining_interest) , 2) )"></span>
                                                <input required min="0" step="0.01" type="hidden"
                                                    name="expected_interest"
                                                    data-bind="value: round(parseFloat(remaining_interest) , 2)">
                                            </td>
                                            <td>
                                                <input required min="0" step="0.01" type="hidden"
                                                    name="expected_penalty"
                                                    data-bind="textInput: $root.single_installment_total_penalty">
                                                <span class="input-xs"
                                                    data-bind="text: curr_format($root.single_installment_total_penalty())"></span>
                                                <input name="prev_demanded_penalty" type="hidden" data-bind="textInput: round(($root.loan_installment() ? (
                                                    parseFloat($root.loan_installment().demanded_penalty)
                                                ) : 0) , 2)">
                                                <input name="prev_payment_status" type="hidden" data-bind="textInput: $root.loan_installment() ? $root.loan_installment().payment_status : '' ">
                                                <input name="prev_payment_date" type="hidden" data-bind="textInput: $root.loan_installment() ? $root.loan_installment().actual_payment_date : '' ">
                                            </td>
                                            <td>
                                                <span class="input-xs"
                                                    data-bind="text: curr_format( round($root.expected_total() , 2) )">
                                                </span>
                                                <input min="0" step="0.000001" type="hidden" name="expected_total"
                                                    data-bind="textInput: $root.expected_total() ">
                                            </td>

                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td></td>
                                            <td style=" font-size: 0.9em; font-weight: bold; text-align: center;"
                                                colspan="2" class="text-danger"
                                                data-bind="text: 'Installment Number: '+installment_number"></td>
                                            <td colspan="3"
                                                style=" font-size: 0.9em; font-weight: bold; text-align: center;"
                                                class="text-danger"
                                                data-bind="text: 'Due date: '+moment(repayment_date,'YYYY-MM-DD').format('DD-MMM-YYYY')">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="5">
                                                <div style=" font-size: 0.9em; font-weight: bold; text-align: center;"
                                                    class="text-danger"
                                                    data-bind="text: (typeof $root.penalty_amount() !== 'undefined')?$root.penalty_amount().message:''">
                                                </div>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </fieldset>
                        </div>
                    </div>

                    <!-- ko with: $root.payment_mode -->


                    <div class="form-group row">
                        <?php if (!empty($org['loan_curtailment']) && $org['loan_curtailment'] == 1) { ?>
                        <!-- ko if: $root.loan_installments().length > 1  -->

                        <label class="col-lg-3 col-form-label">
                            Do Loan Curtailment ? <span class="text-danger">*</span>
                        </label>
                        <div class="col-lg-2 form-group mt-2">
                            <label> <input value="0" name="curtail_loan" type="radio"
                                    data-bind="checked: $root.curtail_loan" required="required"> No </label>
                            <label> <input value="1" type="radio" name="curtail_loan"
                                    data-bind="checked: $root.curtail_loan" required="required"> Yes</label>
                        </div>

                        <!-- ko ifnot: $root.curtail_loan() === '1' -->
                        <div class="col-lg-7"></div>
                        <!-- /ko -->

                        <!-- ko if: $root.curtail_loan() === '1' -->
                        <label class="col-lg-2 col-form-label">
                            Installments<span class="text-danger">*</span>
                        </label>

                        <div class="col-lg-4">
                            <input required id="curtail_installments" name="curtail_installments" min="1" type="number"
                                class="form-control"
                                data-bind="attr: { 'data-rule-max': $root.loan_installments().filter((v,i) => i > 0).length , 'data-msg-max': 'Installments for curtailment cannot be greater than ' + $root.loan_installments().filter((v,i) => i > 0).length } ">
                            <p class="blueText">
                                <span>Min: </span>
                                <span>1</span>&nbsp;
                                <span>Max (remaining installments): </span>
                                <span data-bind="text: $root.loan_installments().filter((v,i) => i > 0).length "></span>

                            </p>

                        </div>
                        <div class="col-lg-1"></div>
                        <!-- /ko -->

                        <!-- /ko -->

                        <?php } ?>


                        <label class="col-lg-2 col-form-label">Total Amount<span class="text-danger">*</span></label>
                        <div class="col-lg-4">

                            <input id="money-totalAmount" class="form-control money-format" autocomplete="off">
                            <input type="hidden" id="numeric-totalAmount" data-bind="textInput: $root.totalAmount">

                            <input class="form-control do-not-ignore" autocomplete="off" type="hidden" id="totalAmount"
                                name="totalAmount"
                                data-bind="textInput: $root.totalAmount, valueUpdate:'afterkeydown',  attr: {'data-rule-min':parseInt(0), 'data-msg-min':'Amount is less than 0', 'data-rule-max': $root.payment_with_savings_max() ? round($root.payment_with_savings_max() , 2) : ($root.next_payment_data() ? $root.max_total_amount_single_installment() : false), 'data-msg-max':'Amount is greater than ' + ($root.payment_with_savings_max() ? (curr_format( $root.payment_with_savings_max() )) : curr_format($root.max_total_amount_single_installment())) }">
                            <p class="blueText">
                                <span data-bind="">Min: </span>
                                <span data-bind="text: curr_format(parseInt(0))"></span>&nbsp;
                                <!--ko if: $root.next_payment_data() && $root.curtail_loan() == '0' -->
                                <span data-bind=''>Max: </span>
                                <span
                                    data-bind="text:  ($root.payment_with_savings_max() ? (curr_format( round($root.payment_with_savings_max() , 2) )) : curr_format($root.max_total_amount_single_installment()))"></span>
                                &nbsp;&nbsp;
                                <!--/ko -->
                                <span style="font-size: 1.2em;" class="text-danger">Demanded principle:</span>
                                <span style="font-size: 1.2em;" class="text-dark"
                                    data-bind="text: $root.payment_data() ? curr_format( round(parseFloat($root.payment_data().remaining_principal) , 2) ) : 0">:</span>&nbsp;&nbsp;
                                <span style="font-size: 1.2em;" class="text-danger">Demanded Interest:</span>
                                <span style="font-size: 1.2em;" class="text-dark"
                                    data-bind="text: $root.payment_data() ? curr_format( round(parseFloat($root.payment_data().remaining_interest) , 2) ) : 0 "></span>&nbsp;&nbsp;
                                <span style="font-size: 1.2em;" class="text-danger">Demanded Penalty:</span>
                                <span style="font-size: 1.2em;" class="text-dark"
                                    data-bind="text: curr_format($root.single_installment_total_penalty() )"></span>

                            </p>
                        </div>
                        <label class="col-lg-2 col-form-label text-success">Collect Interest first? <span
                                class="text-danger">*</span></label>

                        <div class="col-lg-2 form-group mt-1">
                            <label> <input value="0" name="interest_first" type="radio"
                                    data-bind="checked: $root.interest_first" required="required"> No </label>
                            <label> <input value="1" type="radio" name="interest_first"
                                    data-bind="checked: $root.interest_first" required="required"> Yes</label>
                        </div>
                        <div class="col-lg-2"></div>

                        <!--ko if: $root.interest_first() !== undefined  -->
                        <div class="form-group row col-md-12 col-lg-12 d-flex">
                            <div class="col-12 col-lg-12">
                                <fieldset class="col-12">
                                    <legend style=" text-align: right;">Payment Summary</legend>
                                    <table class='table table-hover'>
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;" class="border-right">#</th>
                                                <th style="width: 50%;">Particular</th>
                                                <th style="width: 40%; text-align: right;">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="border-right">1</td>
                                                <td>
                                                    <span class="input-xs" required>Principal</span>
                                                </td>
                                                <td style="text-align: right;">
                                                    <!--ko ifnot: $root.edit_principal() -->
                                                    <span
                                                        data-bind="text: curr_format($root.principal_amount())"></span>
                                                    <!--/ko -->
                                                    <!--ko if: $root.edit_principal() -->

                                                    <!--ko if: $root.totalAmount() ? parseInt($root.totalAmount()) > 0 : false -->
                                                    <input id="td_principal_amount" class="form-control"
                                                        autocomplete="off" type="number"
                                                        data-bind="textInput: $root.principal_amount, attr: {'data-rule-max': parseFloat($root.payment_data().remaining_principal), 'data-msg-max':'Amount is greater than the Demanded principal'}"
                                                        min="0" required="required">
                                                    <!--/ko -->
                                                    <!--ko if: $root.totalAmount() ? parseInt($root.totalAmount()) <= 0 : true -->
                                                    <input class="form-control" autocomplete="off" type="number"
                                                        data-bind="textInput: $root.principal_amount, attr: {'data-rule-max': parseFloat($root.payment_data().remaining_principal), 'data-msg-max':'Amount is greater than the Demanded principal'}"
                                                        min="0" disabled>
                                                    <!--/ko -->

                                                    <!--/ko -->
                                                    <input type="hidden" name="paid_principal"
                                                        data-bind="textInput: $root.principal_amount">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border-right">2</td>
                                                <td>
                                                    <span class="input-xs" required>Interest</span>
                                                    <!--ko ifnot: ($root.totalAmount() ? parseInt($root.totalAmount()) <= 0 : true) -->
                                                    <span data-bind="click: () => {
                                                    $root.edit_interest(true);
                                                    $('#td_interest_amount').focus();
                                                    $root.edit_click('td_interest_amount');
                                                    }" role="button" class="ml-1" style="cursor: pointer;"><i
                                                            class="fa fa-edit text-success"></i></span>
                                                    <!--/ko -->
                                                    <!--ko if: $root.payment_data() ? parseInt( parseFloat($root.payment_data().remaining_interest) - ($root.interest_amount() ? parseFloat($root.interest_amount()) : 0 ) ) > 0  : false -->
                                                    <p class="blueText d-flex mt-1">
                                                        <span class="text-danger"
                                                            data-bind="text: 'Remaing Interest : ' + curr_format(( parseFloat($root.payment_data().remaining_interest) - ($root.interest_amount() ? parseFloat($root.interest_amount()) : 0 ) ))"></span>
                                                        &nbsp; &nbsp;
                                                        <span class="d-flex align-items-center">
                                                            Forgive remaining interest?&nbsp;<input type="checkbox"
                                                                data-bind="checked: $root.forgive_interest">
                                                        </span>
                                                        <input type="hidden" name="forgiven_interest"
                                                            data-bind="textInput: $root.forgiven_interest">
                                                    </p>
                                                    <!--/ko -->
                                                </td>
                                                <td style="text-align: right;">
                                                    <!--ko ifnot: $root.edit_interest() -->
                                                    <span data-bind="text: curr_format($root.interest_amount())"></span>
                                                    <!--/ko -->
                                                    <!--ko if: $root.edit_interest() -->

                                                    <!--ko if: $root.totalAmount() ? parseInt($root.totalAmount()) > 0 : false -->
                                                    <input id="td_interest_amount" class="form-control"
                                                        autocomplete="off" type="number"
                                                        data-bind="textInput: $root.interest_amount, attr: {'data-rule-max': parseFloat($root.payment_data().remaining_interest), 'data-msg-max':'Amount is greater than the Demanded Interest'}"
                                                        required="required" min="0">
                                                    <!--/ko -->
                                                    <!--ko if: $root.totalAmount() ? parseInt($root.totalAmount()) <= 0  : true -->
                                                    <input class="form-control" autocomplete="off" type="number"
                                                        data-bind="textInput: $root.interest_amount" disabled>
                                                    <!--/ko -->

                                                    <!--/ko -->
                                                    <input type="hidden" name="paid_interest"
                                                        data-bind="textInput: $root.interest_amount">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="border-right">3</td>
                                                <td>
                                                    <span class="input-xs" required>Penalty</span>
                                                    <!--ko ifnot: ($root.penalty_amount() ? (parseFloat($root.penalty_amount().penalty_value) + parseFloat($root.loan_installment().demanded_penalty)) <= 0 : true) || ($root.totalAmount() ? parseInt($root.totalAmount()) <= 0 : true) -->
                                                    <span data-bind="click: () => {
                                                    $root.edit_penalty(true);
                                                    $('#td_penalty').focus();
                                                    $root.edit_click('td_penalty');
                                                    }" role="button" class="ml-1" style="cursor: pointer;"><i
                                                            class="fa fa-edit text-success"></i></span>
                                                    <!--/ko -->
                                                    <!--ko if: parseInt( (parseFloat($root.penalty_amount().penalty_value) + parseFloat($root.loan_installment().demanded_penalty)) - ( $root.received_penalty_amount() ? parseFloat($root.received_penalty_amount()) : 0 ) ) > 0 -->
                                                    <p class="blueText d-flex mt-1">
                                                        <span class="text-danger"
                                                            data-bind="text: 'Remaining Penalty : ' + curr_format(( (parseFloat($root.penalty_amount().penalty_value) + parseFloat($root.loan_installment().demanded_penalty)) - ( $root.received_penalty_amount() ? parseFloat($root.received_penalty_amount()) : 0 ) ).toFixed(2))"></span>
                                                        &nbsp; &nbsp;
                                                        <span class="d-flex align-items-center">
                                                            Forgive remaining Penalty?&nbsp;<input type="checkbox"
                                                                data-bind="checked: $root.forgive_penalty">
                                                        </span>
                                                        <input type="hidden" name="forgiven_penalty"
                                                            data-bind="textInput: $root.forgiven_penalty">
                                                    </p>
                                                    <!--/ko -->
                                                </td>
                                                <td style="text-align: right;">
                                                    <!--ko ifnot: $root.edit_penalty() -->
                                                    <span
                                                        data-bind="text: curr_format($root.received_penalty_amount())"></span>
                                                    <!--/ko -->
                                                    <!--ko if: $root.edit_penalty() -->

                                                    <!--ko if: $root.penalty_amount() ? (parseFloat($root.penalty_amount().penalty_value) + parseFloat($root.loan_installment().demanded_penalty)) > 0 : false -->
                                                    <input id="td_penalty" class="form-control" autocomplete="off"
                                                        type="number"
                                                        data-bind="textInput: $root.received_penalty_amount, valueUpdate:'afterkeydown', attr: {'data-rule-max': (parseFloat($root.penalty_amount().penalty_value) + parseFloat($root.loan_installment().demanded_penalty)), 'data-msg-max':'Amount is greater than the Demanded Penalty'} "
                                                        min="0" required="required">
                                                    <!--/ko -->
                                                    <!--ko if: $root.penalty_amount() ? (parseFloat($root.penalty_amount().penalty_value) + parseFloat($root.loan_installment().demanded_penalty)) <= 0 : true -->
                                                    <input class="form-control" autocomplete="off" type="number"
                                                        data-bind="textInput: $root.received_penalty_amount, valueUpdate:'afterkeydown' "
                                                        min="0" disabled>
                                                    <!--/ko -->

                                                    <!--/ko -->
                                                    <input type="hidden" name="paid_penalty"
                                                        data-bind="textInput: $root.received_penalty_amount">
                                                </td>
                                            </tr>
                                            <!-- ko if: parseInt($root.curtail_loan()) == 0 -->
                                            <tr>
                                                <td class="border-right">4</td>
                                                <td>
                                                    <span class="input-xs" required>Extra Amount</span>
                                                </td>
                                                <td style="text-align: right;">
                                                    <span>
                                                        <input
                                                            style="border : 0; outline: none; background: none; margin: 0; padding: 0; text-align: right;"
                                                            id="extra_amount" name="extra_amount" class="form-control"
                                                            autocomplete="off" type="number"
                                                            data-bind="value: (parseFloat($root.extra_amount()).toFixed(2)), attr: {'data-rule-min': parseInt(0), 'data-msg-min':'Extra amount can not be negative. Make sure the Total amount covers the indicated principal, interest and penalty'}"
                                                            readonly="readonly">

                                                        <input type="hidden" name="extra_amount"
                                                            data-bind="textInput: $root.extra_amount">
                                                    </span>

                                                </td>
                                            </tr>
                                            <!-- /ko -->
                                            <?php if (!empty($org['loan_curtailment']) && $org['loan_curtailment'] == 1) { ?>
                                            <!-- ko if: parseInt($root.curtail_loan()) == 1 -->
                                            <tr>
                                                <td class="border-right">4</td>
                                                <td class="d-flex flex-column">
                                                    <span class="input-xs" required>Loan Principal Curtailment</span>
                                                    <span class="loan_curtailment_error"></span>

                                                </td>
                                                <td style="text-align: right;">
                                                    <span data-bind="text: curr_format($root.extra_amount())"></span>
                                                    <input type="hidden" id="td_extra_principal"
                                                        class="form-control do-not-ignore" autocomplete="off"
                                                        name="extra_principal"
                                                        data-bind="textInput: $root.extra_amount, valueUpdate:'afterkeydown' , attr: {
                                                'data-rule-min':parseFloat($root.loan_curtailment_min()),'data-msg-min':'Curtailment amount should be greater or eqaul to ' + curr_format(parseFloat($root.loan_curtailment_min())), 'data-rule-max': parseFloat($root.loan_curtailment_max()), 'data-msg-max': 'Curtailment amount can not exceed ' + curr_format(parseFloat($root.loan_curtailment_max())) }">

                                                </td>
                                            </tr>
                                            <!-- /ko -->
                                            <?php } ?>

                                        </tbody>

                                    </table>
                                </fieldset>
                            </div>
                        </div>

                        <!--ko if: $root.extra_amount() > 0 && ($root.curtail_loan() == '0') -->
                        <div class="col-lg-12 row d-flex my-2">
                            <label class="col-lg-2 col-form-label text-success">Use extra amount as? <span
                                    class="text-danger">*</span>
                                <span class="after-p"></span>
                            </label>

                            <div class="col-lg-10 form-group d-flex mt-2">
                                <!--ko if: $root.loan_installments()[$root.loan_installments().length -1].installment_number != $root.loan_installment().installment_number -->
                                <label class=""> <input checked value="1" name="extra_amount_use" type="radio"
                                        data-bind="checked: $root.extra_amount_use" required="required">&nbsp; Next
                                    Installment</label>
                                <!--/ko -->
                                <label class="ml-2"> <input value="2" type="radio" name="extra_amount_use"
                                        data-bind="checked: $root.extra_amount_use" required="required">&nbsp;
                                    <?php if (in_array('6', $modules)) { ?>Savings Deposit<?php } else { ?>Extra
                                    Income<?php } ?></label>

                            </div>
                        </div>
                        <!--/ko -->

                        <!-- End Ist Interst First Check -->
                        <!--/ko -->


                    </div>

                    <!--ko if: $root.interest_first() !== undefined  -->
                    <div class="row form-group">
                        <label data-bind="visible: id !=5" class="col-lg-2 col-form-label">Transaction Channel <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-4" data-bind="visible: id !=5">
                            <select class="form-control" required
                                data-bind='options: $root.transaction_channel, optionsText:function(data_item){return data_item.channel_name}, optionsCaption: "--select--", optionsAfterRender: setOptionValue("id"), attr:{name:"transaction_channel_id"}, value:$root.trans_channel'
                                data-msg-required="Transaction channel must be selected" style="width: 100%">
                            </select>
                        </div>
                        <label data-bind="visible: id ==5" class="col-lg-2 col-form-label">Savings Account<span
                                class="text-danger">*</span></label>
                        <div class="col-lg-4" data-bind="visible: id ==5">
                            <select class="form-control" required data-bind='
                                    options: $root.filtered_savingac, 
                                    optionsText: function(data_item){return data_item.account_no + " | " + data_item.member_name}, 
                                    optionsCaption: "-- select --", optionsAfterRender: setOptionValue("id"),attr:{name:"savings_account_id"}' style="width: 100%">
                            </select>
                        </div>
                    </div>
                    <!--/ko -->


                    <!--/ko-->
                    <!--payment mode -->
                    <!--/ko -->

                    <!--/ko-->

                    <!-- ko if: $root.installment_payment_date() -->

                    <!-- ko with: payment_data -->

                    <!--ko if: $root.interest_first() !== undefined  -->
                    <div class="form-group row">
                        <label class="col-lg-2 col-form-label">Narrative</label>
                        <div class="col-lg-4">
                            <textarea required rows="3" class="form-control" id="comment" name="comment"></textarea>
                        </div>
                        <div class="col-lg-6">
                            <fieldset>
                                <legend style=" text-align: right;"> Received Amount</legend>
                                <input class="form-control"
                                    data-bind="value: $root.totalAmount() ? $root.totalAmount() : 0" name="paid_total"
                                    type="text" required hidden>
                                <h2 class="pull-right"
                                    data-bind="text: $root.totalAmount() ? curr_format($root.totalAmount()) : 0"></h2>
                            </fieldset>
                        </div>
                    </div>
                    <!--/ko -->

                    <br>

                    <!--/ko-->
                    <!-- with payment data -->

                    <!--/ko -->

                    <!--/ko-->
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