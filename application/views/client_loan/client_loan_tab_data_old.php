<?php $principal_interest_bf_on_topup_loans = isset($org['principal_interest_bf_on_topup_loans']) ? $org['principal_interest_bf_on_topup_loans'] : false; ?>
<style type="text/css">
    h3 {
        font-weight: bold;
        color: #3c8dbc;
        font-size: 22px;
    }
</style>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <?php if ($type == "client_loan") { ?>
                <div class="ibox-title">
                    <ul class="breadcrumb">
                        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                        <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                    </ul>
                    <div class="pull-right add-record-btn">
                        <div id="reportrange" class="reportrange">
                            <i class="fa fa-calendar"></i>
                            <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if ($type == 'group_loan') { ?>
                <div class="ibox-title">
                    <ul class="breadcrumb">
                        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                        <li><a href="<?php echo site_url("group_loan"); ?>">Group Loans</a></li>
                        <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                    </ul>
                    <div class="pull-right add-record-btn">
                        <div id="reportrange" class="reportrange">
                            <i class="fa fa-calendar"></i>
                            <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="ibox-content">
                <div class="tabs-container">
                    <!-- <ul class="list-unstyled" >
                    <li class="dropdown pull-right">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" aria-expanded="false">
                            <i class="fa fa-modx"></i> Actions </a>
                        <ul class="dropdown-menu" x-placement="bottom-start" style="position: absolute; top: 39px; left: 0px; will-change: top, left;">
                        //<?php  //if (isset($requested_product['loan_type_id']) && $requested_product['loan_type_id'] ==1 ) {
                            //}else{
                            // if(in_array('1', $client_loan_privilege)){ 
                            ?>
                                <li><a href="#add_pending_approval-modal" data-toggle="modal"  class="btn btn-default btn-sm">
                                    <i class="fa fa-plus-circle"></i> New Application</a></li>
                           // <?php //} }
                                ?>
                            <li><a class="btn btn-default btn-sm" data-toggle="modal" data-target="#installment_payment-modal"><i class="fa fa-money"></i> Installment Payment </a></li>
                            <li><a class="btn btn-default btn-sm" data-toggle="modal" data-target="#loan_calculator-modal"><i class="fas fa-calculator"></i> Loan Calculator</a></li>
                        </ul>
                    </li>
                </ul> -->

                    <ul class="nav nav-tabs" role="tablist">
                        <?php if (isset($requested_product['loan_type_id']) && $requested_product['loan_type_id'] == 1) {
                        } else {
                            if (in_array('1', $client_loan_privilege)) { ?>
                                <!--  <li><a href="<?php //echo site_url("client_loan/add_loan")
                                                    ?>" class="nav-link btn btn-default btn-sm"><i class="fa fa-plus-circle"></i> New Application</a></li> -->
                                <!-- <li><a href="#add_pending_approval-modal" data-toggle="modal"  class="nav-link btn btn-default btn-sm">
                                    <i class="fa fa-plus-circle"></i> New Application</a></li> -->
                                <li><a href="#add_client_loan-modal" data-toggle="modal" class="nav-link btn btn-default btn-sm">
                                        <i class="fa fa-plus-circle"></i> New Application</a></li>
                            <?php }
                            if (in_array('13', $client_loan_privilege)) { ?>

                                <li><a class="nav-link btn btn-default btn-sm" data-toggle="modal" data-target="#installment_payment-modal"><i class="fa fa-money"></i> Installment Payment </a></li>
                                <li>
                                    <a class=" nav-link btn btn-sm btn-default" data-toggle="modal" data-target="#multiple_installment_payment-modal"><i class="fa fa-money"></i> Multiple Installment Payment</a>
                                </li>
                        <?php }
                        } ?>
                        <li><a class="nav-link btn btn-default btn-sm" data-toggle="modal" data-target="#loan_calculator-modal"><i class="fas fa-calculator"></i> Loan Calculator</a></li>
                        <?php if (in_array('19', $client_loan_privilege)) { ?>
                            <li><a href="#top_client_loan-modal" data-toggle="modal" class="nav-link btn btn-default btn-sm"><i class="fa fa-plus-circle"></i>Topup Application</a></li>
                        <?php }  ?>

                    </ul>
                    <hr class="hr-line-dashed">

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i>Active</a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-active">Active
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(7) -->
                                        <span class="badge bg-green" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a>
                                </li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-locked">Locked
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(12) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a>
                                </li>
                                <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-in_arrears">In Arrears
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(13) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a>
                                </li>
                                <!-- <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#">Rescheduled</a></li>
                               <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#">Re-financed</a></li> -->
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-defaulters"><i class=""></i> Defaulters</a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-risky_loans"><i class=""></i> Risking Loans</a></li>
                            </ul>
                        </li>
                        <li>
                            <a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-disbursed_loans">Disbursed
                            </a>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i>Loan Applications</a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-partial_application" data-bind="click: display_table">Partial Application
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(1) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <?php if ($org['loan_app_stage'] == 0) { ?>
                                    <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-pending_approval" data-bind="click: display_table">Pending Approval
                                            <!--ko foreach: $root.state_totals -->
                                            <!--ko if: state_id == parseInt(5) -->
                                            <span class="badge bg-red" data-bind="text: number">0</span>
                                            <!--/ko-->
                                            <!--/ko-->
                                        </a></li>
                                <?php }
                                if (($org['loan_app_stage'] == 0) || ($org['loan_app_stage'] == 1)) { ?>
                                    <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-approved" data-bind="click: display_table">Approved
                                            <!--ko foreach: $root.state_totals -->
                                            <!--ko if: state_id == parseInt(6) -->
                                            <span class="badge bg-green" data-bind="text: number">0</span>
                                            <!--/ko-->
                                            <!--/ko-->
                                        </a></li>
                                <?php }  ?>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-modx"></i> Closed </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-cleared">Obligation Met
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(10) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-paid_off">Paid Off
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(9) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-refinanced">Refinanced
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(14) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-written_off">Written Off
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(8) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-modx"></i> More </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-application_withdraw">Withdrawn
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(4) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-cancled">Cancelled
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(3) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-rejected">Rejected
                                        <!--ko foreach: $root.state_totals -->
                                        <!--ko if: state_id == parseInt(2) -->
                                        <span class="badge bg-red" data-bind="text: number">0</span>
                                        <!--/ko-->
                                        <!--/ko-->
                                    </a></li>
                            </ul>
                        </li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_fee"></i>Loan Fees</a></li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_payments"></i>Transactions</a></li>
                        <?php if ($org['mobile_payments'] == 1) { ?>
                            <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-pending_payout"></i>Payout Requests
                                    <!--ko foreach: $root.state_totals -->
                                    <!--ko if: state_id == parseInt(20) -->
                                    <span class="badge bg-red" data-bind="text: number">0</span>
                                    <!--/ko-->
                                    <!--/ko-->
                                </a></li>
                        <?php } ?>
                    </ul>

                    <div class="tab-content">
                        <?php $this->view('client_loan/states/pending/tab_view'); ?>
                        <?php $this->view('client_loan/states/payouts/tab_view'); ?>
                        <?php $this->view('client_loan/states/approved/tab_view'); ?>
                        <?php $this->view('client_loan/states/rejected/tab_view'); ?>
                        <?php $this->view('client_loan/states/cancled/tab_view'); ?>
                        <?php $this->view('client_loan/states/withdrawn/tab_view'); ?>
                        <?php $this->view('client_loan/states/partial/tab_view'); ?>
                        <?php $this->view('client_loan/states/active/tab_view'); ?>
                        <?php $this->view('client_loan/states/disbursed/tab_view'); ?>
                        <?php $this->view('client_loan/states/written_off/tab_view'); ?>
                        <?php $this->view('client_loan/states/paid_off/tab_view'); ?>
                        <?php $this->view('client_loan/states/locked/tab_view'); ?>
                        <?php $this->view('client_loan/states/obligations_met/tab_view'); ?>
                        <?php $this->view('client_loan/states/defaulters/tab_view'); ?>
                        <?php $this->view('client_loan/states/risky_loans/tab_view'); ?>
                        <?php $this->view('client_loan/states/in_arrears/tab_view'); ?>
                        <?php $this->view('client_loan/states/refinanced/tab_view'); ?>
                        <?php $this->view('client_loan/fees/tab_view'); ?>
                        <?php $this->view('client_loan/payments/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php //$this->view('client_loan/states/partial/add_modal'); 
?>
<?php $this->view('client_loan/states/approved/reverse_approval_modal'); ?>
<?php $this->view('client_loan/states/rejected/reject_modal'); ?>
<?php $this->view('client_loan/states/cancled/cancle_modal'); ?>
<?php $this->view('client_loan/states/withdrawn/application_withdraw_modal'); ?>
<?php $this->view('client_loan/states/partial/forward_application_modal'); ?>
<?php $this->view('client_loan/states/partial/reverse_action_modal'); ?>
<?php $this->view('client_loan/states/locked/lock_modal'); ?>
<?php $this->view('client_loan/states/written_off/write_off_modal'); ?>
<?php $this->view('client_loan/states/paid_off/pay_off_modal'); ?>
<?php $this->view('client_loan/states/partial/loan_calculator_modal'); ?>
<?php //$this->view('client_loan/fees/add_modal'); 
?>
<?php //$this->view('client_loan/fees/pay_fees_modal'); 
?>
<?php $this->view('client_loan/states/active/payment_modal');

if (($org['loan_app_stage'] == 0) || ($org['loan_app_stage'] == 1)) {
    $this->view('client_loan/states/active/disburse_loan_modal');
} elseif ($org['loan_app_stage'] == 2) {
    $this->load->view('client_loan/loan_steps_files/disbursement_modal.php');
}

if ($org['loan_app_stage'] == 0) {
    $this->view('client_loan/states/approved/approve_modal');
} elseif ($org['loan_app_stage'] == 1) {
    $this->view('client_loan/states/approved/approve_modal');
    // $this->load->view('client_loan/loan_steps_files/approve_modal.php');
}

?>
<?php
$this->view('client_loan/states/partial/steps_add_modal');
$this->view('client_loan/states/partial/top_up_modal.php'); ?>

<?php $this->view('client_loan/states/active/multiple_installments_payment'); ?>


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

        <!-- Interest amount to bring forward from parent loan -->

        <?php if($principal_interest_bf_on_topup_loans) { ?>
        <!-- ko if: $root.selected_active_loan() -->
        <label class="col-lg-3 col-form-label">B/F Interest amount</label>
        <div class="col-lg-3 form-group">
            <input min="0" class="form-control" name="interest_amount_bf" type="number" data-bind='value: $root.interest_amount_bf' />
            <div class="blueText">
                <p>Interest amount brought forward from parent loan</p>
            </div>
        </div>
        <!-- /ko -->
        <?php } ?>

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
        <!--ko if: penalty_applicable==1 && parseInt(penalty_calculation_method_id) === 1 -->
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
<script>
    $(document).ready(() => {
        $('#formInstallment_payment_multiple #loan_ref_no').select2();
  
    });
    let showSingleInstallmentForm = () => {
        $('#multiple_installment_payment-modal').modal('hide');
        $('#installment_payment-modal').modal('show');
    }
</script>