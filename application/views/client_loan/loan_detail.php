<?php //echo json_encode($loan_detail); die; 
?>
<style type="text/css">
    h3 {
        font-weight: bold;
        color: #3c8dbc;
        font-size: 22px;
    }

    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner-border {
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: .25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        -webkit-animation: spinner-border .75s linear infinite;
        animation: spinner-border .75s linear infinite;
    }

    .spinner-border-sm {
        height: 1rem;
        border-width: .2em;
    }
</style>

<div class="d-none hidden" id="client_loan_report_print_out">
</div>
<div class="d-none hidden" id="client_loan_disbursement_sheet_print_out">
</div>
<div id="div_loan_payments_print_out" style="display: none;"></div>

<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                    <li><a href="<?php echo site_url("client_loan"); ?>"><?php echo $this->lang->line('cont_client_name'); ?>
                            Loans</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                </ul>
            </div>
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active firsttab" data-toggle="tab" href="#tab-loan_details">Loan
                                Details</a></li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_ledger_card"><i class="fa fa-credit-card"></i> Ledger Card</a></li>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-repayment_schedule"><i class="fa fa-credit-card"></i> Loan Schedule</a></li>
                        <li data-bind="visible: (parseInt($root.loan_detail().state_id) >=7)"><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_installment_payment"><i class="fa fa-money"></i>Transactions</a></li>
                        <?php if (in_array('3', $client_loan_privilege)) { ?>
                            <li><a class="nav-link" data-toggle="tab" href="#tab-loan_fee" data-bind="click: display_table">Fees</a></li><?php } ?>
                        <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_docs"><i class="fa fa-file"></i> Loan Docs</a></li>


                        <!-- <li class="dropdown dropdown-active">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-modx"></i>Security </a>
                            <ul class="nav-link dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-guarantor">Guarantor</a></li>
                                <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-collateral">Collateral</a></li>
                            </ul>
                        </li> -->
                        <?php if (in_array('6', $modules)) { ?>
                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i>
                                    Security </a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-collateral">Collateral</a></li>
                                    <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-guarantors">Guarantor</a></li>
                                    <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-guarantor">Guarantor Savings</a></li>

                                </ul>
                            </li>
                        <?php } else { ?>

                            <li class="dropdown">
                                <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i>
                                    Security </a>
                                <ul class="dropdown-menu">
                                    <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-guarantors">Guarantor</a></li>
                                    <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-collateral">Collateral</a></li>
                                </ul>
                            </li>
                        <?php } ?>

                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-modx"></i> More
                            </a>
                            <ul class="dropdown-menu">
                                <?php if (($org['loan_app_stage'] == 0) || ($org['loan_app_stage'] == 1)) { ?>
                                    <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_approvals"><i class="fa fa-modx"></i> Approvals</a></li>
                                <?php } ?>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_docs"><i class="fa fa-file"></i> Loan Docs</a></li>
                                <?php if ((in_array('6', $modules)) && (in_array('5', $modules))) { ?>
                                    <li><a class="nav-link" data-toggle="tab" href="#tab-loan_attached_saving_acc" data-bind="click: display_table"> <i class="fa fa-modx"></i> Attached
                                            Accounts</a></li>
                                <?php } ?>
                                <!-- <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab"
                                        href="#tab-monthly_expense"><i class="fa fa-modx"></i> Monthly-Expenses</a></li> -->
                                <!-- <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab"
                                        href="#tab-monthly_income"><i class="fa fa-modx"></i> Monthly-Income</a></li> -->
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_history"><i class="fa fa-file"></i> Loan History</a></li>

                            </ul>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-loan_details" class="tab-pane active">
                            <div class="panel-body">
                                <div class="pull-left add-record-btn">
                                    <div class="panel-title">
                                        <h3 data-bind="text:($root.loan_detail().group_name&&$root.loan_detail().member_name)?$root.loan_detail().group_name+', '+ $root.loan_detail().member_name+'\'s Loan Details':($root.loan_detail().group_name&&!$root.loan_detail().member_name)?$root.loan_detail().group_name+'\'s Loan Details':$root.loan_detail().member_name+'\'s Loan Details'">
                                        </h3>
                                    </div>
                                </div>
                                <div style="padding-left: 12em" class="pull-left add-record-btn">
                                    <h3 class="btn btn-success btn-sm " data-bind="text: ($root.loan_detail().state_name)?((!($root.loan_detail().action_date=='0000-00-00'))?$root.loan_detail().state_name+' on '+moment($root.loan_detail().action_date,'YYYY-MM-DD').format('DD-MMM-YYYY'):$root.loan_detail().state_name):''">
                                    </h3>
                                </div>
                                <!--   <div style=" font-size: 0.9em; font-weight: bold; text-align: center;"
                                                    class="text-danger">  
                                                    NOTE Making payments from this page has been temporarily disabled, Please make payments from the Loan list page 
                                                   
                                                </div> -->
                                <!-- ko if: parseInt($root.loan_detail().topup_application)==1 -->
                                <div style="padding-left: 12em" class="pull-left add-record-btn">
                                    <h3 class="btn btn-info btn-sm"> Top up Loan</h3>
                                </div>
                                <!-- /ko-->
                                <div style="padding-left: 2em;" class="pull-right add-record-btn">
                                    <?php
                                    if (in_array('3', $client_loan_privilege)) { ?>
                                        <!--ko if: (parseInt($root.loan_detail().state_id) <= 6) -->
                                        <a href="#add_pending_approval-modal" data-bind="click: initialize_edit" data-toggle="modal" class="btn btn-primary btn-sm pull-right">
                                            <i class="fa fa-edit"></i> Update Detail</a>
                                        <!--/ko-->
                                    <?php }  ?>

                                    <?php

                                    if ($org['loan_app_stage'] == 0) {
                                        if (in_array('20', $client_loan_privilege)) { ?>
                                            <!--ko if: (parseInt($root.loan_detail().state_id) == 1) -->
                                            <a data-toggle="modal" data-bind="click: action_on_loan" data-target="#forward_application-modal" class="btn btn-sm"><i class='fa fa-forward' aria-hidden='true'></i> Forward Application</a>
                                            <!--/ko-->
                                        <?php }

                                        if (in_array('22', $client_loan_privilege)) { ?>
                                            <!--ko if: (parseInt($root.loan_detail().state_id) == 6) -->
                                            <!-- <a data-toggle="modal" data-bind="click: disburse" data-target="#disburse-modal"
                                        class="btn btn-sm"><i class='text-success fa fa-money fa-fw'></i> Disburse
                                        Loan</a> -->
                                            <!--/ko-->
                                        <?php }

                                        if (in_array('14', $client_loan_privilege)) { ?>
                                            <!--ko if: (parseInt($root.loan_detail().state_id) == 5) -->
                                            <a data-toggle="modal" data-bind="click: approve_loan" data-target="#approve-modal" class="btn btn-sm"><i class='fa fa-check-square-o' style='font-size:16px'></i>
                                                Approve Application</a>
                                            <!--/ko-->
                                        <?php }
                                    } elseif ($org['loan_app_stage'] == 1) {
                                        if (in_array('14', $client_loan_privilege)) { ?>
                                            <!--ko if: (parseInt($root.loan_detail().state_id) == 1) -->
                                            <!--  <a data-toggle="modal" data-bind="click: approve_loan" data-target="#approve-modal"
                                        class="btn btn-sm"><i class='fa fa-check-square-o' style='font-size:16px'></i>
                                        Approve Application</a> -->
                                            <!--/ko-->
                                        <?php }
                                    } elseif ($org['loan_app_stage'] == 2) {
                                        if (in_array('22', $client_loan_privilege)) { ?>
                                            <!--ko if: (parseInt($root.loan_detail().state_id) == 1) -->
                                            <a data-toggle="modal" data-bind="click: disburse" data-target="#disburse-modal" class="btn btn-sm"><i class='text-success fa fa-money fa-fw'></i> Disburse
                                                Loan</a>
                                            <!--/ko-->
                                        <?php }
                                    }

                                    if (in_array('20', $client_loan_privilege)) { ?>

                                    <?php }
                                    if (in_array('10', $client_loan_privilege)) { ?>
                                        <!--ko if: (parseInt($root.loan_detail().state_id) == 1) -->
                                        <a data-toggle="modal" data-bind="click: action_on_loan" data-target="#reject-modal" class="btn btn-sm"> <i class='text-danger fa fa-ban'></i> Reject Application</a>
                                        <!--/ko-->
                                    <?php }
                                    if (in_array('12', $client_loan_privilege)) { ?>
                                        <!--ko if: ((parseInt($root.loan_detail().state_id) == 1) || (parseInt($root.loan_detail().state_id) == 5)) -->
                                        <a data-toggle="modal" data-bind="click: action_on_loan" data-target="#application_withdraw-modal" class="btn btn-sm"><i class='text-danger fa fa-undo'></i> Withdraw Application</a>
                                        <!--/ko-->
                                    <?php }
                                    if (in_array('11', $client_loan_privilege)) { ?>
                                        <!--ko if: ((parseInt($root.loan_detail().state_id) == 2) || (parseInt($root.loan_detail().state_id) == 4)) -->
                                        <a data-toggle="modal" data-bind="click: action_on_loan" data-target="#cancle-modal" class="btn btn-sm"> <i class='text-danger fa fa-times'></i> Cancel
                                            Application</a>
                                        <!--/ko-->
                                    <?php }
                                    if (in_array('18', $client_loan_privilege)) { ?>
                                        <!--ko if: (parseInt($root.loan_detail().state_id) == 7) -->
                                        <a data-toggle="modal" data-bind="click: action_on_loan" data-target="#lock-modal" class="btn btn-sm"><i class='text-danger fa fa-lock fa-fw'></i> Lock Loan
                                            A/C</a>
                                        <!--/ko-->
                                    <?php }
                                    if (in_array('18', $client_loan_privilege) && in_array('14', $client_loan_privilege)) { ?>
                                        <!--ko if: (parseInt($root.loan_detail().state_id) == 6) -->
                                        <a data-toggle="modal" data-bind="click: approve_loan" data-target="#approve-modal" class="btn btn-sm"><i class='text-danger fa fa-lock fa-fw'></i> Approve Loan</a>
                                        <!--/ko-->
                                    <?php }
                                    if (in_array('21', $client_loan_privilege)) { ?>
                                        <!--ko if: (parseInt($root.loan_detail().state_id) == 6) -->
                                        <a data-toggle="modal" data-bind="click: action_on_loan" data-target="#reverse_approval-modal" class="btn btn-sm"><i class='text-danger fa fa-undo fa-fw'></i> Reverse Approval</a>
                                        <!--/ko-->
                                        <!--ko if: ((parseInt($root.loan_detail().state_id) != 1) && (parseInt($root.loan_detail().state_id) < 6)) -->
                                        <a data-toggle="modal" data-bind="click: action_on_loan" data-target="#reverse_action-modal" class="btn btn-sm"><i class='text-danger fa fa-undo fa-fw'></i> Reverse Action</a>
                                        <!--/ko-->
                                    <?php }
                                    if (in_array('13', $client_loan_privilege)) { ?>
                                        <!--ko if: (parseInt($root.loan_detail().state_id) == 7 || parseInt($root.loan_detail().state_id) == 13) -->

                                        <a class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#multiple_installment_payment-modal"><i class="fa fa-money"></i>
                                            Multiple Installment Payment</a>
                                        <a class="btn btn-sm btn-primary text-white" data-toggle="modal" data-target="#installment_payment-modal"><i class="fa fa-money"></i> Payment</a>
                                        <!--/ko-->
                                    <?php }  ?>
                                    <?php
                                    if (in_array('6', $client_loan_privilege)) {
                                    ?>
                                        <!--ko if: (parseInt($root.loan_detail().state_id) == 6) -->
                                        <button onclick="print_loan_agreement()" class="btn btn-primary btn-sm pull-right" style="color:#fff; margin-right: 0.2em"> <i class="fa fa-print "></i> Loan Agreement</button>
                                        <!--/ko-->
                                    <?php } ?>
                                    <?php
                                    if (in_array('12', $client_loan_privilege)) {
                                    ?>
                                        <!--ko if: ( parseInt($root.loan_detail().state_id) == 5) -->
                                        <button onclick="print_loan_application()" class="btn btn-primary btn-sm pull-right" style="color:#fff; margin-right: 0.2em"> <i class="fa fa-print "></i> Loan Application Form</button>
                                        <!--/ko-->
                                    <?php } ?>

                                    <?php if (in_array('13', $client_loan_privilege) || in_array('21', $client_loan_privilege) || in_array('22', $client_loan_privilege) || in_array('18', $client_loan_privilege) || in_array('14', $client_loan_privilege) || in_array('11', $client_loan_privilege) || in_array('12', $client_loan_privilege) || in_array('10', $client_loan_privilege) || in_array('20', $client_loan_privilege)) {
                                    ?>

                                        <!--ko if: (parseInt($root.loan_detail().state_id) > 7 && parseInt($root.loan_detail().state_id) !=13 ) -->
                                        <a data-toggle="modal" data-target="#" class="btn btn-sm">No Action</a>
                                        <!--/ko-->

                                    <?php } else { ?>
                                        <a data-toggle="modal" data-target="#" class="btn btn-sm">No Privelges</a>
                                    <?php } ?>

                                    <?php if (in_array('6', $client_loan_privilege)) { ?>
                                        <button style="margin-right: 2em;" onclick="print_client_loan_report()" class="btn btn-success btn-sm"> <i class="fa fa-print fa-2x"></i> </button>
                                    <?php } ?>
                                </div>
                                <!-- <div data-bind="visible: (parseInt($root.loan_detail().state_id) < 6)" style="padding-left: 2em;" class="pull-right add-record-btn">
                                            <a href='<?php echo base_url(); ?>client_loan/pdf_appraisal/<?php echo $loan_detail['id']; ?>/<?php echo $loan_detail['loan_no']; ?>' target = '_blank' class="btn btn-default btn-sm pull-right" ><i class="fa fa-file"></i> Appraisal doc </a>
                                    </div> -->
                                <!-- <li data-bind="visible: false">
                                        <div data-bind="visible: (parseInt($root.loan_detail().state_id) == 7)" style="padding-left: 2em;" class="pull-right add-record-btn">
                                            <a href='<?php echo base_url(); ?>client_loan/pdf_loan_fact_sheet/<?php echo $loan_detail['id']; ?>/<?php echo $loan_detail['loan_no']; ?>' target = '_blank' class="btn btn-default btn-sm pull-right" ><i class="fa fa-file"></i> Loan Fact Sheet </a>
                                        </div>                             
                                    </li> -->



                                <table class="table table-bordered table-hover table-user-information  table-stripped  m-t-md">

                                    <tbody data-bind="with: loan_detail">
                                        <tr>
                                            <td>
                                                <!--ko if: (parseInt($root.loan_detail().state_id) >= 7) -->
                                                <button href="#adjust_penalty_modal" data-toggle="modal" class="btn btn-danger btn-sm">
                                                    <i class="fa fa-edit"></i> Adjust Penalty Payable
                                                </button>
                                                <!--/ko-->
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Ref #</strong></td>
                                            <td colspan="2">
                                                <!-- ko if: '<?php echo !empty($loan_detail['group_loan_no']) ? $loan_detail['group_loan_no'] : ''; ?>' -->
                                                <span data-bind="text: group_loan_no"></span> [ <span data-bind="text: loan_no"></span> ]
                                                <!-- /ko -->
                                                <!-- ko ifnot: '<?php echo !empty($loan_detail['group_loan_no']) ? $loan_detail['group_loan_no'] : ''; ?>' -->
                                                <span data-bind="text: loan_no"></span>
                                                <!-- /ko -->
                                            </td>
                                            <td><strong>Client name</strong></td>
                                            <td colspan="3"><span data-bind="text:(group_name&&member_name)?group_name+', '+ member_name:(group_name&&!member_name)?group_name:member_name"></span>
                                            </td>
                                            <td><strong>Credit officer</strong></td>
                                            <td colspan="5"><span data-bind="text: credit_officer_name"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Product name</strong></td>
                                            <td colspan="2"><span data-bind="text: product_name"></span></td>

                                            <td><strong>Requested amount</strong></td>
                                            <td colspan="3"><span data-bind="text: 'UGX '+curr_format(requested_amount*1)"></span>
                                            </td>
                                            <td><strong>Disbursed Amount</strong></td>
                                            <td colspan="3"><span data-bind="text: 'UGX '+curr_format(disbursed_amount*1)"></span>
                                            </td>
                                        </tr>
                                        <tr>

                                            <td><strong>Application date</strong></td>
                                            <td colspan="2"><span data-bind="text: (!(application_date=='0000-00-00'))?moment(application_date,'YYYY-MM-DD').format('DD-MMM-YYYY'):'No Date'"></span>
                                            </td>
                                            <td><strong>Interest rate</strong></td>
                                            <td colspan="3"><span data-bind="text: interest_rate +'%'"></span></td>
                                            <!-- calculated_interest_rate -->
                                            <!-- ko ifnot: '<?php echo !empty($loan_detail['group_loan_no']) ? $loan_detail['group_loan_no'] : ''; ?>' -->
                                            <td><strong>Interest Calculated On</strong></td>
                                            <td colspan="5"><span data-bind="text: (type_name)?type_name:''"></span>
                                            </td>
                                            <!--/ko -->
                                            <!-- ko if: '<?php echo !empty($loan_detail['group_loan_no']) ? $loan_detail['group_loan_no'] : ''; ?>' -->
                                            <td><strong>Type</strong></td>
                                            <td colspan="5"><span data-bind="text: (type_name)?type_name:''"></span>
                                            </td>
                                            <!--/ko -->
                                        </tr>
                                        <tr>
                                            <td><strong>Loan Security</strong></td>
                                            <td colspan="2"><span data-bind="text: (min_collateral)?(min_collateral*1)+'%':''"></span>
                                            </td>
                                            <td><strong>offset period</strong></td>
                                            <td colspan="3"><span data-bind="text: (offset_period)?((offset_every)?offset_period+' '+offset_every:''):''"></span>
                                            </td>
                                            <td><strong>Repayment frequency</strong></td>
                                            <td colspan="5"><span data-bind="text: (repayment_frequency)?((made_every_name)?repayment_frequency+' '+made_every_name:''):''"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Installments</strong></td>
                                            <td colspan="2"><span data-bind="text: installments"></span></td>
                                            <td><strong>Penalty Tolerance(Grace) Period</strong></td>
                                            <td colspan="3"><span data-bind="text: (grace_period)?grace_period:0 +' Day(s)'"></span>
                                            </td>
                                            <td><strong>Penalty charged</strong></td>
                                            <td colspan="5"><span data-bind="text: (penalty_rate_charged_per==1)?'Daily':((penalty_rate_charged_per==2)?'Weekly':((penalty_rate_charged_per==3)?'Monthly': 
                                                    ((penalty_rate_charged_per==4) ? 'Once (One time)' : 'None' )
                                                    ))"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <!-- ko if: parseInt(penalty_calculation_method_id) == 1 -->
                                            <td><strong>Penalty rate</strong></td>
                                            <td colspan="2"><span data-bind="text: (penalty_rate*1) +  '%' "></span>
                                            </td>
                                            <!-- /ko -->
                                            <!-- ko if: parseInt(penalty_calculation_method_id) == 2 -->
                                            <td><strong>Penalty Value</strong></td>
                                            <td colspan="2"><span data-bind="text: curr_format(fixed_penalty_amount*1) "></span>
                                            </td>
                                            <!-- /ko -->

                                            <td><strong>Penalty calculation method</strong></td>
                                            <td colspan="8"><span data-bind="text: method_description"></span></td>
                                        </tr>
                                        <!-- ko if: parseInt(preferred_payment_id)==1 -->
                                        <tr>
                                            <td><strong>Payment Option</strong></td>
                                            <td colspan="2"><span data-bind="text: payment_mode"></span></td>
                                            <td><strong>Loan Purpose</strong></td>
                                            <td colspan="8"><span data-bind="text: (loan_purpose)?loan_purpose:'None'"></span></td>
                                        </tr>
                                        <!-- /ko -->
                                        <!-- ko if: parseInt(preferred_payment_id)==2 -->
                                        <tr>
                                            <td><strong>Payment Option</strong></td>
                                            <td colspan="2"><span data-bind="text: payment_mode"></span></td>
                                            <td><strong>A/C Number</strong></td>
                                            <td colspan="3"><span data-bind="text: ac_number"></span></td>
                                            <td><strong>A/C Name</strong></td>
                                            <td colspan="5"><span data-bind="text: (ac_name)?ac_name:'None'"></span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bank Branch</strong></td>
                                            <td colspan="2"><span data-bind="text: bank_branch"></span></td>
                                            <td><strong>Bank Name</strong></td>
                                            <td colspan="3"><span data-bind="text: bank_name"></span></td>
                                            <td><strong>Loan Purpose</strong></td>
                                            <td colspan="5"><span data-bind="text: (loan_purpose)?loan_purpose:'None'"></span></td>
                                        </tr>
                                        <!-- /ko -->
                                        <!-- ko if: parseInt(preferred_payment_id)==4 -->
                                        <tr>
                                            <td><strong>Payment Option</strong></td>
                                            <td colspan="2"><span data-bind="text: payment_mode"></span></td>
                                            <td><strong>Phone Number</strong></td>
                                            <td colspan="3"><span data-bind="text: phone_number"></span></td>
                                            <td><strong>Loan Purpose</strong></td>
                                            <td colspan="5"><span data-bind="text: (loan_purpose)?loan_purpose:'None'"></span></td>
                                        </tr>
                                        <!-- /ko -->
                                        <tr>
                                            <td><strong>Comment</strong></td>
                                            <td colspan="10"><span data-bind="text: (comment)?comment:'None'"></span></td>
                                        </tr>
                                        <!-- ko if: parseInt(topup_application)==1 -->
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan="5">
                                                <div class="col-lg-12 form-group">
                                                    <h4 data-bind="visible: ((typeof member_name !=='undefined') && (linked_loan_id !=null)) ">
                                                        <a data-bind="attr:{href: '<?php echo site_url('client_loan/view'); ?>'+'/'+linked_loan_id}" target="_blank" title='View this Loan details'>To view the
                                                            parent loan details click here</a>
                                                    </h4>
                                                    <h4 data-bind="visible: ((typeof group_name !=='undefined') && (group_loan_id !=null) ) ">
                                                        <a data-bind="attr:{href: '<?php echo site_url('client_loan/view'); ?>'+'/'+group_loan_id+'/1'}" target="_blank" title='View this Loan details'>To view
                                                            parent loan details click here</a>
                                                    </h4>
                                                </div>
                                            </td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <!-- /ko -->
                                        <tr>
                                            <td colspan="12">
                                                <h3>
                                                    <center>
                                                        <strong>Current Loan Balances </strong>
                                                    </center>
                                                </h3>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="2">
                                                <h2>
                                                    <strong>Principal: </strong>
                                                    <span class="text-danger" style="font-weight: bold;" data-bind="text: (expected_principal)?curr_format(parseFloat(expected_principal)-parseFloat(paid_principal)):0"></span>
                                                </h2>
                                            </td>
                                            <td colspan="2">
                                                <h2>
                                                    <strong>Interest: </strong>
                                                    <span class="text-danger" style="font-weight: bold;" data-bind="text: (expected_interest)?curr_format(parseFloat(expected_interest)-parseFloat(paid_interest)):0"></span>
                                                </h2>
                                            </td>
                                            <td colspan="2">
                                                <h2>
                                                    <strong>Penalty: </strong>
                                                    <span class="text-danger" style="font-weight: bold;" data-bind="text: (total_penalty)?curr_format(total_penalty):0"></span>
                                                </h2>
                                            </td>
                                            <td colspan="3">
                                                <h2>
                                                    <strong>Total: </strong>
                                                    <span class="text-danger" style="font-weight: bold;" data-bind="text: (expected_principal)?curr_format(round((parseFloat(expected_principal)+parseFloat(expected_interest)+parseFloat(total_penalty))-parseFloat(paid_amount)),2):0"></span>
                                                </h2>
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end of loan_details -->
                        <?php if (in_array('15', $modules)) {
                            $this->view('client_loan/security/guarantor/tab_view');
                        } ?>
                        <?php $this->view('client_loan/security/collateral/tab_view'); ?>
                        <?php
                        $this->view('client_loan/fees/tab_view');
                        ?>
                        <?php if ((in_array('6', $modules)) && (in_array('5', $modules))) {
                            $this->view('client_loan/loan_attached_saving_accounts/tab_view');
                        } ?>
                        <?php $this->view('client_loan/repayment_schedule/tab_view'); ?>
                        <?php $this->view('client_loan/loan_ledger_card/tab_view'); ?>
                        <?php $this->view('client_loan/loan_docs/tab_view'); ?>
                        <?php $this->view('client_loan/approval/tab_view'); ?>
                        <?php $this->view('client_loan/history/tab_view'); ?>
                        <?php $this->view('client_loan/income_and_expense/income/tab_view'); ?>
                        <?php $this->view('client_loan/income_and_expense/expense/tab_view'); ?>
                        <?php $this->view('client_loan/loan_transactions/tab_view'); ?>
                        <?php $this->view('client_loan/guarantors/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (in_array('15', $modules)) {
    $this->view('client_loan/security/guarantor/add_modal');
} ?>
<?php $this->view('client_loan/security/collateral/add_modal'); ?>
<?php $this->view('client_loan/security/collateral/attach_existing'); ?>
<?php $this->view('client_loan/guarantors/add_modal'); ?>

