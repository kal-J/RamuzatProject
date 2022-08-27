<!-- date range picker section -->
<div class="row">
    <div class="col-lg-8">

    </div>
    <div class="col-lg-4">
        <div id="reportrange" class="reportrange pull-right">
            <i class="fa fa-calendar"></i><span>January 01, 2019 - December 31, 2020</span> <b class="caret"></b>
        </div>
    </div>
</div>
<!-- end of date range picker section -->

<!-- First row -->
<div class="row">
    <div class="col-lg-7">
        <div class="ibox">
            <div class="ibox-content">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row text-left">
                        <div class="col">
                            <span class="h5 font-bold m-t block text-info">Loans</span>
                            <?php if (in_array('1', $client_loan_privilege)) { ?>
                                <small class="text-muted m-b block"><a href="#add_client_loan-modal" data-toggle="modal" class="btn btn-outline-primary btn-rounded btn-success  btn-sm"><i class="fa fa-plus-circle"></i> Apply</a></small>
                            <?php } ?>
                        </div>

                        <div class="col" data-bind="with: loan_count_partial">
                            <span class="h5 font-bold m-t block text-info" data-bind="text: loan_count"></span>
                            <small class="m-b block text-info">Partial</small>
                        </div>
                        <?php if ($org['loan_app_stage'] == 0) { ?>
                            <div class="col" data-bind="with: loan_count_pend_approval">
                                <span class="h5 font-bold m-t block text-success" data-bind="text: loan_count"></span>
                                <small class="m-b block text-success">Pending</small>
                            </div>
                        <?php }
                        if (($org['loan_app_stage'] == 0) || ($org['loan_app_stage'] == 1)) { ?>
                            <div class="col" data-bind="with: loan_count_approved">
                                <span class="h5 font-bold m-t block text-warning" data-bind="text: loan_count"></span>
                                <small class="m-b block text-warning">Approved</small>
                            </div>
                        <?php } ?>
                        <div class="col" data-bind="with: loan_count_active">
                            <div class=" m-l-md">
                                <span class="h5 font-bold m-t block text-success" data-bind="text: loan_count"></span>
                                <small class="m-b block text-success">Active</small>
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="ibox">
            <div class="ibox-content">
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <div class="row text-left">
                        <div class="col">
                            <div class=" m-l-md">
                                <span class="h5 font-bold m-t block"><?php echo $this->lang->line('cont_client_name_p'); ?></span>
                                <?php if (in_array('1', $member_privilege)) { ?>
                                    <small class="text-muted m-b block"><a href="#add_member-modal" class="btn btn-outline-primary btn-rounded  btn-sm btn-success" data-toggle="modal"><i class="fa fa-plus-circle"></i> Add</a></small>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col" data-bind="with: client_count_active">
                            <span class="h5 font-bold m-t block" data-bind="text: client_count"></span>
                            <small class="text-muted m-b block">Active</small>
                        </div>
                        <div class="col text-danger " data-bind="with: client_count_inactive">
                            <span class="h5 font-bold m-t block" data-bind="text: client_count"></span>
                            <small class="m-b block">Inactive</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- End of first row -->

<!-- ============================================================= Dashboard ========================================== -->
<div class="row">
    <!-- The section for savings, Income & Expense graph -->
    <div class="col-lg-7">
        <div class="row">
            <!--<div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                       <span class="label label-default float-right" style="font-weight:bold; font-size:11px;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span> 
                        <h5>Balance Changes</h5>
                    </div>
                    <div class="ibox-content">
                    <div class="row">
                      <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4" data-bind="with:amount_paid">
                        <span style ="font-weight:bold; font-size:11px;" >Principal Collected </span>
                       <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:already_principal_amount?curr_format(round(already_principal_amount,2)*1):0">0</span></h4>
                       </div>
                       <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                       <span style ="font-weight:bold; font-size:11px;" >Principal Disbursed </span>
                       <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:principal_disbursed()?curr_format(round(principal_disbursed(),2)*1):0">0</span></h4>
                       </div>
                       <div class="col-xs-12 col-sm-6 col-md-4 col-lg-4">
                       <span style ="font-weight:bold; font-size:11px;" >Gross loan portfolio</span>
                       <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:gross_loan_portfolio()?curr_format(round(gross_loan_portfolio(),2)*1):0">0</span></h4>
                       </div>
                       <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
                       <span style ="font-weight:bold; font-size:11px;" >Change in Portfolio</span>
                       <h4 class="no-margins"><span style ="font-weight:bold;" class="text-success" data-bind="text:change_in_Portfolio()?curr_format(change_in_Portfolio()*1):0">0</span></h4>
                       </div> 
                    </div>
                    </div>
                </div>
            </div> -->
            <?php if (in_array('6', $modules)) { ?>
                <!-- the row for savings    -->
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-content">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="row text-left">
                                    <div class="col text-success">
                                        <span class="h5 font-bold m-t block outline-primary">Savings</span>
                                        <?php if (in_array('24', $savings_privilege)) { ?>
                                            <small class="text-muted m-b block"><a href="#add_transaction" class="btn btn-outline-primary btn-rounded btn-success  btn-sm" data-toggle="modal"><i class="fa fa-plus-circle"></i> Deposit</a></small>
                                        <?php } ?>
                                    </div>

                                    <div class="col text-success ">
                                        <span class="h5 font-bold m-t block" data-bind="text:deposits_sum()?curr_format(round(deposits_sum(),2)*1):0"></span>
                                        <small class="m-b block">Total Credit </small>
                                    </div>
                                    <div class="col">
                                        <span class="h5 font-bold m-t block" data-bind="text:withdraw_sum()?curr_format(round(withdraw_sum(),2)*1):0"></span>
                                        <small class="text-muted m-b block">Total Debit </small>
                                    </div>
                                    <div class="col text-info ">
                                        <span class="h5 font-bold m-t block" data-bind="text:savings_sums()?curr_format(round(savings_sums(),2)*1):0"></span>
                                        <small class="m-b block">Total Savings</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End of the row for savings    -->

            <?php } ?>
            <?php if (in_array('12', $modules)) { ?>
                <!-- the row for savings    -->
                <div class="col-lg-12">
                    <div class="ibox ">
                        <div class="ibox-content">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <div class="row text-left">
                                    <div class="col text-success">
                                        <span class="h5 font-bold m-t block outline-primary">Shares</span>
                                        <!-- <small class="text-muted m-b block"><a href="#add_transaction" class="btn btn-outline-primary btn-rounded btn-success  btn-sm" data-toggle="modal"><i class="fa fa-plus-circle"></i> Deposit</a></small> -->
                                    </div>

                                    <div class="col text-success ">
                                        <span class="h5 font-bold m-t block" data-bind="text:share_deposits_sum()?curr_format(round(share_deposits_sum(),2)*1):0"></span>
                                        <small class="m-b block">Total Credit </small>
                                    </div>
                                    <div class="col">
                                        <span class="h5 font-bold m-t block" data-bind="text:share_withdraw_sum()?curr_format(round(share_withdraw_sum(),2)*1):0"></span>
                                        <small class="text-muted m-b block">Total Debit </small>
                                    </div>
                                    <div class="col text-info ">
                                        <span class="h5 font-bold m-t block" data-bind="text:share_sums()?curr_format(round(share_sums(),2)*1):0"></span>
                                        <small class="m-b block">Total Shares</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End of the row for savings    -->

            <?php } ?>
            <!-- Section for the income expense graph -->
            <div class="col-lg-12">
                <div class="card ">
                    <div class="card-body">
                        <div id="line_graph1" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                    </div>
                </div>
            </div>
            <!--End of Section for the income expense graph -->
        </div>
    </div>
    <!-- End of section for Savings, Income & Expense -->

    <div class="col-lg-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="card ">
                    <div class="card-body">
                        <?php if (in_array('6', $modules)) { ?>
                            <div id="semi_circle2" style="min-width: 310px; height: 250px; margin: 0 auto"></div>
                        <?php } else { ?>
                            <div id="semi_circle2" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

                        <?php } ?>
                    </div>
                </div>
            </div>

            <?php if (in_array('6', $modules)) { ?>
                <div class="col-lg-12">
                    <div class="card ">
                        <div class="card-body">
                            <div id="semi_circle1" style="min-width: 310px; height: 250px; margin: 0 auto"></div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    <!-- <div class="col-lg-4">

<div class="row">
<div class="col-lg-12">
<?php //if (!empty($sms_module['id'])) { 
?>
        <div class="ibox ">
            <div class="ibox-title">
            <span class="label label-default float-right" style="font-weight:bold; font-size:11px;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span> 
                <h5>SMS sent out </h5>
            </div>
            <div class="ibox-content">
            <table class="table table-sm">
            <tbody >
                <tr >
                    <th scope="row">Loan module </th>
                    <td><h4 class="no-margins"><span style ="font-weight:bold;"  class="text-success" data-bind="text:loan_sms_total()?curr_format(round(loan_sms_total(),2)*1):0">0</span></h4> 
                </tr>
                <tr >
                    <th scope="row">Savings module </th>
                    <td><h4 class="no-margins"><span style ="font-weight:bold;"  class="text-success" data-bind="text:savings_sms_total()?curr_format(round(savings_sms_total(),2)*1):0">0</span></h4> 
                </tr>
                <tr class="table-active">
                    <th scope="row">Total SMS sent</th>
                    <td><h4 class="no-margins"><span style ="font-weight:bold;" data-bind="text:total_sms()?curr_format(round(total_sms(),2)*1):0" >0</span></h4> 
                </tr>
            </tbody>
            </table>
            </div>
        </div>
    <?php //} 
    ?>
    </div>
</div>


</div> -->


</div>
<!--  ===================end of row two ================ -->
<?php $this->view('user/member/add_member_model'); ?>
<!--  ko stopBinding: true -->
<div id="tab-savings">
    <?php $this->load->view('savings_account/deposits/add_modal'); ?>
</div>
<!--  /ko -->

<!--  ko stopBinding: true -->
<div id="tab-loans">
    <?php $this->view('client_loan/states/partial/steps_add_modal'); ?>

    <script type="text/html" id="application_details">
        <legend>Loan Amount details</legend>


        <!--ko if: $root.qualification_check() && ($root.loan_type2() == 'client_loan') -->
        <div class="mb-3 ml-2">
            <span class="text-success text-large">Maximum amount that can be requested is &nbsp; <span data-bind="text: $root.requestable_amounts() ? curr_format($root.requestable_amounts().max) : 0"></span></span>
        </div>
        <!--/ko -->

        <!--ko ifnot: $root.qualification_check() -->
        <!--ko if: ($root.loan_type2() == 'client_loan') -->
        <div class="form-group row m-1">
            <div class="text-danger text-large">This Member does not qualify for this Loan </div>
            <div class="col-lg-12 text-danger">
                Member Total Savings : <span data-bind="text: $root.requestable_amounts() ? curr_format($root.requestable_amounts().savings_total) : 0"></span>
            </div>
            <div class="col-lg-12 text-danger">
                Member Total Shares : <span data-bind="text: $root.requestable_amounts() ? curr_format($root.requestable_amounts().shares_total) : 0"></span>
            </div>
            <div class="col-lg-12 text-danger">
                Minimum Collateral required : UGX. <strong data-bind="text: curr_format(parseFloat(min_amount))"></strong>
            </div>
        </div>
        <!--/ko -->
        <!--/ko -->

        <!--ko if: $root.qualification_check() || $root.loan_type2() == 'group_loan' -->

        <div class="form-group row">
            <input type="hidden" name="loan_product_id" data-bind="value: parseInt(id)">
            <label class="col-lg-3 col-form-label">Requested amount<span class="text-danger">*</span></label>
            <?php if (isset($case2) && $case2 == 'client_loan' && $type == 'group_loan') : ?>
                <div class="col-lg-3 form-group">

                    <input required class="form-control" min="0" name="requested_amount" type="number" data-bind='value: $root.app_amount, 
                        attr: {"data-rule-min":1, 
                        "data-rule-max": (parseFloat($root.group_loan_details().borrowed_amount)>0)? (parseFloat($root.group_loan_details().requested_amount) - parseFloat($root.group_loan_details().borrowed_amount)) : parseFloat($root.group_loan_details().requested_amount), "data-msg-min":"Loan amount is less than "+curr_format(parseInt(0)), "data-msg-max":"Loan amount is more than "+curr_format(parseInt($root.group_loan_details().borrowed_amount)), "value":$root.amount}' required />
                    <div class="blueText">
                        <p>
                            <span data-bind="visible: (parseFloat($root.group_loan_details().requested_amount)>0)">Min: </span>
                            <span data-bind="visible: (parseFloat($root.group_loan_details().requested_amount)>0), text: curr_format(parseInt(1))"></span>&nbsp;
                            <span data-bind='visible: (parseFloat($root.group_loan_details().requested_amount)>0)'>Max: </span>
                            <span data-bind="visible: (parseFloat($root.group_loan_details().requested_amount)>0), text: ((parseFloat($root.group_loan_details().borrowed_amount)>0)? ( parseFloat($root.group_loan_details().requested_amount) - parseFloat($root.group_loan_details().borrowed_amount)) :parseFloat($root.group_loan_details().requested_amount))"></span>
                        </p>
                    </div>
                </div>
            <?php else : ?>
                <div class="col-lg-3 form-group">
                    <input required min="0" class="form-control" name="requested_amount" type="number" data-bind='value: $root.app_amount, attr: {"data-rule-min":((parseFloat(min_amount)>0)?min_amount:null), "data-rule-max": parseFloat($root.requestable_amounts().max), "data-msg-min":"Loan amount is less than "+curr_format(parseInt(min_amount)), "data-msg-max":"Loan amount is more than "+curr_format(parseInt($root.requestable_amounts().max))}' required />
                    <div class="blueText">
                        <p>
                            <span data-bind="visible: (parseFloat(min_amount)>0)">Min: </span>
                            <span data-bind="visible: (parseFloat(min_amount)>0), text: curr_format(parseInt(min_amount))"></span>&nbsp;
                            <span data-bind='visible: (parseFloat(max_amount)>0)'>Max: </span>
                            <span data-bind="visible: (parseFloat(max_amount)>0), text: curr_format(parseInt($root.requestable_amounts().max))"></span>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
            <!--ko if: typeof $parent.loan_type()!=='undefined'&&parseInt($parent.loan_type())===1-->
            <label class="col-lg-3 col-form-label">Interest rate<span class="text-danger">*</span><br><small><em>All rates are in per annum</em></small></label>
            <div class="col-lg-3 form-group">
                <input min="0" step="0.01" required class="form-control" name="interest_rate" type="number" data-bind='value: $root.app_interest, attr: {"data-rule-min":((parseFloat(min_interest)>0)?min_interest:null), "data-rule-max": ((parseFloat(max_interest)>0)?max_interest:null), "data-msg-min":"Interest rate is less than "+parseFloat(min_interest), "data-msg-max":"Interest rate is more than "+parseFloat(max_interest)}' required />
                <div class="blueText">
                    <p>
                        <span data-bind="visible: (parseFloat(min_interest)>0)">Min: </span>
                        <span data-bind="visible: (parseFloat(min_interest)>0), text: parseFloat(min_interest)"></span>&nbsp;
                        <span data-bind='visible: (parseFloat(max_interest)>0)'>Max: </span>
                        <span data-bind="visible: (parseFloat(max_interest)>0), text: parseFloat(max_interest)"></span>
                    </p>
                </div>
            </div>
            <!--/ko-->
        </div>

        <!--/row -->
        <!--ko if: typeof $parent.loan_type()!=='undefined'&&parseInt($parent.loan_type())===1-->
        <div class="form-group row">
            <label class="col-lg-3 col-form-label">Grace period<span class="text-danger">*</span></label>
            <div class="col-lg-2 form-group">
                <input required min="0" class="form-control" name="grace_period" type="number" data-bind='textInput: def_grace_period, attr: {"data-rule-min":((parseFloat(min_grace_period)>0)?min_grace_period:null), "data-rule-max": ((parseFloat(max_grace_period)>0)?max_grace_period:null), "data-msg-min":"Grace period is less than "+parseInt(min_grace_period), "data-msg-max":"Grace period is more than "+parseInt(max_grace_period)}' required />
                <div class="blueText">
                    <p>
                        <span data-bind="visible: (parseFloat(min_grace_period)>0)">Min: </span>
                        <span data-bind="visible: (parseFloat(min_grace_period)>0), text: parseInt(min_grace_period)"></span>&nbsp;
                        <span data-bind='visible: (parseFloat(max_grace_period)>0)'>Max: </span>
                        <span data-bind="visible: (parseFloat(max_grace_period)>0), text: parseInt(max_grace_period)"></span>
                    </p>
                </div>
            </div>
            <label class="col-lg-2 col-form-label">Offset period<span class="text-danger">*</span></label>
            <div class="col-lg-2 form-group">
                <input required min="0" class="form-control" name="offset_period" type="number" data-bind='value: $root.app_offset_period, attr: {"data-rule-min":((parseFloat(min_offset)>0)?min_offset:null), "data-rule-max": ((parseFloat(max_offset)>0)?max_offset:null), "data-msg-min":"Offset period is less than "+parseInt(min_offset), "data-msg-max":"Offset period is more than "+parseInt(max_offset)}' required />
                <div class="blueText">
                    <p>
                        <span data-bind="visible: (parseFloat(min_offset)>0)">Min: </span>
                        <span data-bind="visible: (parseFloat(min_offset)>0), text: parseInt(min_offset)"></span>&nbsp;
                        <span data-bind='visible: (parseFloat(max_offset)>0)'>Max: </span>
                        <span data-bind="visible: (parseFloat(max_offset)>0), text: parseInt(max_offset)"></span>
                    </p>
                </div>
            </div>
            <div class="col-lg-3">

                <select class="form-control" name="offset_made_every" data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: $root.app_offset_every' required data-msg-required="This field is required">
                </select>
            </div>
        </div>

        <!--/row -->
        <span class="text-danger"><small>The loan term should not exceed <span data-bind="text: $parent.loan_product_length" class="blueText"></span> which is the maximum loan period for this loan product</small></span>

        <div class="form-group row">
            <label class="col-lg-3 col-form-label">No of Installments<span class="text-danger">*</span></label>
            <div class="col-lg-2">
                <input type="number" min="1" id="installment" name="installments" class="form-control" data-bind='value: $root.app_installments, attr: {"data-rule-min":((parseFloat(min_repayment_installments)>0)?min_repayment_installments:null), "data-rule-max": ((parseFloat(max_repayment_installments)>0)?max_repayment_installments:null), "data-msg-min":"Installment is less than "+parseInt(min_repayment_installments), "data-msg-max":"Installment is more than "+parseInt(max_repayment_installments)}' data-rule-mustbelessthanProductMaxLoanPeriod required />
                <div class="blueText">
                    <p>
                        <span data-bind="visible: (parseFloat(min_repayment_installments)>0)">Min: </span>
                        <span data-bind="visible: (parseFloat(min_repayment_installments)>0), text: parseInt(min_repayment_installments)"></span>&nbsp;
                        <span data-bind='visible: (parseFloat(max_repayment_installments)>0)'>Max: </span>
                        <span data-bind="visible: (parseFloat(max_repayment_installments)>0), text: parseInt(max_repayment_installments)"></span>
                    </p>
                </div>
            </div>
            <label class="col-lg-2 col-form-label">Paid every after<span class="text-danger">*</span></label>
            <div class="col-lg-2">
                <input class="form-control" type="number" id="paid_every" min="1" step="1" name="repayment_frequency" data-rule-mustbelessthanProductMaxLoanPeriod data-bind="value: $root.app_repayment_frequency" required>
            </div>
            <div class="col-lg-3">
                <select data-rule-mustbelessthanProductMaxLoanPeriod class="form-control" name="repayment_made_every" id="period_id" data-bind='options: $root.repayment_made_every_detail, optionsText: "made_every_name", optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"),optionsValue:"id", value: $root.app_repayment_made_every' required data-msg-required="This field is required">
                </select>
            </div>
        </div>

        <!--/row -->
        <div class="form-group row">
            <!--ko if: penalty_applicable==1  && parseInt(penalty_calculation_method_id) === 1 -->
            <label class="col-lg-3 col-form-label">Penalty rate<span class="text-danger">*</span></label>
            <div class="col-lg-3 form-group">
                <input min="0" step="0.01" required class="form-control" name="penalty_rate" type="number" data-bind='value: $root.app_penalty_rate, attr: {"data-rule-min":((parseFloat(min_penalty_rate)>0)?min_penalty_rate:null), "data-rule-max": ((parseFloat(max_penalty_rate)>0)?max_penalty_rate:null), "data-msg-min":"Penalty rate is less than "+parseFloat(min_penalty_rate), "data-msg-max":"Penalty rate is more than "+parseFloat(max_penalty_rate)}' required />
                <div class="blueText">
                    <p>
                        <span data-bind="visible: (parseFloat(min_penalty_rate)>0)">Min: </span>
                        <span data-bind="visible: (parseFloat(min_penalty_rate)>0), text: parseFloat(min_penalty_rate)"></span>&nbsp;
                        <span data-bind='visible: (parseFloat(max_penalty_rate)>0)'>Max: </span>
                        <span data-bind="visible: (parseFloat(max_penalty_rate)>0), text: parseFloat(max_penalty_rate)"></span>
                    </p>
                </div>
            </div>
            <!--/ko-->
            <!--ko if: penalty_applicable==1 && parseInt(penalty_calculation_method_id) === 2 -->
            <label class="col-lg-3 col-form-label">Penalty rate<span class="text-danger">*</span></label>
            <div class="col-lg-3 form-group">
                <input min="0" required class="form-control" name="penalty_rate" type="number" data-bind='value: $root.product_name().fixed_penalty_amount' required />

            </div>
            <!--/ko-->
            <input data-bind="value: link_toDeposit_account" name="link_to_deposit_account" id="link_to_deposit_account" type="hidden">
        </div>

        <!--/row -->
        <div class="form-group row" data-bind="if: penalty_applicable==1">
            <label class="col-lg-4 col-form-label">Penalty calculation method<span class="text-danger">*</span></label>
            <label class="col-lg-8 col-form-label" data-bind="text: method_description"></label>
            <input data-bind="value: penalty_calculation_method_id" name="penalty_calculation_method_id" id="penalty_calculation_method_id" type="hidden">
            <div class="clearfix"></div>
            <label class="col-lg-4 col-form-label">Penalty rate charged<span class="text-danger">*</span></label>
            <input data-bind="value: penalty_rate_chargedPer" name="penalty_rate_charged_per" id="penalty_rate_charged_per" type="hidden"></label><label class="col-lg-8 col-form-label" data-bind="text: (penalty_rate_chargedPer==1)?'Daily':((penalty_rate_chargedPer==2)?'Weekly':((penalty_rate_chargedPer==3)?'Monthly':'None'))"></label>
            <div class="clearfix"></div>
        </div>
        <!--/row -->

        <!--/ko-->


        <!--/ko -->
    </script>
</div>
<!--  /ko -->