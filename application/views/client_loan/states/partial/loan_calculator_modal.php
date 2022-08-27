<?php $user_name = (isset($user['firstname']) ? $user['firstname'] . ' ' . $user['lastname'] . ' ' . $user['othernames'] : ''); ?>

<div class="modal inmodal fade" id="loan_calculator-modal" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form id="formLoan_calculator" method="post" class="formValidate">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                    <h4 class="modal-title">Loan Calculator</h4>
                    <small class="font-bold">Note: Required fields are marked with <span class="text-danger">*</span>, and ensure to click the calculate button after entering the required fields</small>
                </div>
                <div class="modal-body">
                    <!-- Start of the modal body -->
                    <div class="form-group row mb-0 d-flex justify-content-center">
                        <?php if (isset($case2) && $case2 == 'My Loans') { ?>

                            <div class="col-lg-4 d-flex flex-column">
                                <label class="col-form-label">Client</label>
                                <div class="form-group">
                                    <input id="member_id_2" type="hidden" name="member_id" value="<?php echo (isset($user['id']) ? $user['id'] : ''); ?>">
                                    <input type="text" class="form-control" readonly value="<?php echo (isset($user['firstname']) ? $user['firstname'] . ' ' . $user['lastname'] . ' ' . $user['othernames'] . '- ' . $user['client_no'] : ''); ?>">
                                </div>

                            </div>

                        <?php } else { ?>
                            <div class="col-lg-4 d-flex flex-column">
                                <label class="col-form-label">Client (optional)</label>
                                <div class="form-group">
                                    <select class="form-control" id="loan_calculator_member_id" name="member_id" data-bind='options: $root.member_names, optionsText: function(item){ return item.member_name+"-"+item.client_no;}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: member_name' style="width: 100%">
                                    </select>
                                </div>

                            </div>
                        <?php } ?>

                        <div class="col-lg-4 d-flex flex-column">
                            <label class="col-form-label">Loan Product<span class="text-danger">*</span></label>
                            <div class="form-group">
                                <select class="select2able form-control" name="loan_product_id" data-bind='options: product_names, optionsText: "product_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: product_name' required data-msg-required="Loan Product is required" style="width: 100%">
                                </select>
                            </div>
                        </div>
                        <!-- ko with: product_name -->
                        <div class="col-lg-4 d-flex flex-column">

                            <label class="col-form-label">Amount<span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input id="loan_calc_amount" class="form-control" name="requested_amount" type="number" data-bind='textInput: $root.amount, attr: {"data-rule-min":((parseFloat(min_amount)>0)?min_amount:null), "data-rule-max": ((parseFloat(max_amount)>0)?max_amount:null), "data-msg-min":"Loan amount is less than "+curr_format(parseInt(min_amount)), "data-msg-max":"Loan amount is more than "+curr_format(parseInt(max_amount)),value: def_amount}' required />
                                <div class="blueText">
                                    <p>
                                        <span data-bind="visible: (parseFloat(min_amount)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(min_amount)>0), text: curr_format(parseInt(min_amount))"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(max_amount)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(max_amount)>0), text: curr_format(parseInt(max_amount))"></span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <!--/ko-->





                    </div>
                    <!-- ko with: product_name -->
                    <!--ko if: ($root.member_name() || '<?php echo $user_name ?>') && $root.requestable_amounts() -->
                    <div class="row col-lg-12">
                        <fieldset class="col-lg-12">
                            <legend class="text-success" style=" text-align: right;">Loan Qualification Check</legend>
                            <table class='table table-hover'>
                                <thead>
                                    <th>Client</th>
                                    <th>Check</th>
                                    <!--ko if: !$root.qualification_check() -->
                                    <th>Needed Collateral</th>
                                    <!--/ko -->
                                    <th>Savings</th>
                                    <th>Shares</th>
                                </thead>

                                <tbody>
                                    <tr>
                                        <td data-bind="text: '<?php echo $user_name ?>' || $root.member_name().member_name"></td>
                                        <td>
                                            <span data-bind="visible: $root.qualification_check()" class="text-success">
                                                <i class="fa fa-check"></i> Qualifies
                                            </span>
                                            <span data-bind="visible: !$root.qualification_check()" class="text-danger">
                                                <i class="fa fa-times"></i> Does not Qualify
                                            </span>
                                        </td>
                                        <!--ko if: !$root.qualification_check() -->
                                        <td>
                                            <!-- ko if: $root.requestable_amounts() && $root.requestable_amounts().hasOwnProperty('needed_col') -->
                                            <span data-bind="text: curr_format($root.requestable_amounts().needed_col)"></span>
                                            <!--/ko -->
                                            <!-- ko if: $root.requestable_amounts() && !$root.requestable_amounts().hasOwnProperty('needed_col') -->
                                            <span data-bind="text: curr_format(round(parseFloat($root.requestable_amounts().min) - $root.requestable_amounts().max , 2))">
                                            </span>
                                            <!--/ko -->
                                        </td>
                                        <!--/ko -->
                                        <td>
                                            <span data-bind="text: curr_format($root.requestable_amounts().savings_total)"></span>
                                        </td>
                                        <td>
                                            <span data-bind="text: curr_format($root.requestable_amounts().shares_total)"></span>
                                        </td>
                                    </tr>

                                </tbody>
                            </table>
                        </fieldset>
                    </div>

                    <div class="col-lg-12 my-1"></div>
                    <!--/ko -->

                    <!-- ko if: $root.filtered_loan_fees() && $root.product_name() -->
                    <div class="row col-lg-12">
                        <fieldset class="col-lg-12">
                            <legend style="text-align: right;">Required Fees</legend>
                            <table class='table table-hover'>
                                <thead>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                </thead>
                                <tbody>
                                    <!-- ko foreach: $root.filtered_loan_fees -->
                                    <tr>
                                        <td data-bind="text: feename"></td>
                                        <td data-bind="text: feetype"></td>
                                        <td data-bind="text: parseInt(amountcalculatedas_id) === 2 ? curr_format(round(amount , 2)) : (
                                            parseInt(amountcalculatedas_id) === 1 ? curr_format(round(parseFloat($root.amount() ? $root.amount() : $root.product_name().min_amount)*( parseFloat(amount) /100) , 2)) : (
                                                parseInt(amountcalculatedas_id) === 3 ? curr_format(round($root.compute_fee_amount(loanfee_id, $root.amount() ? $root.amount() : $root.product_name().min_amount) )) : 0
                                            )
                                        )"></td>

                                    </tr>

                                    <!-- /ko -->
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <strong>Total</strong>

                                        </td>
                                        <td></td>
                                        <td style="font-weight: bold;" data-bind="text: $root.filtered_loan_fees_total()"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </fieldset>
                    </div>
                    <div class="col-lg-12 my-1"></div>
                    <!--/ko -->


                    <div class="form-group row col-lg-12 mt-0">
                        <div class="col-lg-12">
                            <span class="text-danger"><small>The loan term should not exceed <span data-bind="text: $parent.loan_product_length" class="blueText"></span> which is the maximum loan period for this loan product</small></span>
                        </div>

                        <div class="form-group row col-lg-12 my-0">

                            <div class="col-lg-4 d-flex flex-column">
                                <label class="col-form-label">No of Installments<span class="text-danger">*</span></label>
                                <div>
                                    <input data-rule-mustbelessthanProductMaxLoanPeriod placeholder="" required min="1" class="form-control" name="installments" type="number" data-bind='textInput:$root.installments, attr: {"data-rule-min":((parseFloat(min_repayment_installments)>0)?min_repayment_installments:null), "data-rule-max": ((parseFloat(max_repayment_installments)>0)?max_repayment_installments:null), "data-msg-min":"Installment is less than "+parseInt(min_repayment_installments), "data-msg-max":"Installment is more than "+parseInt(max_repayment_installments),
                            value:def_repayment_installments}' required id="installment" />
                                    <div class="blueText">
                                        <p>
                                            <span data-bind="visible: (parseFloat(min_repayment_installments)>0)">Min: </span>
                                            <span data-bind="visible: (parseFloat(min_repayment_installments)>0), text: parseInt(min_repayment_installments)"></span>&nbsp;
                                            <span data-bind='visible: (parseFloat(max_repayment_installments)>0)'>Max: </span>
                                            <span data-bind="visible: (parseFloat(max_repayment_installments)>0), text: parseInt(max_repayment_installments)"></span>
                                        </p>
                                    </div>
                                </div>

                            </div>

                            <div class="col-lg-4 d-flex flex-column">
                                <label class="col-form-label">Paid every After<span class="text-danger">*</span></label>
                                <div class="">
                                    <input data-rule-mustbelessthanProductMaxLoanPeriod data-bind='textInput:repayment_frequency, attr:{value:$root.repayment_frequency}' required class="form-control" name="repayment_frequency" id="paid_every" min="1" type="number">
                                </div>
                            </div>

                            <div class="col-lg-4 d-flex flex-column">
                                <label style="visibility: hidden;" class="col-form-label">Period</label>
                                <div class="">
                                    <select data-rule-mustbelessthanProductMaxLoanPeriod class="form-control" name="repayment_made_every" id="period_id" data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", textInput: $root.repayment_made_every, value:$root.product_name().repayment_made_every' required data-msg-required="This field is required">
                                    </select>
                                </div>
                            </div>



                        </div>
                        <!--/row -->
                    </div>
                    <div class="form-group col-lg-12 row my-0">
                        <div class="col-lg-4 d-flex flex-column">
                            <label class="col-form-label">Interest rate<span class="text-danger">*</span></label>
                            <div class="form-group">
                                <input min="0" step="0.01" required class="form-control" name="interest_rate" type="number" data-bind='textInput: $root.interest_rate, attr: {"data-rule-min":((parseFloat(min_interest)>0)?min_interest:null), "data-rule-max": ((parseFloat(max_interest)>0)?max_interest:null), "data-msg-min":"Interest rate is less than "+parseFloat(min_interest), "data-msg-max":"Interest rate is more than "+parseFloat(max_interest),value: def_interest}' required />
                                <div class="blueText">
                                    <p>
                                        <span data-bind="visible: (parseFloat(min_interest)>0)">Min: </span>
                                        <span data-bind="visible: (parseFloat(min_interest)>0), text: parseFloat(min_interest)"></span>&nbsp;
                                        <span data-bind='visible: (parseFloat(max_interest)>0)'>Max: </span>
                                        <span data-bind="visible: (parseFloat(max_interest)>0), text: parseFloat(max_interest)"></span>
                                    </p>
                                </div>
                            </div>

                        </div>
                        <div class="col-lg-5 d-flex flex-column">
                            <label class="col-form-label">Date of Payment <small>(For first Installment)</small> <span class="text-danger">*</span></label>
                            <div class=" form-group">
                                <div class="input-group date">
                                    <input class="form-control" autocomplete="off" data-bind="datepicker: $root.payment_date" required name="action_date" type="text" value="<?php echo date('d-m-Y'); ?>"><span data-bind="datepicker: $root.payment_date" class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div>
                            </div>

                        </div>

                        <div class="col-lg-3"></div>

                    </div>

                    <div class="row d-flex justify-content-center mt-0 mb-2">
                        <a href="#" class="btn btn-success btn-lg" data-bind="click: () =>  {
                            if($('#formLoan_calculator').valid()) {
                                $root.calculate();
                                if($root.payment_date()){
                                    $('#loan_amortization_schedule_1').css('display', 'flex');
                                }
                            }
                            
                            
                        }"><i class="fa fa-check"></i>Calculate</a>
                    </div>

                    <!--/ko-->
                    <!--ko if: typeof $root.product_name() !=='undefined' -->
                    <div style="display: none;" id="loan_amortization_schedule_1" class="form-group row">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <caption class="text-success" style=" font-size: 1.5em; font-weight: bold; text-align: center; caption-side: top;">Loan Amortization Schedule <span class="ml-4"><button type="button" onclick="printJS({printable: 'printable_loan_amortization', type: 'html', targetStyles: ['*'], documentTitle: 'Loan-Amortization'})" class="btn btn-primary btn-sm"><i class="fa fa-print fa-2x"></i></button></span></caption>
                                <thead>
                                    <tr>
                                        <th>## </th>
                                        <th>Date of Payment</th>
                                        <th>Interest Amount(UGX)</th>
                                        <th>Principal Amount(UGX)</th>
                                        <th>Total Installment(UGX)</th>
                                    </tr>
                                </thead>
                                <tbody data-bind="foreach: payment_schedule">
                                    <tr>
                                        <td><span data-bind="text: (installment_number)?installment_number:''"></span></td>
                                        <td><span data-bind="text: (payment_date)?moment(payment_date,'X').format('D-MMM-YYYY'):'None';"></span></td>
                                        <td><span data-bind="text: curr_format(round(interest_amount,2))"></span></td>
                                        <td> <span data-bind="text: curr_format(round(principal_amount,2))"></span></td>
                                        <td data-bind="text: curr_format(round(paid_principal,2))"></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr data-bind="with: payment_summation">
                                        <th></th>
                                        <th data-bind="text: 'Period '+ payment_date"></th>
                                        <th data-bind="text: 'Total '+ curr_format(round(interest_amount,2))"></th>
                                        <th data-bind="text: 'Total '+ curr_format(round(principal_amount,0))"></th>
                                        <th data-bind="text: 'Total '+ curr_format(round(paid_principal,2))"></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <!--/ko-->

                </div><!-- End of the modal body -->
                <div class="modal-footer">
                    <!-- start of the modal footer -->
                    <button type="button" data-dismiss="modal" id="btn-cancel" name="btn_cancel" class="btn btn-danger btn-sm">
                        <i class="fa fa-times"></i> Cancel</button>
                </div><!-- End of the modal footer -->
            </form>

            <?php $this->load->view('client_loan/states/partial/print_loan_amortization_modal'); ?>
        </div>
    </div>
</div>