<div class="ibox-title">
    <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("setting"); ?>">Settings</a></li>
        <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" href="#tab-details"> Loan Product</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#tab-penalty">Repayment & Penalty</a></li>
                <li><a class="nav-link" data-toggle="tab" href="#tab-fee">Fees</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-details" class="tab-pane active">
                    <div class="panel-body">
                        <div class="pull-left add-record-btn">
                            <div class="panel-title">
                                <h3 data-bind="text: ($root.loan_product().product_name)?$root.loan_product().product_name+'\'s Details':''">
                                </h3>
                            </div>
                        </div>
                        <div class="pull-right add-record-btn">
                            <?php if (in_array('3', $loan_product_privilege)) { ?>
                                <a href="#add_loan_product-modal" data-bind="click: initialize_edit" data-toggle="modal" class="btn btn-default pull-right btn-sm">
                                    <i class="fa fa-edit"></i> Edit Loan Product</a>
                            <?php } ?>
                        </div>
                        <table class="table table-stripped  m-t-md">
                            <tbody data-bind="with: loan_product">
                                <tr>
                                    <th>Interest Calculated</th>
                                    <td data-bind="text: (type_name)?type_name:'None'"></td>
                                    <th>Available To</th>
                                    <td data-bind="text: (name)?name:'None'"></td>
                                    <th>Loan Security</th>
                                    <td data-bind="text: (min_collateral)?(min_collateral*1)+'%':'None'"></td>
                                </tr>
                                <tr>
                                    <th>Minimum Amount</th>
                                    <td data-bind="text: (min_amount)?curr_format(min_amount*1):'None'"></td>
                                    <th>Maximum Amount</th>
                                    <td data-bind="text: (max_amount)?curr_format(max_amount*1):'None'"></td>
                                    <th>Default Amount</th>
                                    <td data-bind="text: (def_amount)?curr_format(def_amount*1):'None'"></td>
                                </tr>
                                <tr>
                                    <th>Minimum Interest </th>
                                    <td data-bind="text: (min_interest)?(min_interest*1)+'%':'None'"></td>
                                    <th>Maximum Interest </th>
                                    <td data-bind="text: (max_interest)?(max_interest*1)+'%':'None'"></td>
                                    <th>Default Interest </th>
                                    <td data-bind="text: (def_interest)?(def_interest*1)+'%':'None'"></td>
                                </tr>
                                <tr>
                                    <th>Loan(Assest) Receivable A/C</th>
                                    <td data-bind="text: (loan_receivable_account)?loan_receivable_account:'Not provided'"></td>
                                    <th>Interest income A/C</th>
                                    <td data-bind="text: (interest_income_account)?interest_income_account:'Not provided'"></td>
                                    <th>Interest Receivable A/C</th>
                                    <td data-bind="text: (interest_receivable_account)?interest_receivable_account:'Not provided'"></td>
                                </tr>
                                <tr>
                                    <th>Minimum Offset Period</th>
                                    <td data-bind="text: (min_offset)?min_offset+' '+offset_every:'None'"></td>
                                    <th>Maximum Offset Period</th>
                                    <td data-bind="text: (max_offset)?max_offset+' '+offset_every:'None'"></td>
                                    <th>Default Offset Period</th>
                                    <td data-bind="text: (def_offset)?def_offset+' '+offset_every:'None'"></td>
                                </tr>
                                <tr>
                                    <th>Fund Source A/C</th>
                                    <td data-bind="text: (fund_source_account)?fund_source_account:'Not provided'"></td>
                                    <th>Minimum Guarantor</th>
                                    <td data-bind="text: (min_guarantor)?min_guarantor:'None'"></td>
                                    <th>Description</th>
                                    <td colspan="3" data-bind="text: (description)?description:'None'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" id="tab-penalty" class="tab-pane">
                    <div class="panel-body">
                        <div class="pull-left add-record-btn">
                            <div class="panel-title">
                                <h3>Penalty & Repayment Details</h3>
                            </div>
                        </div>
                        <div class="pull-right add-record-btn">
                            <?php if (in_array('3', $loan_product_privilege)) { ?>
                                <a data-toggle="modal" href="#add_loan_product_penalty-modal" data-bind="click: initialize_edit" class="btn btn-sm btn-default pull-right"><i class="fa fa-edit"></i> Edit Repayment and Penalty</a>
                            <?php } ?>
                        </div>
                        <table class="table table-stripped  m-t-md">
                            <tbody data-bind="with: loan_product">
                                <tr>
                                    <th>Minimum Repayment Installment</th>
                                    <td data-bind="text: (min_repayment_installments)?min_repayment_installments:'None'"></td>
                                    <th>Maximum Repayment Installment</th>
                                    <td data-bind="text: (max_repayment_installments)?max_repayment_installments:'None'"></td>
                                    <th>Default Repayment Installment</th>
                                    <td data-bind="text: (def_repayment_installments)?def_repayment_installments:'None'"></td>
                                </tr>
                                <tr>
                                    <th>Repayment Frequency</th>
                                    <td data-bind="text: (repayment_frequency)?repayment_frequency+' '+made_every_name:'None'"></td>
                                    <th>Maximum Tranches</th>
                                    <td data-bind="text: (max_tranches)?max_tranches:'None'"></td>
                                    <th>Days in a Year</th>
                                    <td data-bind="text: (days_of_year=='1')?'365 Days':'366 Days';"></td>
                                </tr>

                                <tr>
                                    <th>Bad Debt A/C</th>
                                    <td data-bind="text: (bad_debt_account)?bad_debt_account:'Not provided'"></td>
                                    <th>Miscellaneous A/C</th>
                                    <td colspan="3" data-bind="text: (miscellaneous_account)?miscellaneous_account:'Not provided'"></td>
                                </tr>

                                <tr>
                                    <th>Link to Deposit Account</th>
                                    <td data-bind="text: (link_toDeposit_account=='1')?'Yes':'No'"></td>
                                    <th>Penalty Applicable</th>
                                    <td data-bind="text: (penalty_applicable=='1')?'Yes':'No'"></td>
                                    <th data-bind="visible: ((parseInt(penalty_applicable))==1)"><span>Penalty Charged Per</span></th>
                                    <td data-bind="visible: ((parseInt(penalty_applicable))==1)">
                                        <span data-bind="text: (penalty_rate_chargedPer==1)?'Daily':((penalty_rate_chargedPer==2)?'Weekly':((penalty_rate_chargedPer==3)?'Monthly': ((penalty_rate_chargedPer==4) ? 'Once (One time)' : 'None' )))"></span>
                                    </td>
                                </tr>
                                <tr data-bind="visible: ((parseInt(penalty_applicable))==1)">
                                    <th>Minimum Grace/Tolerance Period</th>
                                    <td data-bind="text: (min_grace_period)?min_grace_period:'None'"></td>
                                    <th>Maximum Grace/Tolerance Period</th>
                                    <td data-bind="text: (max_grace_period)?max_grace_period:'None'"></td>
                                    <th>Default Grace/Tolerance Period</th>
                                    <td data-bind="text: (def_grace_period)?def_grace_period:'None'"></td>
                                </tr>

                                <!-- ko if: parseInt($root.loan_product().penalty_calculation_method_id) === 1 -->
                                <tr data-bind="visible: ((parseInt(penalty_applicable))==1)">
                                    <th>Minimum Penalty Rate</th>
                                    <td data-bind="text: (min_penalty_rate)?(min_penalty_rate*1)+'%':'None'"></td>
                                    <th>Maximum Penalty Rate</th>
                                    <td data-bind="text: (max_penalty_rate)?(max_penalty_rate*1)+'%':'None'"></td>
                                    <th>Default Penalty Rate</th>
                                    <td data-bind="text: (def_penalty_rate)?(def_penalty_rate*1)+'%':'None'"></td>
                                </tr>
                                <!-- /ko -->


                                <tr data-bind="visible: ((parseInt(penalty_applicable))==1)">
                                    <!-- ko if: parseInt($root.loan_product().penalty_calculation_method_id) === 2 -->
                                    <th>Fixed Penalty Amount</th>
                                    <td data-bind="text: curr_format($root.loan_product().fixed_penalty_amount*1)"></td>
                                    <!-- /ko -->

                                    <th>Penalty Applied after loan end date</th>
                                    <td data-bind="text: parseInt($root.loan_product().penalty_applicable_after_due_date) === 1 ? 'YES' : 'NO' "></td>

                                </tr>


                                <tr data-bind="visible: ((parseInt(penalty_applicable))==1)">
                                    <th>Penalty income A/C</th>
                                    <td data-bind="text: (penalty_income_account)?penalty_income_account:'Not provided'"></td>
                                    <th>Penalty Receivable A/C</th>
                                    <td data-bind="text: (penalty_receivable_account)?penalty_receivable_account:'Not provided'"></td>
                                    <th>Penalty Calculation Method</th>
                                    <td data-bind="text: (method_description)?method_description:'None'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!--End of penalty section-->
                <div role="tabpanel" id="tab-fee" class="tab-pane">
                    <div class="panel-body">
                        <div><strong>Fees</strong> <?php if (in_array('1', $loan_product_privilege)) { ?> <a data-toggle="modal" href="#add_loan_product_fee-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> Add Fee</a> <?php } ?></div>
                        <div class="table-responsive">
                            <table id="tblLoan_product_fee" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                <thead>
                                    <tr>
                                        <th>Fee Name</th>
                                        <th>Fee Type</th>
                                        <th>Amount Calculated As</th>
                                        <th>Amount/Rate</th>
                                        <th>Linked Income A/C</th>
                                        <th>Linked Income Receivable A/C</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive-->
                    </div>
                </div>
                <!--End of Fees section-->

                <div role="tabpanel" id="tab-guarantor-setting" class="tab-pane">
                    <div class="panel-body">
                        <div><strong>Fees</strong> <?php if (in_array('1', $loan_product_privilege)) { ?><a data-toggle="modal" href="#add_loan_product_guarantor_setting-modal" class="btn btn-sm btn-default pull-right"><i class="fa fa-plus-circle"></i> Add Setting</a> <?php } ?></div>
                        <div class="table-responsive">
                            <table id="tblLoan_product_guarantor_setting" class="table table-striped table-bordered table-hover m-t-md" width="100%">
                                <thead>
                                    <tr>
                                        <th>Setting</th>
                                        <th>Description</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div><!-- /.table-responsive-->
                    </div>
                </div>
                <!--End of Fees section-->
            </div>
        </div>
    </div>
</div>
<?php echo $add_loan_product_modal; ?>
<?php echo $add_loan_product_penalty_modal; ?>
<?php echo $add_loan_product_fee_modal; ?>
<?php echo $add_loan_product_guarantor_setting_modal; ?>
<script>
    var dTable = {};
    var loan_productDetailModel = {};
    $(document).ready(function() {
        $('.loan_product_fees_selects').select2({
            dropdownParent: $("#add_loan_product-modal")
        });
        $('form#formLoan_product').validator().on('submit', saveData);
        $('form#formLoan_product_penalty').validate({
            submitHandler: saveData2
        });
        $('form#formLoan_product_fee').validator().on('submit', saveData);
        $('form#formLoan_product_guarantor_setting').validator().on('submit', saveData);
        /*********************************** Page Data Model (Knockout implementation) *****************************************/
        var Loan_productDetailModel = function() {
            var self = this;
            self.loan_product = ko.observable(<?php echo json_encode($loan_product); ?>);
            self.product_types = ko.observable(<?php echo json_encode($loan_product_type); ?>);
            self.product_type = ko.observable();
            self.loan_fees = ko.observable(<?php echo json_encode($loan_fee); ?>);
            self.feename = ko.observable();
            self.penalty_calculation_method_id = ko.observable();
            self.penalty_calculation_method = ko.observableArray(<?php echo json_encode($penalty_calculation_method); ?>);
            self.penaltyApplicable = ko.observable(<?php echo $loan_product['penalty_applicable']; ?>);
            self.penalty_applicable_after_due_date = ko.observable(<?php echo $loan_product['penalty_applicable_after_due_date']; ?>);
            self.taxRateSources = [{
                id: 1,
                desc: 'URA'
            }, {
                id: 2,
                desc: 'GOVT'
            }, {
                id: 3,
                desc: 'District'
            }];
            self.taxApplicable = ko.observable(0);
            self.taxCalculationMethod = ko.observable();
            self.interestRateApplicable = ko.observable(0);
            self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
            self.initialize_edit = function() {
                edit_data(self.loan_product(), "formLoan_product");
                edit_data(self.loan_product(), "formLoan_product_penalty");
            };
            self.formatAccount2 = function(account) {
                return account.account_code + " " + account.account_name;
            };
            self.select2accounts = function(sub_category_id) {
                //its possible to send multiple subcategories as the parameter
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function(account) {
                    return Array.isArray(sub_category_id) ? (check_in_array(account.sub_category_id, sub_category_id)) : (account.sub_category_id == sub_category_id);
                });
                return filtered_accounts;
            };

            self.mandatory_sv_or_sh = ko.observable("0");
            self.use_shares_as_security = ko.observable("0");
            self.use_savings_as_security = ko.observable("0");


        };

        loan_productDetailModel = new Loan_productDetailModel();
        ko.applyBindings(loan_productDetailModel);

        var handleDataTableButtons = function() {
            <?php $this->view('setting/loan/loan_product_fee/loan_product_fee_js'); ?>
            <?php //$this->view('setting/loan/loan_product_guarantor_setting/loan_product_guarantor_setting_js'); 
            ?>
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function() {
                    handleDataTableButtons();
                }
            };
        }();
        TableManageButtons.init();
    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formLoan_product":
                loan_productDetailModel.loan_product(response.loan_product);
                break;
            case "formLoan_product_penalty":
                loan_productDetailModel.loan_product(response.loan_product);
                break;
            case "formLoan_product_fee":
                if (typeof response.loan_product_fee != 'undefined') {
                    loan_product_feeDetailModel.loan_fees(response.loan_product_fee);
                }
                break;
            default:
                //nothing really to do here
                break;
        }
    }
</script>