<?php $this->view('client_loan/adjust_penalty_modal'); ?>
<?php $this->view('client_loan/fees/add_modal'); ?>
<?php $this->view('client_loan/fees/pay_fees_modal'); ?>
<?php if ((in_array('6', $modules)) && (in_array('5', $modules))) {
    $this->view('client_loan/loan_attached_saving_accounts/add_modal');
} ?>
<?php $this->view('client_loan/loan_docs/add_modal'); ?>
<?php $this->view('client_loan/states/partial/add_modal'); ?>
<?php $this->view('client_loan/states/approved/reverse_approval_modal'); ?>
<?php $this->view('client_loan/states/rejected/reject_modal'); ?>
<?php $this->view('client_loan/states/cancled/cancle_modal'); ?>
<?php $this->view('client_loan/states/withdrawn/application_withdraw_modal'); ?>
<?php $this->view('client_loan/states/partial/forward_application_modal'); ?>
<?php $this->view('client_loan/states/partial/reverse_action_modal'); ?>
<?php $this->view('client_loan/states/locked/lock_modal'); ?>
<?php $this->view('client_loan/states/written_off/write_off_modal'); ?>
<?php $this->view('client_loan/states/paid_off/pay_off_modal'); ?>
<?php $this->view('client_loan/repayment_schedule/reschedule_modal'); ?>
<?php $this->view('client_loan/income_and_expense/income/add_modal'); ?>
<?php $this->view('client_loan/income_and_expense/expense/add_modal'); ?>
<?php $this->view('client_loan/states/active/multiple_installments_payment'); ?>
<?php $this->view('client_loan/states/active/payment_modal');

$this->view('client_loan/printer_modal');
$this->view('client_loan/disbursement_printer_modal');

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

<?php $this->view('client_loan/loan_transactions/edit_transaction_date_modal'); ?>

<script>
    var dTable = {};
    var loanDetailModel = {};
    var TableManageButtons = {};
    var allMoneyInputs = [];
    $(document).ready(function() {
        var periods = ['days', 'weeks', 'months'];
        var loan_product_length = '';
        var LoanFee = function() {
            var self = this;
            self.selected_fee = ko.observable();
        };

        var PayLoanFee = function() {
            var self = this;
            self.selected_fee = ko.observable();
        };
        var SavingsAccount = function() {
            var self = this;
            self.selected_fee = ko.observable();
        };

        $(".select2able").select2({
            allowClear: true
        });
        $("#relationship_type_id").select2({
            dropdownParent: $("#add_guarantor-modal")
        });
        $("#savings_account_id").select2({
            dropdownParent: $("#add_guarantor-modal")
        });
        $("#credit_officer_id").select2({
            dropdownParent: $("#add_pending_approval-modal")
        });
        $("#loan_product_id").select2({
            dropdownParent: $("#add_pending_approval-modal")
        });

        //$('form#formClient_loan').validate({submitHandler: saveData2});
        $("form#formClient_loan").validate({
            rules: {
                installments: {
                    mustbelessthanProductMaxLoanPeriod: true
                },
                repayment_frequency: {
                    mustbelessthanProductMaxLoanPeriod: true
                },
                repayment_made_every: {
                    mustbelessthanProductMaxLoanPeriod: true
                }
            },
            submitHandler: saveData2
        });
        <?php if (in_array('15', $modules)) { ?>
            $('form#formClient_loan_guarantor').validate({
                submitHandler: saveData2
            });
        <?php } ?>
        $('form#formClient_loan_doc').validator().on('submit', saveData);
        $('form#formLoan_collateral').validator().on('submit', saveData);
        $('form#formGuarantor').validator().on('submit', saveData);
        $('form#edit_loan_trans_date').validator().on('submit', saveData);
        $('form#form_adjust_penalty_modal').validator().on('submit', saveData);

        $("form#formApprove").validate({
            rules: {
                approved_installments: {
                    mustbelessthantheProductMaxLoanPeriod: true
                },
                approved_repayment_frequency: {
                    mustbelessthantheProductMaxLoanPeriod: true
                },
                approved_repayment_made_every: {
                    mustbelessthantheProductMaxLoanPeriod: true
                }
            },
            submitHandler: saveData2
        });
        $('form#formReject').validator().on('submit', saveData);
        $('form#formCancle').validator().on('submit', saveData);
        $('form#formApplication_withdraw').validator().on('submit', saveData);
        $('form#formForward_application').validator().on('submit', saveData);
        $('form#formActive').validate({
            rules: {
                fund_source_account: {
                    remote: {
                        url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                        type: "post",
                        data: {
                            amount: function() {
                                return $("form#formActive input[name='amount_approved']").val();
                            },
                            account_id: function() {
                                return $("form#formActive input[name='source_fund_account_id']").val();
                            }
                        }
                    }
                }

            },
            submitHandler: <?php if ($org['mobile_payments'] == 1) { ?>saveData9 <?php } else { ?>saveData2<?php } ?>
        });
        $('form#formLock').validator().on('submit', saveData);
        $('form#formWrite_off').validator().on('submit', saveData);
        $('form#formPay_off').validator().on('submit', saveData);
        $('form#formReverse').validator().on('submit', saveData);
        $('form#formReverse_approval').validator().on('submit', saveData);
        $('form#formReschedule_payment').validate({
            submitHandler: saveData2
        });
        $('form#formLoan_fee_application').validator().on('submit', saveData);
        <?php if ((in_array('6', $modules)) && (in_array('5', $modules))) { ?>
            $('form#formLoan_detail_saving_accounts').validator().on('submit', saveData);
        <?php } ?>
        $('form#formInstallment_payment').validate({
            submitHandler: saveData2,
            //your validation rules
            ignore: ':hidden:not(.do-not-ignore)',
            errorPlacement: function(error, element) {
                if (element.attr("name") == "extra_principal") {
                    error.insertBefore($(".loan_curtailment_error"));
                } else if (element.attr("name") == "extra_amount_use") {
                    error.insertAfter($(".after-p"));

                } else {
                    error.insertAfter(element);
                }
            }
        });
        $('form#formInstallment_payment_multiple').validate({
            submitHandler: saveData2,
            ignore: ':hidden:not(.do-not-ignore)'
        });

        $('form#formClient_loan_monthly_expense').validator().on('submit', saveData);
        $('form#formClient_loan_monthly_income').validator().on('submit', saveData);

        var MemberGuarantor = function() {
            var self = this;
            self.selected_member_guarantor = ko.observable();
        };

        var LoanDetailModel = function() {
            var self = this;
            self.client_type = ko.observable("<?php echo isset($client_type) ? $client_type : 1; ?>");
            self.selected_ac = ko.observable();
            self.trans_channel = ko.observable();
            self.display_table = function(data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.loan_detail = ko.observable(<?php echo json_encode($loan_detail); ?>);
            self.loan_details = ko.observable();
            self.group_loan_details = ko.observable(<?php if (isset($group_loan_details)) {
                                                        echo json_encode($group_loan_details);
                                                    } ?>);
            self.selected_product = ko.observable();
            //rescheduling a loan
            self.schedule_detail = ko.observable();
            self.interest_rate = ko.observable();
            self.top_up_amount = ko.observable();
            self.current_installment = ko.observable();
            self.repayment_frequency = ko.observable();
            self.installments = ko.observable();
            self.new_date = ko.observable('<?php echo date('d-m-Y'); ?>');
            self.compute_interest_from_disbursement_date = ko.observable("0");
            self.compute_interest_from_disbursement_date.subscribe(() => {
                let dataobj = {
                    new_repayment_date: self.new_date()
                };
                get_new_schedule(dataobj, 2);
            });

            //paying for the loan
            self.payment_date = ko.observable('<?php //echo date('d-m-Y'); ?>');
            self.payment_details = ko.observable();
            self.penalty_amount = ko.observable();
            self.extra_amount_available = ko.observable(0);


            self.repayment_made_every_detail = ko.observableArray(
                <?php echo json_encode($repayment_made_every); ?>);
            self.repayment_made_every = ko.observable();

            self.payment_summation = ko.observable();
            self.pay_off_data = ko.observable();
            self.approval_data = ko.observable();
            self.payment_schedule = ko.observableArray();
            self.product_names = ko.observable(<?php echo json_encode($loanProducts); ?>);
            self.product_name = ko.observable();

            self.guarantors = ko.observable(<?php echo json_encode($guarantors); ?>);
            self.guarantor = ko.observable();

            self.income_items = ko.observable(<?php echo json_encode($income_items); ?>);
            self.income_item = ko.observable();

            self.expense_items = ko.observable(<?php echo json_encode($expense_items); ?>);
            self.expense_item = ko.observable();

            self.savings_accs = ko.observable(<?php echo json_encode($savings_accs); ?>);
            self.savings_accs_member = ko.observable(
                <?php echo (!empty($savings_accs_member) ? json_encode($savings_accs_member) : (!empty($savings_accs) ? json_encode($savings_accs) : '')) ?>
            );
            self.savings_acc = ko.observable();

            self.guarantor_amount = ko.observable(0);
            self.loan_type = ko.observable('1');
            self.collateral_amount = ko.observable(0);
            self.available_guarantors = ko.observableArray();
            self.available_loan_fees = ko.observableArray(
                <?php echo (!empty($available_loan_fees) ? json_encode($available_loan_fees) : '') ?>);
            self.unpaid_loan_fees = ko.observableArray(
                <?php echo (!empty($unpaid_loan_fees) ? json_encode($unpaid_loan_fees) : '') ?>);
            self.payment_modes = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
            self.approval_date = ko.observable('<?php //echo date('d-m-Y'); 
                                                ?>');
            self.suggested_disbursement_date = ko.observable('<?php //echo date('d-m-Y'); 
                                                                ?>');

            self.pay_with = ko.observable(<?php echo (isset($pay_with)) ? json_encode($pay_with) : ''; ?>);

            self.member_names = ko.observable(<?php echo json_encode($members); ?>);
            self.member_name = ko.observable();
            self.member_id = ko.observable(
                <?php echo isset($loan_detail['member_id']) ? $loan_detail['member_id'] : ''; ?>);

            self.filtered_member_names = ko.computed(() => {
                if (self.member_name()) {
                    return self.member_names().filter(m => parseInt(m.id) !== parseInt(self
                        .member_name().id));

                }

                if (self.member_names() && self.member_id()) {
                    return self.member_names().filter(m => parseInt(m.id) !== parseInt(self
                        .member_id()));
                }
                return [];
            }, self);

            self.loan_collaterals = ko.observableArray([]);
            self.member_collaterals = ko.observableArray([]);

            self.existing_collateral = ko.observableArray([]);

            self.filter_member_collateral = ko.computed(function() {
                let filtered = self.member_collaterals().filter(m_col => {
                    return self.loan_collaterals().filter(l_col => l_col.member_collateral_id ==
                        m_col.id).length === 0;
                });
                self.existing_collateral(filtered);
            }, self);

            self.added_existing_collateral = ko.observableArray([

            ]);

            self.remove_existing_collateral = function(selected_col) {
                self.added_existing_collateral.remove(selected_col);
                self.existing_collateral.push(selected_col);
            }
            self.add_existing_collateral = function(new_collateral) {
                self.added_existing_collateral.push(new_collateral);
                self.existing_collateral.remove(new_collateral);
            }

            self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
            //Guarantor types
            self.guarantor_types = ko.observable([{
                "id": 1,
                "guarantor_name": "Existing member"
            }, {
                "id": 2,
                "guarantor_name": "Not a member"
            }]);
            self.guarantor_name = ko.observable();


            //paying for the loan
            self.installment_payment_date = ko.observable('<?php //echo date('d-m-Y'); 
                                                            ?>');
            self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
            self.tchannels = ko.observable();
            self.payment_data = ko.observable();
            self.next_payment_data = ko.observable(); // next installment data

            // Principal or Interest First
            self.interest_first = ko.observable('0');

            self.penalty_amount = ko.observable();
            self.loan_ref_no = ko.observable();
            self.installment_number = ko.observable();
            self.principal_amount = ko.observable(0);
            self.interest_amount = ko.observable(0);
            self.received_penalty_amount = ko.observable(0);
            self.extra_principal_amount = ko.observable();
            self.extra_amount = ko.observable(0);
            self.extra_amount_use = ko.observable(1);

            self.extra_amount.subscribe((data) => {
                setTimeout(function() {
                    let validator = $('#formInstallment_payment').validate({
                        //your validation rules
                        errorPlacement: function(error, element) {
                            if (element.attr("name") == "extra_principal") {
                                error.insertBefore($(".loan_curtailment_error"));
                            } else if (element.attr("name") == "extra_amount_use") {
                                // an example
                                error.insertAfter($(".after-p"));

                            } else {
                                error.insertAfter(element);
                            }
                        }
                    });
                    if (parseInt(self.curtail_loan()) == 1) {
                        validator.element("#td_extra_principal");
                    } else {
                        validator.element("#extra_amount");
                    }
                    //$('#formInstallment_payment').valid();
                }, 500);

            });

            self.paid_total = ko.observable(); // for multiple installment payments
            self.loan_balance = ko.observable(
                <?php echo !empty($loan_balance) ? $loan_balance : 0; ?>); // for multiple installment payments

            self.curtail_loan = ko.observable('0');
            self.curtail_loan.subscribe((data) => {
                if (parseInt(data) == 1) {

                    $('#totalAmount').rules("add", {
                        required: true,
                        max: parseFloat(self.multiple_installment_max()),
                        min: 0,
                        messages: {
                            required: 'This Field is required',
                            max: 'Amount is greater than the total loan payable amount ' +
                                curr_format(parseFloat(self.multiple_installment_max())),
                            min: 'Amount is less than ' + curr_format(0),

                        },
                    });


                } else {
                    $('#totalAmount').rules("add", {
                        required: true,
                        max: self.payment_with_savings_max() ? round(self
                            .payment_with_savings_max(), 2) : (self.next_payment_data() ?
                            self.max_total_amount_single_installment() : false),
                        min: 0,
                        messages: {
                            required: 'This Field is required',
                            max: 'Amount is greater than ' + curr_format(self
                                .payment_with_savings_max() ? round(self
                                    .payment_with_savings_max(), 2) : (self
                                    .next_payment_data() ? self
                                    .max_total_amount_single_installment() : false)),
                            min: 'Amount is less than ' + curr_format(0),

                        },
                    });
                    $('#td_extra_principal-error').css('display', 'none');
                }
                $('#formInstallment_payment').valid();
            });


            self.totalAmount = ko.observable(0);
            self.forgive_interest = ko.observable(0);
            self.forgive_penalty = ko.observable(0);
            self.forgiven_interest = ko.observable(0);
            self.forgiven_penalty = ko.observable(0);

            self.forgive_interest.subscribe(() => {
                if (self.payment_data()) {
                    if (self.forgive_interest()) {
                        self.forgiven_interest(
                            round(parseFloat(self.payment_data().remaining_interest) - (self
                                .interest_amount() ? parseFloat(self.interest_amount()) : 0), 2)
                        );
                    }
                }

            });
            self.forgive_penalty.subscribe(() => {
                if (self.penalty_amount() && self.loan_installment()) {
                    if (self.forgive_penalty()) {
                        self.forgiven_penalty(
                            round((parseFloat(self.penalty_amount().penalty_value) + parseFloat(self
                                .loan_installment().demanded_penalty)) - (self
                                .received_penalty_amount() ? parseFloat(self
                                    .received_penalty_amount()) : 0), 2)
                        );
                    }
                }
            });

            self.interest_amount.subscribe((data) => {
                let totalAmount = self.totalAmount() ? parseFloat(self.totalAmount()) : 0;
                let interest_amount = self.interest_amount() ? parseFloat(self.interest_amount()) : 0;
                let principal_amount = self.principal_amount() ? parseFloat(self.principal_amount()) :
                    0;
                let penalty = self.received_penalty_amount() ? parseFloat(self
                    .received_penalty_amount()) : 0;
                let extra_amount = totalAmount - (principal_amount + interest_amount + penalty);
                self.extra_amount(
                    round(parseFloat(extra_amount), 2)
                );

                self.forgive_interest(0);
            });

            self.received_penalty_amount.subscribe((data) => {
                let totalAmount = self.totalAmount() ? parseFloat(self.totalAmount()) : 0;
                let interest_amount = self.interest_amount() ? parseFloat(self.interest_amount()) : 0;
                let principal_amount = self.principal_amount() ? parseFloat(self.principal_amount()) :
                    0;
                let penalty = self.received_penalty_amount() ? parseFloat(self
                    .received_penalty_amount()) : 0;
                let extra_amount = totalAmount - (principal_amount + interest_amount + penalty);
                self.extra_amount(
                    round(parseFloat(extra_amount), 2)
                );

                self.forgive_penalty(0);
            });

            self.principal_amount.subscribe((data) => {
                let totalAmount = self.totalAmount() ? parseFloat(self.totalAmount()) : 0;
                let interest_amount = self.interest_amount() ? parseFloat(self.interest_amount()) : 0;
                let principal_amount = self.principal_amount() ? parseFloat(self.principal_amount()) :
                    0;
                let penalty = self.received_penalty_amount() ? parseFloat(self
                    .received_penalty_amount()) : 0;
                let extra_amount = totalAmount - (principal_amount + interest_amount + penalty);
                self.extra_amount(
                    round(parseFloat(extra_amount), 2)
                );
            });

            self.calculate_principal_interest = () => {
                let totalAmount = self.totalAmount();

                if (self.payment_data()) {
                    if (parseFloat(totalAmount) >= parseFloat(self.payment_data().remaining_principal)) {
                        self.principal_amount(
                            round(parseFloat(self.payment_data().remaining_principal), 2)
                        );

                        // calculate interest
                        let interest = parseFloat(totalAmount) - parseFloat(self.payment_data()
                            .remaining_principal);

                        if (interest > 0) {
                            if (interest <= parseFloat(self.payment_data().remaining_interest)) {
                                self.interest_amount(
                                    round(parseFloat(interest), 2)
                                );
                                self.extra_amount(0);
                                self.received_penalty_amount(0);
                            }

                            if (interest > parseFloat(self.payment_data().remaining_interest)) {
                                self.interest_amount(
                                    round(parseFloat(self.payment_data().remaining_interest), 2)
                                );

                                let extra = interest - self.payment_data().remaining_interest;

                                if (extra > 0) {

                                    if (self.penalty_amount() && self.loan_installment()) {
                                        if ((parseFloat(self.penalty_amount().penalty_value) + parseFloat(
                                                self.loan_installment().demanded_penalty)) <= extra) {
                                            self.received_penalty_amount(
                                                round(parseFloat(self.penalty_amount().penalty_value) +
                                                    parseFloat(self.loan_installment()
                                                        .demanded_penalty), 2)
                                            );

                                            self.extra_amount(
                                                round(
                                                    parseFloat(extra - (parseFloat(self.penalty_amount()
                                                        .penalty_value) + parseFloat(self
                                                        .loan_installment().demanded_penalty))), 2)
                                            );
                                        }
                                        if ((parseFloat(self.penalty_amount().penalty_value) + parseFloat(
                                                self.loan_installment().demanded_penalty)) > extra) {
                                            self.received_penalty_amount(round(parseFloat(extra), 2));
                                            self.extra_amount(0);
                                        }
                                    } else {
                                        self.extra_amount(
                                            round(parseFloat(extra), 2)
                                        );
                                        self.received_penalty_amount(0);
                                    }


                                } else {
                                    self.extra_amount(0);
                                    self.received_penalty_amount(0);
                                }
                            }

                        } else {
                            self.interest_amount(0);
                            self.extra_amount(0);
                            self.received_penalty_amount(0);
                        }

                    }

                    if (parseFloat(totalAmount) < parseFloat(self.payment_data().remaining_principal)) {
                        self.principal_amount(
                            round(parseFloat(totalAmount), 2)
                        );
                        self.interest_amount(0);
                        self.extra_amount(0);
                        self.received_penalty_amount(0);

                    }

                    if (parseFloat(totalAmount) < 0 || parseFloat(totalAmount) === parseFloat(0) ||
                        totalAmount === '') {
                        self.principal_amount(0);
                        self.interest_amount(0);
                        self.extra_amount(0);
                        self.received_penalty_amount(0);

                    }
                }
            }

            self.calculate_interest_principal = () => {
                if (self.payment_data()) {

                    let totalAmount = parseFloat(self.totalAmount());
                    let remaining_principal = parseFloat(self.payment_data().remaining_principal);
                    let remaining_interest = parseFloat(self.payment_data().remaining_interest);

                    if (totalAmount <= remaining_interest) {
                        self.interest_amount(
                            round(parseFloat(totalAmount), 2)
                        );
                        self.principal_amount(0);
                        self.extra_amount(0);
                        self.received_penalty_amount(0);
                    }

                    if (totalAmount > remaining_interest) {
                        self.interest_amount(
                            round(parseFloat(remaining_interest), 2)
                        );

                        let bal1 = totalAmount - remaining_interest;

                        if (bal1 <= remaining_principal) {
                            self.principal_amount(
                                round(parseFloat(bal1), 2)
                            );
                            self.extra_amount(0);
                            self.received_penalty_amount(0);
                        }

                        if (bal1 > remaining_principal) {

                            self.principal_amount(
                                round(parseFloat(remaining_principal), 2)
                            );

                            let bal2 = bal1 - remaining_principal;

                            if (self.penalty_amount() && self.loan_installment()) {
                                let penalty = parseFloat(self.penalty_amount().penalty_value) + parseFloat(
                                    self.loan_installment().demanded_penalty);

                                if (bal2 <= penalty) {
                                    self.received_penalty_amount(round(parseFloat(bal2), 2));
                                    self.extra_amount(0);
                                }

                                if (bal2 > penalty) {
                                    self.received_penalty_amount(round(parseFloat(penalty), 2));
                                    self.extra_amount(
                                        round(parseFloat(bal2 - penalty), 2)
                                    );
                                }
                            } else {
                                self.received_penalty_amount(0);
                                self.extra_amount(round(parseFloat(bal2), 2));
                            }

                        }
                    }

                }
            }

            // recalculate amounts on changes

            self.totalAmount.subscribe((data) => {
                if (parseInt(self.interest_first()) === 1) {
                    self.calculate_interest_principal();
                } else {
                    self.calculate_principal_interest();
                }

                self.forgive_penalty(0);
                self.forgive_interest(0);

                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);
            });

            self.interest_first.subscribe(() => {
                if (parseInt(self.interest_first()) === 1) {
                    self.calculate_interest_principal();
                } else {
                    self.calculate_principal_interest();
                }

                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);
            });

            self.penalty_amount.subscribe(() => {

                add_money_formatter();

                if (parseInt(self.interest_first()) === 1) {
                    self.calculate_interest_principal();
                } else {
                    self.calculate_principal_interest();
                }

                self.forgive_penalty(0);
                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);
            });


            //for payment purposes
            self.active_loans = ko.observableArray(<?php echo json_encode($active_loans) ?>);
            self.active_loan = ko.observable();

            self.active_loan.subscribe(() => {
                self.forgive_penalty(0);
                self.forgive_interest(0);

                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);

                if (self.active_loan()) {
                    get_total_pending_penalty();
                }
            });

            self.loan_installments = ko.observableArray();
            self.loan_installment = ko.observable();

            self.total_pending_penalty = ko.observable(
                0); // Holds Multiple Installment Total Penalty Calculted on the fly

            self.total_demanded_principal = ko.observable(0);
            self.total_demanded_interest = ko.observable(0);
            self.total_demanded_amount = ko.observable(0);
            self.total_demanded_penalty = ko.observable(0);

            self.overall_penalty = ko.computed(() => {
                if (self.total_pending_penalty() || self.total_demanded_penalty()) {
                    add_money_formatter();
                }


                if (self.total_pending_penalty()) {
                    return parseFloat(self.total_demanded_penalty()) + parseFloat(self
                        .total_pending_penalty());
                }

                return parseFloat(self.total_demanded_penalty());

            }, self).extend({
                notify: 'always'
            });

            self.loan_installments.subscribe((data) => {
                if (data) {
                    let demanded_principal = 0;
                    let demanded_interest = 0;
                    let demanded_penalty = 0;
                    let demanded_total_amount = 0;

                    let i = 0;

                    data.forEach(installment => {
                        i++;

                        if ($('#multiple_installment_payment').val()) {
                            if (i === 1 && self.active_loan()) {
                                get_payment_detail({
                                    loan_ref_no: self.active_loan().loan_no,
                                    call_type: $('#call_type').val(),
                                    installment_number: installment.installment_number
                                });
                            }
                            if (i === 2 && self.active_loan()) {
                                get_next_payment_detail({
                                    loan_ref_no: self.active_loan().loan_no,
                                    call_type: $('#call_type').val(),
                                    installment_number: installment.installment_number
                                });
                            }
                        }

                        if (i === 2 && self.active_loan()) {
                            get_next_payment_detail({
                                loan_ref_no: self.active_loan().loan_no,
                                call_type: $('#call_type').val(),
                                installment_number: installment.installment_number
                            });
                        }

                        demanded_principal += parseFloat(installment.principal_amount);
                        demanded_interest += parseFloat(installment.interest_amount);
                        demanded_total_amount += parseFloat(installment.total_amount);
                        demanded_penalty += parseFloat(installment.demanded_penalty);
                    });

                    self.total_demanded_amount(demanded_total_amount);
                    self.total_demanded_principal(demanded_principal);
                    self.total_demanded_interest(demanded_interest);
                    self.total_demanded_penalty(demanded_penalty);
                }
            });

            self.pay_multiple_installments = ko.observable(1);
            self.with_interest = ko.observable(0);


            //getting the loan installments for a loan
            self.filtered_active_loan_installment = ko.computed(function() {
                if (self.active_loan()) {
                    fetch_installments(self.active_loan().id);
                }
                self.payment_data(null);
                self.next_payment_data(null);
            });

            self.selected_active_loan = ko.observable();

            self.filter_loan_product = ko.computed(function() {
                var loan_product;
                if (self.selected_active_loan()) {
                    loan_product = ko.utils.arrayFilter(self.product_names(), function(data) {
                        return parseInt(data.id) == parseInt(self.selected_active_loan()
                            .loan_product_id);
                    });
                    self.product_name(loan_product[0]);
                }
                return loan_product;
            });

            //payment options
            self.payment_modes = ko.observable(
                <?php echo (isset($payment_modes)) ? json_encode($payment_modes) : ''; ?>);
            self.payment_mode = ko.observable();

            //range fees charge calculation
            self.available_loan_range_fees = ko.observableArray(
                <?php echo (!empty($available_loan_range_fees) ? json_encode($available_loan_range_fees) : '') ?>
            );
            self.compute_fee_amount = function(loan_fee_id, loan_amount) {
                var available_ranges;
                var fee_amount = 0;
                if (self.available_loan_range_fees()) {
                    available_ranges = ko.utils.arrayFilter(self.available_loan_range_fees(), function(
                        data) {
                        return parseInt(data.loan_fee_id) == parseInt(loan_fee_id);
                    });

                    for (var i = 0; i <= available_ranges.length - 1; i++) {
                        if (parseFloat(available_ranges[i].max_range) != '0.00') {

                            if (parseFloat(loan_amount) >= parseFloat(available_ranges[i].min_range) &&
                                parseFloat(loan_amount) <= parseFloat(available_ranges[i].max_range)) {

                                fee_amount = parseInt(available_ranges[i].calculatedas_id) == 1 ? (
                                    parseFloat(available_ranges[i].range_amount) * parseFloat(
                                        loan_amount) / parseFloat(100)) : parseFloat(available_ranges[i]
                                    .range_amount);
                                break;
                            }
                        } else if (parseFloat(available_ranges[i].max_range) == '0.00' && parseFloat(
                                available_ranges[i].min_range) != '0.00') {
                            if (parseFloat(loan_amount) >= parseFloat(available_ranges[i].min_range)) {
                                fee_amount = parseInt(available_ranges[i].calculatedas_id) == 1 ? (
                                    parseFloat(available_ranges[i].range_amount) * parseFloat(
                                        loan_amount) / parseFloat(100)) : parseFloat(available_ranges[i]
                                    .range_amount);
                                break;
                            }
                        }
                    }
                }

                return fee_amount;
            }

            self.formatAccount2 = function(account) {
                return account.account_code + " " + account.account_name;
            };

            self.select2accounts = function(sub_category_id) {
                //its possible to send multiple subcategories as the parameter
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function(account) {
                    return Array.isArray(sub_category_id) ? (check_in_array(account.sub_category_id,
                        sub_category_id)) : (account.sub_category_id == sub_category_id);
                });
                return filtered_accounts;
            };
            self.loan_installment.subscribe(function(data) {
                var dataobj = {};
                if (typeof data != 'undefined' && typeof self.active_loan() !== 'undefined') {
                    dataobj['loan_ref_no'] = typeof self.active_loan().loan_no !== 'undefined' ? self
                        .active_loan().loan_no : '';
                    dataobj['installment_number'] = typeof data.installment_number !== 'undefined' ?
                        data.installment_number : '';
                    dataobj['call_type'] = $('#call_type').val();
                    get_payment_detail(dataobj);
                }
            });
            self.installment_payment_date.subscribe(function(data) {
                if (typeof data !== 'undefined') {
                    get_new_penalty(data);
                    get_total_pending_penalty(data);
                }
            });

            //End of payment variables


            self.loan_product_length = ko.computed(function() {
                if (typeof self.product_name() != 'undefined') {
                    var loan_product_length = (self.product_name().max_repayment_installments) * (self
                        .product_name().repayment_frequency);
                    var loan_product_period = periods[self.product_name().repayment_made_every - 1];

                    return loan_product_length + ' ' + loan_product_period;
                } else if (typeof self.selected_product() != 'undefined' && self.selected_product() !=
                    null) {
                    var loan_product_length = (self.selected_product().max_repayment_installments) * (
                        self.selected_product().repayment_frequency);
                    var loan_product_period = periods[self.selected_product().repayment_made_every - 1];
                    return loan_product_length + ' ' + loan_product_period;
                } else {
                    return false;
                }
            }, this);

            //
            self.product_date = ko.computed(function() {

                if (typeof self.product_name() != 'undefined') {
                    var loan_product_length = (self.product_name().max_repayment_installments) * (self
                        .product_name().repayment_frequency);
                    var loan_product_period = periods[self.product_name().repayment_made_every - 1];

                    return moment().add(loan_product_length, loan_product_period);
                } else if (typeof self.selected_product() != 'undefined' && self.selected_product() !=
                    null) {
                    var loan_product_length = (self.selected_product().max_repayment_installments) * (
                        self.selected_product().repayment_frequency);
                    var loan_product_period = periods[self.selected_product().repayment_made_every - 1];
                    return moment().add(loan_product_length, loan_product_period);
                } else {
                    return false;
                }
            }, this);
            self.available_loan_saving_accounts = ko.observableArray(
                <?php echo (!empty($savings_accs_member) ? json_encode($savings_accs_member) : '') ?>);
            self.attached_loan_saving_accounts = ko.observableArray([new SavingsAccount()]);

            self.applied_loan_fee = ko.observableArray([new LoanFee()]);
            self.addLoanFee = function() {
                self.applied_loan_fee.push(new LoanFee());
            };
            self.removeLoanFee = function(selected_member) {
                self.applied_loan_fee.remove(selected_member);
            };

            self.pay_loan_fee = ko.observableArray([new PayLoanFee()]);
            self.addLoanPayFee = function() {
                self.pay_loan_fee.push(new PayLoanFee());
            };
            self.removeLoanPayFee = function(selected_member) {
                self.pay_loan_fee.remove(selected_member);
            };

            self.addSavingAcc = function() {
                self.attached_loan_saving_accounts.push(new SavingsAccount());
            };
            self.removeSavingAcc = function(selected_member) {
                self.attached_loan_saving_accounts.remove(selected_member);
            };
            self.payment_date.subscribe(function(data) {
                if (typeof data !== 'undefined') {
                    get_new_penalty(data);
                }
            });

            self.action_date = ko.observable('<?php // echo date('d-m-Y'); ?>');
            self.initialize_edit = function() {
                edit_data(self.loan_detail(), "formClient_loan");
            };

            // self.get_payment_data = function () {
            //    var dataobj = {};
            //     dataobj['loan_ref_no'] = typeof self.loan_ref_no() !== 'undefined' ? self.loan_ref_no : '';
            //     dataobj['installment_number'] = typeof self.installment_number() !== 'undefined' ? self.installment_number : '';
            //     dataobj['call_type']=$('#call_type').val();
            //     get_payment_detail(dataobj);
            // };
            self.action_date.subscribe(function(new_date) {
                var dataobj = {
                    action_date: new_date
                };
                if (typeof new_date !== 'undefined') {
                    get_new_schedule(dataobj, 1);
                }
            });

            self.new_date.subscribe(function(data) {
                var dataobj = {
                    new_repayment_date: data
                };
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });
            self.interest_rate.subscribe(function(data) {
                var dataobj = {
                    interest_rate: data
                };
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });

            self.repayment_frequency.subscribe(function(data) {
                var dataobj = {
                    repayment_frequency: data
                };
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });

            self.repayment_made_every.subscribe(function(data) {
                if (typeof data !== 'undefined') {
                    var dataobj = {
                        repayment_made_every: typeof data.id === 'undefined' ? data : data.id
                    };
                    get_new_schedule(dataobj, 2);
                }
            });

            self.installments.subscribe(function(data) {
                var dataobj = {
                    installments: data
                };
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });

            //filtering the savings A/C per client selection
            self.filtered_savingac = ko.computed(function() {
                var available_savingsac;
                if (self.member_name() || self.active_loan()) {
                    available_savingsac = ko.utils.arrayFilter(self.available_loan_saving_accounts(),
                        function(data) {
                            if (typeof self.member_name() != 'undefined') {
                                return parseInt(data.member_id) == parseInt(self.member_name().id);
                            } else if (typeof self.active_loan() != 'undefined') {
                                return (parseInt(self.client_type()) === 2) || (parseInt(data.member_id) == parseInt(self.active_loan()
                                    .member_id));
                            }
                        });
                } else if (typeof self.member_names() === 'undefined' || typeof self.member_names() ===
                    'object') {
                    available_savingsac = ko.utils.arrayFilter(self.available_loan_saving_accounts(),
                        function(data) {
                            return parseInt(data.member_id) == parseInt(
                                <?php echo (isset($user)) ? $user['id'] : ((isset($_SESSION['member_id']) && !empty($_SESSION['member_id'])) ? $_SESSION['member_id'] : '') ?>
                            );
                        });
                }
                return available_savingsac;
            });
            //for generating the disbursement sheet 
            <?php $this->load->view('client_loan/loan_steps_files/application_knockoutjs.php'); ?>

            self.disburse = function() {

                if (self.loan_detail().group_loan_id) {
                    self.loan_details({
                        ...self.loan_detail(),
                        member_name: undefined
                    });
                } else {
                    self.loan_details(self.loan_detail());
                }

                var data_set = {};
                var data1 = {};
                var controller = "Client_loan";
                <?php if (($org['loan_app_stage'] == 0) || ($org['loan_app_stage'] == 1)) { ?>
                    data_set = self.loan_detail();
                    var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement";
                <?php } elseif ($org['loan_app_stage'] == 2) { ?>
                    var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement1";
                    data1['offset_period1'] = self.loan_detail().offset_period;
                    data1['offset_made_every1'] = self.loan_detail().offset_made_every;
                    data1['amount1'] = self.loan_detail().requested_amount;
                    data1['product_type_id1'] = self.loan_detail().product_type_id;
                    data1['interest_rate1'] = self.loan_detail().interest_rate;
                    data1['installments1'] = self.loan_detail().installments;
                    data1['repayment_made_every1'] = self.loan_detail().repayment_made_every;
                    data1['repayment_frequency1'] = self.loan_detail().repayment_frequency;
                    data1['loan_product_id1'] = self.loan_detail().loan_product_id;
                    data_set = data1;
                <?php } ?>
                $.ajax({
                    url: url,
                    data: data_set,
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        self.action_date(null);
                        self.action_date('<?php // echo date('d-m-Y'); ?>');
                        self.payment_schedule(null);
                        self.payment_schedule(response.payment_schedule);
                        self.payment_summation(response.payment_summation);

                    }
                });
            };

            self.re_finance = function() {
                if (self.loan_detail().group_loan_id) {
                    self.loan_details({
                        ...self.loan_detail(),
                        member_name: undefined
                    });
                } else {
                    self.loan_details(self.loan_detail());
                }

                var controller = "Client_loan";
                var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement";
                $.ajax({
                    url: url,
                    data: self.loan_detail(),
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        self.action_date(null);
                        self.action_date('<?php // echo date('d-m-Y'); ?>');
                        self.payment_schedule(null);
                        self.payment_schedule(response.payment_schedule);
                        self.payment_summation(response.payment_summation);

                    }
                });
            };

            self.approve_loan = function() {
                // console.log(self.loan_detail());
                // throw new Error("my error message");
                if (self.loan_detail().group_loan_id) {
                    self.loan_details({
                        ...self.loan_detail(),
                        member_name: undefined
                    });
                } else {
                    self.loan_details(self.loan_detail());
                }

                // console.log(self.loan_details());
                // throw new Error("my error message");
                let controller = "Client_loan";
                let url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/get_approval_data";
                $.ajax({
                    url: url,
                    data: self.loan_detail(),
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        self.approval_data(null);
                        self.selected_product(null);
                        self.selected_product(response.selected_product);
                        self.approval_data(response.approval_data);

                    }
                });
            };
            self.action_on_loan = function() {
                if (self.loan_detail().group_loan_id) {
                    self.loan_details({
                        ...self.loan_detail(),
                        member_name: undefined
                    });
                } else {
                    self.loan_details(self.loan_detail());
                }
            };

            self.pay_off = function() {

                if (self.loan_detail().group_loan_id) {
                    self.loan_details({
                        ...self.loan_detail(),
                        member_name: undefined
                    });
                } else {
                    self.loan_details(self.loan_detail());
                }

                var controller = "Repayment_schedule";
                var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/get_pay_off_data";
                $.ajax({
                    url: url,
                    data: self.loan_detail(),
                    type: 'POST',
                    dataType: 'json',
                    success: function(response) {
                        self.pay_off_data(null);
                        self.pay_off_data(response.pay_off_data);

                    }
                });
            };

            self.relationships = ko.observable(
                <?php echo (isset($relationship_types)) ? json_encode($relationship_types) : ''; ?>);
            self.relationship = ko.observable();

            //adding/removing members as guarantors (without savings or shares)
            self.added_member_guarantor = ko.observableArray([new MemberGuarantor()]);

            self.addMemberGuarantor = function() {
                self.added_member_guarantor.push(new MemberGuarantor());
                // add select2 to security fees modal
                document.querySelectorAll('.loan_security_fees').forEach(select => {
                    $(select).select2();
                });
            };
            self.removeMemberGuarantor = function(selected_type) {
                self.added_member_guarantor.remove(selected_type);
            };
            //adding/removing members as guarantors (non existing member)
            self.added_member_guarantor2 = ko.observableArray([new MemberGuarantor()]);

            self.addMemberGuarantor2 = function() {
                self.added_member_guarantor2.push(new MemberGuarantor());
                // add select2 to security fees modal
                document.querySelectorAll('.loan_security_fees').forEach(select => {
                    $(select).select2();
                });
            };
            self.removeMemberGuarantor2 = function(selected_type) {
                self.added_member_guarantor2.remove(selected_type);
            };

            // ##
            self.edit_principal = ko.observable(false);
            self.edit_interest = ko.observable(false);
            self.edit_penalty = ko.observable(false);
            self.edit_loan_curtailment = ko.observable(false);

            self.edit_click = (inputId) => {
                $(`#${inputId}`).off('blur');
                $(`#${inputId}`).on('blur', () => {
                    let validator = $('#formInstallment_payment').validate({
                        //your validation rules
                        errorPlacement: function(error, element) {
                            if (element.attr("name") == "extra_amount_use") {
                                // an example
                                error.insertAfter($(".after-p"));

                            } else {
                                error.insertAfter(element);
                            }
                        }
                    });
                    let isValid = validator.element(`#${inputId}`);

                    if (inputId === 'td_principal_amount' && isValid) self.edit_principal(false);

                    if (inputId === 'td_interest_amount' && isValid) self.edit_interest(false);

                    if (inputId === 'td_penalty' && isValid) self.edit_penalty(false);

                    if (inputId === 'td_extra_principal' && isValid) self.edit_loan_curtailment(
                        false);
                    //$('#formInstallment_payment').valid();
                });
            }

            // Computing max and min Loan Curtailment
            self.loan_curtailment_max = ko.observable(0);
            self.loan_curtailment_min = ko.observable(0);

            self.principal_amount.subscribe((data) => {
                if (data != undefined) {
                    let paid_principal = parseFloat(data);
                    let remaining_principal = self.total_demanded_principal() - paid_principal;
                    let max = round((remaining_principal * 0.8), 2);
                    let min = round((remaining_principal * 0.2), 2);

                    self.loan_curtailment_max(max);
                    self.loan_curtailment_min(min);
                }

            });

            self.extra_amount.subscribe((data) => {
                let paid_principal = self.principal_amount();
                let remaining_principal = self.total_demanded_principal() - paid_principal;
                let max = round((remaining_principal * 0.8), 2);
                let min = round((remaining_principal * 0.2), 2);

                self.loan_curtailment_max(max);
                self.loan_curtailment_min(min);
            });


            // Computing Max and Min
            self.max_total_amount_single_installment = ko.computed(() => {
                let remaining_principal = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_principal)
                ) : 0;

                let remaining_interest = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_interest)
                ) : 0;

                let next_installment_principal = self.next_payment_data() ? (
                    parseFloat(self.next_payment_data().remaining_principal)
                ) : 0;

                let penalty = self.penalty_amount() ? (
                    parseFloat(self.penalty_amount().penalty_value)
                ) : 0;

                let demanded_penalty = self.loan_installment() ? (
                    parseFloat(self.loan_installment().demanded_penalty)
                ) : 0;

                let total = (remaining_principal + remaining_interest + next_installment_principal +
                    penalty + demanded_penalty);
                return round(parseFloat(total), 2);

            }, self);

            // Computing Penalty For Single Installment
            self.single_installment_total_penalty = ko.computed(() => {
                let on_the_fly_penalty = self.penalty_amount() ? (
                    parseFloat(self.penalty_amount().penalty_value)
                ) : 0;

                let old_penalty = self.loan_installment() ? (
                    parseFloat(self.loan_installment().demanded_penalty)
                ) : 0;

                let total = (on_the_fly_penalty + old_penalty);

                return round(parseFloat(total), 2);
            }, self);

            // Computing Expected Total
            self.expected_total = ko.computed(() => {
                let penalty = self.single_installment_total_penalty();
                let principal = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_principal)
                ) : 0;
                let interest = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_interest)
                ) : 0;

                let total = (penalty + principal + interest);

                return total;

            }, self);

            // set maximum payable amount when using savings for payment

            self.payment_with_savings_max = ko.observable(false);

            self.payment_mode.subscribe((data) => {
                //self.totalAmount(0);
                if (data && parseInt(data.id) === 5) {
                    //self.payment_with_savings_max(round(self.expected_total(), 2));
                    self.payment_with_savings_max(false);
                } else {
                    self.payment_with_savings_max(false);
                }

                setTimeout(() => add_money_formatter(), 1000);
            });


            // Multiple Installment Max & Min Amount 
            self.multiple_installment_max = ko.observable(0);
            self.multiple_installment_min = ko.observable(0);

            self.paid_total_change = ko.computed(() => {
                let data = self.overall_penalty();
                let total_demanded = self.total_demanded_amount();
                let overall_total = round((parseFloat(total_demanded) + parseFloat(data)), 2);

                let remaining_principal = self.payment_data() ? self.payment_data()
                    .remaining_principal : 0;
                let remaining_interest = self.payment_data() ? self.payment_data().remaining_interest :
                    0;
                let next_principal = self.next_payment_data() ? self.next_payment_data()
                    .remaining_principal : 0;

                let min_total = parseFloat(remaining_principal) + parseFloat(remaining_interest) +
                    parseFloat(next_principal);

                //let min = Math.ceil(min_total / 100) * 100;

                self.multiple_installment_min(round(min_total, 2));

                self.multiple_installment_max(round((overall_total + (0.2 * overall_total)), 2));

                return true;
            }, self).extend({
                notify: 'always'
            });

            // COMMENTED OUT TO REMOVE THE LIMIT FLEXIBILITY
            // self.paid_total_change.subscribe((data) => {
            //     if(data) {
            //         $("#numeric-paid_total").rules("remove", "min max");

            //         $("#numeric-paid_total").rules("add", {
            //             required: true,
            //             max: Math.ceil(self.multiple_installment_max() / 100) * 100,
            //             min: self.multiple_installment_min(),
            //             messages: {
            //                 required: "This field is required.",
            //                 max: jQuery.validator.format('Amount is greater than ' + curr_format(
            //                     Math.ceil(self.multiple_installment_max() / 100) * 100)),
            //                 min: jQuery.validator.format('Amount must be greater than ' + curr_format(self
            //                     .multiple_installment_min())),
            //             }
            //         });

            //     }
            // })






            //end of the observables
        };
        loanDetailModel = new LoanDetailModel();
        ko.applyBindings(loanDetailModel);
        //loan period validation
        $.validator.addMethod("mustbelessthanProductMaxLoanPeriod", function(value, element) {
            $(element).attr('data-rule-mustbelessthanProductMaxLoanPeriod');
            var account_length = (parseInt($('#installment').val()) * parseInt($('#paid_every').val()));
            var account_period = periods[parseInt($('#period_id').val()) - 1];

            var account_date = moment().add(account_length, account_period);

            if (typeof loanDetailModel.product_date() != 'undefined') {
                var period_difference = loanDetailModel.product_date().diff(account_date, 'days');

                if (period_difference >= 0) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        }, "This period exceedes the above stated period");

        $.validator.addMethod("mustbelessthantheProductMaxLoanPeriod", function(value, element) {
            $(element).attr('data-rule-mustbelessthantheProductMaxLoanPeriod');
            var account_length = (parseInt($('#approved_installments').val()) * parseInt($(
                '#approved_repayment_frequency').val()));
            var account_period = periods[parseInt($('#approved_repayment_made_every').val()) - 1];

            var account_date = moment().add(account_length, account_period);

            if (typeof loanDetailModel.product_date() != 'undefined') {
                var period_difference = loanDetailModel.product_date().diff(account_date, 'days');

                if (period_difference >= 0) {
                    return true;
                } else {
                    return false;
                }

            } else {
                return false;
            }
        }, "This period exceedes the above stated period");
        var handleDataTableButtons = function(tabClicked) {
            <?php if (in_array('15', $modules)) {
                $this->view('client_loan/security/guarantor/table_js');
            } ?>
            <?php $this->view('client_loan/security/collateral/table_js'); ?>
            <?php $this->view('client_loan/repayment_schedule/table_js'); ?>
            <?php $this->view('client_loan/loan_ledger_card/table_js'); ?>
            <?php $this->view('client_loan/loan_docs/table_js'); ?>
            <?php $this->view('client_loan/fees/table_js'); ?>
            <?php if ((in_array('6', $modules)) && (in_array('5', $modules))) {
                $this->view('client_loan/loan_attached_saving_accounts/table_js');
            } ?>
            <?php $this->view('client_loan/approval/table_js'); ?>
            <?php $this->view('client_loan/history/table_js'); ?>
            <?php $this->view('client_loan/income_and_expense/income/table_js'); ?>
            <?php $this->view('client_loan/income_and_expense/expense/table_js'); ?>
            <?php $this->view('client_loan/loan_transactions/table_js'); ?>
            <?php $this->view('client_loan/guarantors/table_js'); ?>
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        <?php if (in_array('15', $modules)) { ?>
            TableManageButtons.init("tab-guarantor");
        <?php } ?>
        TableManageButtons.init("tab-collateral");
    });

    $('table tbody').on('click', 'tr .reschedule_loan', function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var tbl = row.parent().parent();
        var tbl_id = $(tbl).attr("id");
        var dt = dTable[tbl_id];
        var data = dt.row(row).data();
        if (typeof(data) === 'undefined') {
            data = dt.row($(row).prev()).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev().prev()).data();
            }
        }
        //clear the the other fields because we are starting the selection afresh
        loanDetailModel.payment_summation(null);
        loanDetailModel.payment_schedule(null);
        loanDetailModel.schedule_detail(null);
        loanDetailModel.current_installment(data.installment_number);
        loanDetailModel.new_date(moment(data.repayment_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        loanDetailModel.installments(loanDetailModel.loan_detail().approved_installments);
        loanDetailModel.repayment_frequency(data.repayment_frequency);
        loanDetailModel.interest_rate(data.interest_rate);
        loanDetailModel.repayment_made_every(data.repayment_made_every);
        loanDetailModel.schedule_detail(data);
    });

    $('table tbody').on('click', 'tr .pay_for_installment', function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var tbl = row.parent().parent();
        var tbl_id = $(tbl).attr("id");
        var dt = dTable[tbl_id];
        var data = dt.row(row).data();
        if (typeof(data) === 'undefined') {
            data = dt.row($(row).prev()).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev().prev()).data();
            }
        }
        //clear the the other fields because we are starting the selection afresh
        loanDetailModel.payment_details(null);
        loanDetailModel.payment_details(data);

        var url = "<?php echo site_url("loan_installment_payment/get_penalty_data"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                loanDetailModel.penalty_amount(response.penalty_data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " +
                    textStatus);
            }
        });
    });

    $('table tbody').on('click', 'tr .re_finance_loan', function(e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var tbl = row.parent().parent();
        var tbl_id = $(tbl).attr("id");
        var dt = dTable[tbl_id];
        var data = dt.row(row).data();
        if (typeof(data) === 'undefined') {
            data = dt.row($(row).prev()).data();
            if (typeof(data) === 'undefined') {
                data = dt.row($(row).prev().prev()).data();
            }
        }
        //clear the the other fields because we are starting the selection afresh
        loanDetailModel.payment_summation(null);
        loanDetailModel.payment_schedule(null);
        loanDetailModel.schedule_detail(null);
        loanDetailModel.new_date(moment(data.repayment_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        loanDetailModel.installments(loanDetailModel.loan_detail().installments);

        loanDetailModel.schedule_detail(data);
        loanDetailModel.current_installment(data.installment_number);
    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formClient_loan":
                if (typeof response.client_loan !== 'undefined') {
                    loanDetailModel.loan_detail(response.client_loan);
                }
                if (typeof response.group_loan !== 'undefined') {
                    window.location = "<?php echo site_url('group_loan'); ?>";
                }
                break;
            case "formActive":
                if (typeof response.client_loan != 'undefined') {
                    loanDetailModel.loan_detail(response.client_loan);
                }
                dTable['tblRepayment_schedule'].ajax.reload(null, false);
                break;
            case "formApprove":
                if (typeof response.client_loan != 'undefined') {
                    loanDetailModel.loan_detail(response.client_loan);
                }
                dTable['tblLoan_approvals'].ajax.reload(null, false);
                break;
            case "formReverse":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            case "formReverse_approval":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            case "formApplication_withdraw":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            case "formClient_loan_guarantor":
                if (typeof response.guarantors != 'undefined') {
                    loanDetailModel.guarantors(null);
                    loanDetailModel.guarantors(response.guarantors);
                }
                break;
            case "formLoan_fee_application":
                if (typeof response.available_loan_fees != 'undefined') {
                    loanDetailModel.available_loan_fees(response.available_loan_fees);
                }
                dTable['tblApplied_loan_fee'].ajax.reload(null, false);
                break;
            case "formPayLoan_fee":
                if (typeof response.unpaid_loan_fees != 'undefined') {
                    loanDetailModel.unpaid_loan_fees(response.unpaid_loan_fees);
                }
                dTable['tblApplied_loan_fee'].ajax.reload(null, false);
                break;
            case "formLoan_detail_saving_accounts":
                if (typeof response.savings_accs != 'undefined') {
                    loanDetailModel.available_loan_saving_accounts(response.savings_accs);
                }
                dTable['tblLoan_attached_saving_accounts'].ajax.reload(null, false);
                break;
            case "formReschedule_payment":
                dTable['tblRepayment_schedule'].ajax.reload(null, false);
                break;
            case "formPay_off":
                loanDetailModel.loan_detail(response.client_loan);
                dTable['tblLoan_installment_payment'].ajax.reload(null, false);
                break;
            case "formInstallment_payment":
                loanDetailModel.loan_detail(response.client_loan);
                TableManageButtons.init("tab-loan_installment_payment");
                dTable['tblLoan_installment_payment'].ajax.reload(null, false);
                break;
            case "formInstallment_payment_multiple":
                loanDetailModel.loan_detail(response.client_loan);
                TableManageButtons.init("tab-loan_installment_payment");
                dTable['tblLoan_installment_payment'].ajax.reload(null, false);
                break;
            case "formRe_finance":
                dTable['tblRepayment_schedule'].ajax.reload(null, false);
                break;
            case "formForward_application":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            default:
                window.location.reload();
                break;
        }
    }

    //getting payment schedule for a loan at application stage
    function get_payment_schedule(data) {
        var new_data = {};
        new_data['application_date1'] = typeof data.application_date === 'undefined' ? loanDetailModel.application_date() :
            data.application_date;
        new_data['action_date1'] = typeof data.action_date === 'undefined' ? loanDetailModel.app_action_date() : data
            .action_date;

        new_data['loan_product_id1'] = typeof loanDetailModel.product_name() !== 'undefined' ? loanDetailModel
            .product_name().id : loanDetailModel.loan_details().loan_product_id;
        new_data['product_type_id1'] = typeof loanDetailModel.product_name() !== 'undefined' ? loanDetailModel
            .product_name().product_type_id : loanDetailModel.loan_details().product_type_id;

        new_data['amount1'] = typeof data.amount === 'undefined' ? ((typeof loanDetailModel.app_amount() != 'undefined') ?
            loanDetailModel.app_amount() : ((typeof loanDetailModel.loan_details() != 'undefined') ? loanDetailModel
                .loan_details().requested_amount : '')) : data.amount;
        new_data['offset_period1'] = typeof data.offset_period === 'undefined' ? ((typeof loanDetailModel
            .app_offset_period() != 'undefined') ? loanDetailModel.app_offset_period() : ((typeof loanDetailModel
            .loan_details() != 'undefined') ? loanDetailModel.loan_details().offset_period : '')) : data.offset_period;
        new_data['offset_made_every1'] = typeof data.offset_made_every === 'undefined' ? ((typeof loanDetailModel
                .app_offset_every() != 'undefined') ? loanDetailModel.app_offset_every() : ((typeof loanDetailModel
                .loan_details() != 'undefined') ? loanDetailModel.loan_details().offset_made_every : '')) : data
            .offset_every;
        new_data['interest_rate1'] = typeof data.interest === 'undefined' ? ((typeof loanDetailModel.app_interest() !=
            'undefined') ? loanDetailModel.app_interest() : ((typeof loanDetailModel.loan_details() !=
            'undefined') ? loanDetailModel.loan_details().interest_rate : '')) : data.interest;
        new_data['repayment_made_every1'] = typeof data.repayment_made_every === 'undefined' ? ((typeof loanDetailModel
            .app_repayment_made_every() != 'undefined') ? loanDetailModel.app_repayment_made_every() : ((
                typeof loanDetailModel.loan_details() != 'undefined') ? loanDetailModel.loan_details()
            .repayment_made_every : '')) : data.repayment_made_every;

        new_data['repayment_frequency1'] = typeof data.repayment_frequency === 'undefined' ? ((typeof loanDetailModel
            .app_repayment_frequency() != 'undefined') ? loanDetailModel.app_repayment_frequency() : ((
                typeof loanDetailModel.loan_details() != 'undefined') ? loanDetailModel.loan_details()
            .repayment_frequency : '')) : data.repayment_frequency;

        new_data['installments1'] = typeof data.installments === 'undefined' ? ((typeof loanDetailModel
            .app_installments() != 'undefined') ? loanDetailModel.app_installments() : ((typeof loanDetailModel
            .loan_details() != 'undefined') ? loanDetailModel.loan_details().installments : '')) : data.installments;

        var url = "<?php echo site_url("client_loan/disbursement1"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //clear the the other fields because we are starting the selection afresh
                loanDetailModel.payment_summation(null);
                loanDetailModel.payment_schedule(null);
                //populate the observables
                loanDetailModel.payment_schedule(response.payment_schedule);
                loanDetailModel.payment_summation(response.payment_summation);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }




    //getting new schedule
    function get_new_schedule(data, call_type) {
        var new_data = {};
        new_data['compute_interest_from_disbursement_date'] = loanDetailModel.compute_interest_from_disbursement_date();
        if (call_type === 1) {
            new_data['action_date'] = typeof data.action_date === 'undefined' ? loanDetailModel.action_date() : data
                .action_date;
        } else {
            new_data['new_repayment_date'] = typeof data.new_repayment_date === 'undefined' ? loanDetailModel.new_date() :
                data.new_repayment_date;
            new_data['interest_rate'] = typeof data.interest_rate === 'undefined' ? loanDetailModel.interest_rate() : data
                .interest_rate;
            new_data['repayment_made_every'] = typeof data.repayment_made_every === 'undefined' ? loanDetailModel
                .repayment_made_every() : data.repayment_made_every;
            new_data['repayment_frequency'] = typeof data.repayment_frequency === 'undefined' ? loanDetailModel
                .repayment_frequency() : data.repayment_frequency;
            new_data['installments'] = typeof data.installments === 'undefined' ? loanDetailModel.installments() : data
                .installments;
            new_data['current_installment'] = typeof data.current_installment === 'undefined' ? loanDetailModel
                .current_installment() : data.current_installment;
        }
        new_data['id'] = loanDetailModel.loan_detail().id;
        var url = "<?php echo site_url("client_loan/disbursement"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //clear the the other fields because we are starting the selection afresh
                loanDetailModel.payment_summation(null);
                loanDetailModel.payment_schedule(null);
                //populate the observables
                loanDetailModel.payment_schedule(response.payment_schedule);
                loanDetailModel.payment_summation(response.payment_summation);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting payment data
    function get_payment_detail(new_data) {
        var url = "<?php echo site_url("loan_installment_payment/payment_data"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                loanDetailModel.payment_data(response.payment_data);
                loanDetailModel.penalty_amount(response.penalty_data);

                let next_installment = loanDetailModel.loan_installments().find(val => {
                    if (loanDetailModel.loan_installment()) {
                        return parseInt(val.installment_number) === parseInt(loanDetailModel
                            .loan_installment().installment_number) + 1;
                    }
                    return false;
                });


                if (next_installment) {
                    get_next_payment_detail({
                        ...new_data,
                        installment_number: next_installment.installment_number
                    });
                }

            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting payment data for next installment
    function get_next_payment_detail(new_data) {
        var url = "<?php echo site_url("loan_installment_payment/payment_data"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                loanDetailModel.next_payment_data(response.payment_data);
                //loanDetailModel.penalty_amount(response.penalty_data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting new penalty data
    function get_new_penalty(new_data) {
        var data = {};
        data['payment_date'] = new_data;
        data['client_loan_id'] = loanDetailModel.payment_data() ? loanDetailModel.payment_data().id : loanDetailModel
            .active_loan().id;
        data['state_id'] = loanDetailModel.active_loan().state_id;
        data['installment_number'] = loanDetailModel.payment_data() ? loanDetailModel.payment_data().installment_number :
            loanDetailModel.loan_installments()[0].installment_number;
        var url = "<?php echo site_url("loan_installment_payment/get_penalty_data"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                loanDetailModel.penalty_amount(response.penalty_data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    function fetch_installments(client_loan_id) {
        var url = "<?php echo site_url("repayment_schedule/jsonList2"); ?>";
        var new_data = {
            payment_status: [2, 4],
            status_id: 1,
            client_loan_id: client_loan_id
        };
        // var data = 'payment_status <> 1 AND repayment_schedule.status_id=1 AND client_loan_id='+client_loan_id;

        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                loanDetailModel.loan_installments(response.data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        //compute the sums of the collateral or guarantors
        if (theData.length > 0) {
            if (theData[0]['item_value']) { //collateral data array
                loanDetailModel.collateral_amount(sumUp(theData, 'item_value'));
            }
            if (theData[0]['amount_locked']) { //guarantor data array
                loanDetailModel.guarantor_amount(sumUp(theData, 'amount_locked'));
            }
        }
    }

    let handlePrint = () => {
        let client_loan_id = loanDetailModel.loan_detail().id;
        let status_id = loanDetailModel.loan_detail().status_id;
        $('#btn_print_loan_transactions').css('display', 'none').css('visibility', 'hidden');
        $('#btn_printing_loan_transactions').css('display', 'flex').css('visibility', 'visible');

        $.ajax({
            url: '<?php echo site_url("client_loan/loan_installment_payments_statement"); ?>',
            data: {
                client_loan_id: client_loan_id,
                status_id: status_id
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                // console.log('\n\n\n', response, '\n\n\n');
                $('#btn_print_loan_transactions').css('display', 'flex').css('visibility', 'visible');
                $('#btn_printing_loan_transactions').css('display', 'none').css('visibility', 'hidden');

                $('#div_loan_payments_print_out').html(response.the_page_data);
                printJS({
                    printable: 'printable_loan_payments',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.sub_title
                });
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                $('#btn_print_loan_transactions').css('display', 'flex').css('visibility', 'visible');
                $('#btn_printing_loan_transactions').css('display', 'none').css('visibility', 'hidden');
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error: function(err) {
                $('#btn_print_loan_transactions').css('display', 'flex').css('visibility', 'visible');
                $('#btn_printing_loan_transactions').css('display', 'none').css('visibility', 'hidden');
            },

        });

    }

    const print_client_loan_report = () => {
        $.ajax({
            url: '<?php echo site_url("client_loan/client_loan_report_printout"); ?>',
            data: {
                client_loan_id: loanDetailModel.loan_detail().id,
                print: 1,
                filename: loanDetailModel.loan_detail().loan_no + ' Loan Report',
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //console.log('\n\n\n', response, '\n\n\n');
                $('#client_loan_report_print_out').html(response.the_page_data);
                printJS({
                    printable: 'printable_client_loan_report',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.filename
                })
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }

    const print_disbursement_sheet = () => {

        $.ajax({
            url: '<?php echo site_url("loan_approval/pdf_disburse"); ?>',
            data: {
                client_loan_id: loanDetailModel.loan_detail().id,
                print: 1,
                filename: loanDetailModel.loan_detail().loan_no + ' Disbursement Sheet',
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //console.log('\n\n\n', response, '\n\n\n');
                $('#client_loan_disbursement_sheet_print_out').html(response.pdf_data);
                printJS({
                    printable: 'printable_client_loan_disbursement_sheet',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.filename
                })
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }
    const print_loan_agreement = () => {

        $.ajax({
            url: '<?php echo site_url("loan_approval/pdf_agreement"); ?>',
            data: {
                client_loan_id: loanDetailModel.loan_detail().id,
                print: 1,
                filename: loanDetailModel.loan_detail().loan_no + ' Loan Agreement',
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //console.log('\n\n\n', response, '\n\n\n');
                $('#client_loan_disbursement_sheet_print_out').html(response.pdf_data);
                printJS({
                    printable: 'printable_client_loan_agreement',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.filename
                })
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }
    const print_loan_application = () => {

        $.ajax({
            url: '<?php echo site_url("loan_approval/pdf_application_form"); ?>',
            data: {
                client_loan_id: loanDetailModel.loan_detail().id,
                print: 1,
                filename: loanDetailModel.loan_detail().member_name + ' - ' + loanDetailModel.loan_detail().loan_no + ' Loan Application Form',
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //console.log('\n\n\n', response, '\n\n\n');
                $('#client_loan_disbursement_sheet_print_out').html(response.pdf_data);
                printJS({
                    printable: 'printable_client_loan_application_form',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.filename
                })
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }
    const print_schedule = () => {

        $.ajax({
            url: '<?php echo site_url("loan_approval/pdf_schedule"); ?>',
            data: {
                client_loan_id: loanDetailModel.loan_detail().id,
                print: 1,
                filename: loanDetailModel.loan_detail().loan_no + ' Schedule',
                status_id: 1
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //console.log('\n\n\n', response, '\n\n\n');
                $('#client_loan_disbursement_sheet_print_out').html(response.pdf_data);
                printJS({
                    printable: 'printable_client_loan_schedule',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.filename
                })
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }

    $(document).ready(() => {
        $('#btn_printing_loan_transactions').css('display', 'none').css('visibility', 'hidden');
    });


    $('#select-existing-collateral').on('select2:select', function(e) {
        let selected_col = JSON.parse(e.params.data.element.value);
        loanDetailModel.add_existing_collateral(JSON.parse(e.params.data.element.value));
        let remaing_collateral = loanDetailModel.existing_collateral().filter(val => parseInt(selected_col.id) !==
            parseInt(val.id));
        loanDetailModel.existing_collateral(remaing_collateral);

        $('#select-existing-collateral').val([0]).trigger('change');

    });

    let get_total_pending_penalty = (new_date) => {
        var data = {};
        data['payment_date'] = new_date ? new_date : (loanDetailModel.installment_payment_date() ? loanDetailModel
            .installment_payment_date() : (loanDetailModel.payment_date() ? loanDetailModel.payment_date() :
                "<?php echo date('d-m-Y') ?>"));

        data['client_loan_id'] = loanDetailModel.active_loan().id;

        $.ajax({
            url: '<?php echo site_url("loan_installment_payment/get_total_penalty_data"); ?>',
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                loanDetailModel.total_pending_penalty(response.total_penalty);
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    let get_member_collateral = () => {
        $.ajax({
            url: '<?php echo site_url("member_collateral/jsonList2"); ?>',
            data: {
                member_id: "<?php echo $loan_detail['member_id'] ?>",
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('\n\n\n\n ##', response.data);
                loanDetailModel.member_collaterals(response.data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    let get_member_loan_collateral = () => {
        $.ajax({
            url: '<?php echo site_url("loan_collateral/jsonList"); ?>',
            data: {
                status_id: 1,
                client_loan_id: "<?php echo $loan_detail['id']; ?>"
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                loanDetailModel.loan_collaterals(response.data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    let fetch_collateral_data = () => {
        get_member_collateral();
        get_member_loan_collateral();
        loanDetailModel.added_existing_collateral([]);
        $('#select-existing-collateral').val([0]).trigger('change');

        $('#attach_existing_collateral-modal').modal('show');
    }

    $(document).ready(() => {
        $('#member_guarantor_id_3').select2({
            dropdownParent: $("#add_member_guarantor-modal")
        });
        $('#select-existing-collateral').select2({
            dropdownParent: $("#attach_existing_collateral-modal")
        });

        // handle show/hide loan installment modals on loan details
        $('#multiple_installment_payment-modal').on('show.bs.modal', function(e) {
            $('#multiple_installment_payment').val(1);
        });

        $('#multiple_installment_payment-modal').on('hide.bs.modal', function(e) {

            // Resetting Form
            loanDetailModel.installment_payment_date('');
            loanDetailModel.forgive_penalty('');
            loanDetailModel.with_interest('');
            loanDetailModel.payment_mode('');
            loanDetailModel.trans_channel('');
            loanDetailModel.selected_ac('');
            $('#formInstallment_payment_multiple').trigger("reset");

        });




        $('#installment_payment-modal').on('show.bs.modal', function(e) {


        });
        $('#installment_payment-modal').on('hide.bs.modal', function(e) {

            // Resetting Form
            loanDetailModel.installment_payment_date('');
            loanDetailModel.curtail_loan('0');
            loanDetailModel.payment_mode('');
            loanDetailModel.interest_first('0');
            loanDetailModel.extra_amount_use('');
            loanDetailModel.trans_channel('');
            loanDetailModel.selected_ac('');
            $('#formInstallment_payment').trigger("reset");

        });

    });



    let showSingleInstallmentForm = () => {
        $('#multiple_installment_payment-modal').modal('hide');
        $('#installment_payment-modal').modal('show');
    }

    let add_money_formatter = () => {
        // Money Format options
        let options = {
            digitGroupSeparator: ',',
            decimalCharacter: '.',
            //decimalCharacterAlternative: '.',
            allowDecimalPadding: 'floats'
        }
        if (!AutoNumeric.isManagedByAutoNumeric('.money-format')) {
            allMoneyInputs = new AutoNumeric.multiple('.money-format', options);
        } else {
            allMoneyInputs.forEach(el => el.remove()); // Remove AutoNumeric listeners and reset values to ''
            allMoneyInputs = new AutoNumeric.multiple('.money-format', options); // Re-initialise AutoNumeric
        }

        let moneyInputs = document.querySelectorAll('.money-format');
        // console.log('\n\n', moneyInputs, '\n\n');

        moneyInputs.forEach(input => {
            input.addEventListener('change', (e) => {
                let inputId = input.id;
                // console.log('\n\n', inputId, '\n\n');
                // console.log(e.target.value, '\n');
                let str_val = e.target.value;
                let num_val = parseFloat(str_val.split(',').join(''));

                // console.log('Number Value : ', num_val);

                let numeric_input_id = `numeric-${inputId.split('-')[1]}`;

                // console.log('\n\n', 'Num Input ID', numeric_input_id, '\n\n');

                $(`#${numeric_input_id}`).val(num_val).change();
                $('#formInstallment_payment_multiple').valid();
                $('#formInstallment_payment').valid();

            })
        })

    }
    let get_guarantors = () => {
        dTable['tblGuarantor'].ajax.reload(null, true);
    }
</script>