        <div class="row">
            <div class="col-lg-12">
                <div class="ibox ">
                    <div class="ibox-title">
                        <ul class="breadcrumb">
                            <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                            <li><span style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                        </ul>
                    </div>
                    <div class="ibox-content">
                        <div class="tabs-container">
                            <ul class="nav nav-tabs" role="tablist">
                                <li><a class="nav-link active" data-bind="click: display_table" data-toggle="tab" href="#tab-branch"><i class="fa fa-modx"></i> Organisation </a></li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i> Products </a>
                                    <ul class="dropdown-menu">
                                        <?php if ((in_array('4', $modules)) && (in_array('3', $modules))) { ?>
                                            <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-loan_product">Loan Products</a></li>
                                        <?php }
                                        if ((in_array('6', $modules)) && (in_array('5', $modules))) { ?>
                                            <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-deposit_product">Saving Products</a></li>
                                        <?php }
                                        if ((in_array('12', $modules)) && (in_array('17', $modules))) { ?>
                                            <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-share_issuance">Share Issuance</a></li>
                                        <?php }
                                        if (in_array('9', $modules)) { ?>
                                            <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-social_fund"><?php echo $this->lang->line('cont_subscription');  ?></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i> Fees </a>
                                    <ul class="dropdown-menu">
                                        <?php if ((in_array('4', $modules)) && (in_array('3', $modules))) { ?>
                                            <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-loan_fee" data-bind="click: display_table">Loan Fees</a></li>
                                        <?php }
                                        if ((in_array('6', $modules)) && (in_array('5', $modules))) { ?>
                                            <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab_saving_fees" data-bind="click: display_table">Saving Fees</a></li>
                                        <?php }
                                        if ((in_array('12', $modules)) && (in_array('17', $modules))) { ?>
                                            <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-share_fee" data-bind="click: display_table">Share Fees</a></li>
                                        <?php }
                                        if (in_array('21', $modules)) { ?>
                                            <li><a class="nav-link" data-toggle="tab" href="#tab-member_fees" data-bind="click: display_table">Membership fees</a></li>
                                        <?php } ?>
                                    </ul>
                                </li>

                                <?php if (in_array('16', $modules)) { ?>
                                    <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-role"><i class="fa fa-modx"></i> Roles</a></li>
                                <?php } ?>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i> Other Settings </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-share_category" data-bind="click: display_table">Share Category</a></li>
                                        <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-tax_rate_source" data-bind="click: display_table">Tax Rate</a></li>
                                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-alert-setting"></i>Alert Setting</a></li>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#" title="Organisation structure"><i class="fa fa-modx"></i> Structure </a>
                                    <ul class="dropdown-menu">
                                        <!-- 
                                        <li><a class="nav-link" data-toggle="tab" href="#tab-organisation" data-bind="click: display_table"><i class="fa fa-modx"></i> Organisation </a></li> -->
                                        <li><a class="nav-link" data-toggle="tab" href="#tab-branch" data-bind="click: display_table"><i class="fa fa-modx"></i> Branches</a></li>

                                        <li><a class="nav-link" data-toggle="tab" href="#tab-position" data-bind="click: display_table">Position</a></li>
                                    </ul>
                                </li>
                                <?php if (isset($active_month)) { ?>
                                    <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-locked_month"><i class="fa fa-calendar"></i> Active Month </a></li>
                                <?php } ?>

                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-backup"><i class="fa fa-modx"></i> System Backups</a></li>

                                <li class="dropdown">
                                    <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-modx"></i> More </a>
                                    <ul class="dropdown-menu">
                                        <li><a class="nav-link" data-toggle="tab" href="#tab-transaction_channel" data-bind="click: display_table"><i class="fa fa-modx"></i> Transaction Channels</a></li>

                                        <li><a class="nav-link" data-toggle="tab" href="#tab-items_for_sale" data-bind="click: display_table"><i class="fa fa-shopping-cart"></i> Items For Sale</a></li>

                                        <li><a class="nav-link" data-toggle="tab" href="#tab-user_doc_type" data-bind="click: display_table"><i class="fa fa-modx"></i>User Doc Types </a></li>


                                        <?php if ((in_array('6', $modules)) && (in_array('18', $modules))) { ?>
                                            <!--  <li><a class="nav-link" data-toggle="tab" href="#tab_account_format" data-bind="click: display_table"><i class="fa fa-modx"></i> Account No. Format</a></li> -->
                                        <?php }
                                        if ((in_array('4', $modules)) && (in_array('3', $modules))) { ?>
                                            <li><a class="nav-link" data-toggle="tab" href="#tab-loan_docs_setup" data-bind="click: display_table"><i class="fa fa-modx"></i>Loan&nbsp;Documents</a></li>
                                            <li><a class="nav-link" data-toggle="tab" href="#tab-user_expense_type" data-bind="click: display_table"><i class="fa fa-modx"></i>User&nbsp;Expenses</a></li>
                                            <li><a class="nav-link" data-toggle="tab" href="#tab-user_income_type" data-bind="click: display_table"><i class="fa fa-modx"></i>User&nbsp;Income</a></li>
                                            <li><a class="nav-link" data-toggle="tab" href="#tab-collateral_docs_setup" data-bind="click: display_table"><i class="fa fa-modx"></i>Collateral Types </a></li>
                                        <?php } ?>


                                        <?php if ((in_array('4', $modules)) && (in_array('3', $modules))) { ?>
                                        <?php } ?>


                                        <li><a class="nav-link" data-toggle="tab" href="#tab-holiday" data-bind="click: display_table"><i class="fa fa-modx"></i>Holidays</a></li>
                                        <?php if (in_array('19', $modules)) { ?>
                                            <li><a class="nav-link" data-toggle="tab" href="#tab-approval_setting" data-bind="click: display_table"><i class="fa fa-modx"></i>Approval Setting</a></li>
                                        <?php  } ?>

                                        <?php if (isset($payment_engine['payment_id']) && $payment_engine['payment_id'] != NULL) { ?>
                                            <li><a class="nav-link" data-toggle="tab" href="#tab-payment" data-bind="click: display_table"><i class="fa fa-modx"></i>Payment</a></li>
                                        <?php   } ?>
                                    </ul>
                                </li>
                                <?php if (in_array('16', $modules)) { ?>
                                    <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan-provision"><i class="fa fa-modx"></i>Loan Provision </a></li>
                                <?php } ?>
                            </ul>
                            <div class="tab-content">
                                <?php $this->view('setting/transaction/channels/channels_tab'); ?>
                                <?php $this->view('setting/savings/deposit_product/deposit_product_tab'); ?>
                                <?php $this->view('setting/shares/share_issuance/share_issuance_tab'); ?>
                                <?php $this->view('setting/shares/categories/tab_view'); ?>
                                <?php $this->view('setting/shares/share_fee/tab_view'); ?>
                                <?php $this->view('branch/tab_view'); ?>
                                <?php $this->view('setting/loan/loan_installment_rate/loan_installment_rate_tab_view'); ?>
                                <?php $this->view('setting/saving_fees/tab_saving_fees'); ?>
                                <?php $this->view('setting/saving_fees/view_saving_ranges'); ?>
                                <?php $this->view('setting/loan/loan_product/loan_product_tab_view'); ?>
                                <?php $this->view('setting/subscription_plan/tab_view'); ?>
                                <?php $this->view('setting/role/role_tab_view'); ?>
                                <?php $this->view('setting/tax_rate_source/tab_view'); ?>
                                <?php $this->view('setting/sales/tab_view'); ?>
                                <?php $this->view('setting/loan_provisions/loan_provisions_tab'); ?>
                                <?php $this->view('setting/savings/deposit_product/interest_calculation/interest_cal_tab'); ?>

                                <?php $this->view('setting/loan/loan_fee/loan_fee_tab_view'); ?>
                                <?php $this->view('setting/loan/loan_fee/view_loan_ranges'); ?>
                                <?php //$this->view('setting/loan_fee/loan_fee_tab_view'); 
                                ?>
                                <?php $this->view('setting/loan/loan_fee/view_loan_ranges'); ?>
                                <?php //$this->view('setting/organisation_format/tab_view'); 
                                ?>
                                <?php $this->view('setting/loan/documents/loan_docs_setup/tab_view'); ?>
                                <?php $this->view('setting/loan/collateral_type/tab_view'); ?>
                                <?php $this->view('setting/holiday/tab_view'); ?>
                                <?php $this->view('setting/loan/approval_setting/approval_setting_tab_view'); ?>
                                <?php $this->view('setting/user_doc_type/tab_view'); ?>
                                <?php $this->view('setting/user_expense_type/tab_view'); ?>
                                <?php $this->view('setting/user_income_type/tab_view'); ?>
                                <?php $this->view('setting/member_fees/tab_view'); ?>
                                <?php $this->view('setting/position/position_view_tab'); ?>
                                <?php $this->view('setting/payment/view_tab'); ?>
                                <?php $this->view('setting/locked_month/tab_view'); ?>
                                <?php $this->view('setting/backup/backup_view_tab'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->view('branch/add_modal'); ?>
        <?php $this->view('setting/locked_month/add_modal'); ?>
        <?php $this->view('organisation/add_modal'); ?>
        <?php $this->view('setting/saving_fees/add_deposit_product_fee-modal'); ?>
        <?php $this->view('setting/shares/share_issuance/add_share_issuance'); ?>
        <?php $this->view('setting/shares/share_fee/add_modal'); ?>
        <?php $this->view('setting/shares/categories/add_modal'); ?>
        <?php $this->view('setting/role/add_role-modal'); ?>
        <?php $this->view('setting/sales/add_item_modal'); ?>
        <?php // $this->view('setting/tax_rate/add_modal'); 
        ?>
        <?php $this->view('setting/loan/loan_fee/loan_fee_modal'); ?>
        <?php $this->view('setting/loan/documents/loan_docs_setup/add_modal'); ?>
        <?php $this->view('setting/loan/collateral_type/add_modal'); ?>
        <?php $this->view('setting/holiday/add_modal'); ?>
        <?php $this->view('setting/loan/approval_setting/approval_setting_modal'); ?>
        <?php $this->view('setting/loan/loan_product/loan_product-modal'); ?>
        <?php $this->view('setting/user_doc_type/add_modal'); ?>
        <?php $this->view('setting/user_expense_type/add_modal'); ?>
        <?php $this->view('setting/user_income_type/add_modal'); ?>
        <?php $this->view('setting/member_fees/add_modal'); ?>
        <?php $this->view('setting/position/position_modal'); ?>
        <?php $this->view('setting/backup/backup_modal'); ?>
        <?php $this->view('setting/alert_setting/alert_setting-modal'); ?>
        <?php $this->view('setting/loan_provisions/loan_provision_setting-modal'); ?>
        <?php $this->view('setting/alert_setting/custom_email-modal'); ?>
        <script>
            var dTable = {};
            var TableManageButtons = {};
            var settingsModel = {};
            var RangeFee = function() {
                var self = this;
                self.calculatedas_id = ko.observable();
                self.max_range = "";
                self.min_range = "";
                self.range_amount = "";
                self.id = "";
            };
            $(document).ready(function() {
                $(".select2able").select2({
                    dropdownParent: $('#add_transaction_channel')
                });

                $('#2factor').on('change.states', function() {
                    $("#states_name").toggle($(this).val() == '1');
                    $("#states").toggle($(this).val() == '1');
                }).trigger('change.states');

                $('#sandbox-container').datepicker({
                    format: "dd-mm-yyyy",
                    startDate: "11/08/2019",
                    startView: 1,
                    clearBtn: true,
                    daysOfWeekHighlighted: "1,2,3,4,5",
                    autoclose: true,
                    todayHighlight: true
                });

                $('#linked_account_id').select2({
                    dropdownParent: $("#add_transaction_channel")
                });
                $('.loan_product_fees_selects').select2({
                    dropdownParent: $("#add_loan_product-modal")
                });

                $('.share_issuance_selects').select2({
                    dropdownParent: $("#add_share_issuance-modal")
                });
                $('form#formOrganisation').validate({
                    submitHandler: saveData2
                });
                $('form#formFiscal_month').validate({
                    submitHandler: saveData2
                });
                $('form#formShare_category').validate({
                    submitHandler: saveData2
                });
                $('form#formBranch').validator().on('submit', saveData);
                $('form#formDepositProduct').validate({
                    submitHandler: saveData2
                });
                $('form#formTransactionChannel').validate({
                    submitHandler: saveData2
                });
                $('form#formTransactionDateControl').validate({
                    submitHandler: saveData2
                });
                $('form#formLoan_product').validator().on('submit', saveData);
                $('form#formShare_issuance').validator().on('submit', saveData);
                $('form#formSubscription_plan').validator().on('submit', saveData);
                $('form#formRole').validator().on('submit', saveData);
                $('form#formDepositProductFee').validator().on('submit', saveData);
                $('form#formInterestCalMethod').validator().on('submit', saveData);
                $('form#formItemsForSale').validator().on('submit', saveData);
                //$('form#formLoan_fee').validator().on('submit', saveData2);
                $('form#formLoan_fee').validate({
                    submitHandler: saveData2
                });
                $('form#formShare_fee').validator().on('submit', saveData);
                $('form#formSaving_fees').validate({
                    submitHandler: saveData2
                });
                $('form#formModulePrivilege').validator().on('submit', saveData);
                $('form#formLoan_docs_setup').validator().on('submit', saveData);
                $('form#formCollateral_docs_setup').validator().on('submit', saveData);
                $('form#formHoliday').validator().on('submit', saveData);
                $('form#formApproval_setting').validator().on('submit', saveData);
                $('form#formUser_doc_type').validator().on('submit', saveData);
                $('form#formUser_expense_type').validator().on('submit', saveData);
                $('form#formUser_income_type').validator().on('submit', saveData);
                $('form#formMember_fees').validator().on('submit', saveData);
                $('form#formPosition').validator().on('submit', saveData);
                $('form#formPayment_engine').validator().on('submit', saveData);
                $('form#formNonworkingdays').validator().on('submit', saveData);
                $('form#formBackup').validator().on('submit', saveData);
                $('form#formLoan_provision_setting').validator().on('submit', saveData);

                /*********************************** Page Data Model (Knockout implementation) *****************************************/


                var SettingsModel = function() {
                    var self = this;
                    self.display_table = function(data, click_event) {
                        TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
                    };
                    self.chargeTriggerOptions = ko.observableArray(<?php echo json_encode($chargeTrigger); ?>);
                    self.staffs = ko.observableArray(<?php echo json_encode($staff); ?>);
                    self.Staff = ko.observable();
                    self.dateApplicationMethodOptions = ko.observableArray(<?php echo json_encode($dateApplicationMtd); ?>);
                    self.product_types = ko.observable(<?php echo json_encode($loan_product_type); ?>);
                    self.amountCalOptions = ko.observableArray(<?php echo json_encode($amountcalculatedas); ?>);
                    self.amountcalculatedasother = ko.observable();
                    self.loan_charge_trigger = ko.observableArray(<?php echo json_encode($loan_charge_trigger); ?>);
                    self.charge_trigger_name = ko.observable();
                    self.amountCalOptionsOther = ko.observableArray(<?php echo json_encode($amountcalculatedas_other); ?>);
                    self.amountcalculatedas = ko.observable();
                    self.Amountcal = ko.observable();
                    self.deposit_producttype = ko.observable(<?php echo json_encode($deposit_product_type); ?>);
                    self.product_type = ko.observable();
                    self.producttype = ko.observable();
                    self.chargeTrigger = ko.observable();
                    self.dateApplicationMtd = ko.observable();
                    self.active_period = ko.observable();
                    self.mandatory_saving = ko.observable();
                    self.interestpaid = ko.observable();
                    self.interest_applicable = ko.observable();
                    self.schedule_start_date = ko.observable();

                    self.active_lock_period = ko.observable();
                    self.reminder_type = ko.observable();
                    self.reminder_types = ko.observable([{
                            "id": 1,
                            "reminder_name": "Day(s) "
                        },
                        {
                            "id": 2,
                            "reminder_name": "Week(s) "
                        },
                        {
                            "id": 3,
                            "reminder_name": "Month(s)"
                        },

                    ]);
                    /*self.organisationOptions = ko.observable(<?php //echo json_encode($account_format);  
                                                                ?>);
                    self.org_member_no = ko.observable(<?php //echo json_encode($member_format);  
                                                        ?>);
                    self.org_staff_no = ko.observable(<?php //echo json_encode($staff_format);  
                                                        ?>);
                    // self.org_partner_no = ko.observable(<?php //echo json_encode($partner_format);  
                                                            ?>);
                    self.org_group_no = ko.observable(<?php //echo json_encode($group_format);  
                                                        ?>);*/
                    self.repayment_made_every_options = ko.observableArray(<?php echo json_encode($repayment_made_every); ?>);
                    self.non_working_days = ko.observable(<?php echo json_encode($non_working_days[0]); ?>);

                    self.transaction_channel = ko.observableArray(<?php echo json_encode($channels); ?>);
                    self.selected_channel = ko.observable(<?php echo isset($payment_engine_requirements['transaction_channel_id']) ? $payment_engine_requirements['transaction_channel_id'] : ''; ?>);
                    self.tchannels = ko.observable();
                    self.saving_range_fees = ko.observableArray([new RangeFee()]);
                    self.loan_range_fees = ko.observableArray([new RangeFee()]);
                    self.addRangeFee = function() {
                        self.loan_range_fees.push(new RangeFee());
                        self.saving_range_fees.push(new RangeFee());
                    };
                    self.removeRangeFee = function(calculatedas_id) {
                        self.loan_range_fees.remove(calculatedas_id);
                        self.saving_range_fees.remove(calculatedas_id);
                    };

                    self.initialize_edit = function() {
                        edit_data(self.formatOptions(), "form");
                    };

                    self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);

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
                    //alert setting 


                    self.myTrigger = ko.observable();

                    self.myTriggerOptions = ko.observableArray([{
                            name: 'Paid Once',
                            id: 1
                        },
                        {
                            name: 'Paid periodically',
                            id: 2
                        }
                    ]);

                };
                settingsModel = new SettingsModel();
                ko.applyBindings(settingsModel);




                var handleDataTableButtons = function(tabClicked) {
                    <?php $this->view('branch/table_js'); ?>
                    <?php $this->view('setting/saving_fees/saving_fees_js'); ?>
                    <?php $this->load->view('setting/savings/deposit_product/deposit_product_js'); ?>
                    <?php $this->load->view('setting/transaction/channels/channel_js'); ?>
                    <?php $this->view('setting/loan/loan_product/loan_product_js'); ?>
                    <?php $this->view('setting/shares/share_issuance/share_issuance_js'); ?>
                    <?php $this->view('setting/subscription_plan/table_js'); ?>
                    <?php $this->view('setting/shares/share_fee/table_js'); ?>
                    <?php $this->view('setting/shares/categories/table_js'); ?>
                    <?php $this->view('setting/role/role_js'); ?>
                    <?php $this->view('setting/tax_rate_source/table_js'); ?>
                    <?php $this->load->view('setting/savings/deposit_product/interest_calculation/interest_cal_js'); ?>
                    <?php $this->view('setting/loan/loan_fee/loan_fee_js'); ?>
                    <?php $this->view('setting/loan/documents/loan_docs_setup/table_js'); ?>
                    <?php $this->view('setting/loan/collateral_type/table_js'); ?>
                    <?php $this->view('setting/holiday/table_js'); ?>
                    <?php $this->view('setting/loan/approval_setting/approval_setting_js'); ?>
                    <?php $this->view('setting/user_doc_type/table_js'); ?>
                    <?php $this->view('setting/user_expense_type/table_js'); ?>
                    <?php $this->view('setting/user_income_type/table_js'); ?>
                    <?php $this->view('setting/member_fees/table_js'); ?>
                    <?php $this->view('organisation/user_table_js'); ?>
                    <?php $this->view('setting/locked_month/table_js'); ?>
                    <?php $this->view('setting/position/position_js'); ?>
                    <?php $this->view('setting/backup/backup_js'); ?>
                    <?php $this->view('setting/sales/table_js'); ?>
                    <?php $this->view('setting/alert_setting/alert_setting_js'); ?>
                    <?php $this->view('setting/loan_provisions/loan_provision_setting_js'); ?>

                    if (tabClicked === "tab-payment") {
                        $(".tab-pane").removeClass("active");
                        $("#tab-payment").addClass("active");
                    }
                };
                TableManageButtons = function() {
                    "use strict";
                    return {
                        init: function(tblClicked) {
                            handleDataTableButtons(tblClicked);
                        }
                    };
                }();
                TableManageButtons.init("tab-branch");

            });
            //return Initials
            function abbreviate(str) {
                var matches = str.match(/\b(\w)/g);
                var acronym = matches.join('').toUpperCase();
                return (acronym);
            }

            function zeroFill(number, width) {
                width -= number.toString().length;
                if (width > 0) {
                    return new Array(width + (/\./.test(number) ? 2 : 1)).join('0') + number;
                }
                return number + ""; // always return a string
            }

            function reload_data(formId, reponse_data) {
                switch (formId) {

                    case "formLoan_fee":
                        settingsModel.loan_range_fees([new RangeFee()]);
                        break;
                    case "formItemsForSale":
                        dTable['tblItemsForSale'].ajax.reload(null, false);
                        break;
                    case "formNonworkingdays":
                        if (typeof reponse_data.non_working_days != 'undefined') {
                            settingsModel.non_working_days(null);
                            settingsModel.non_working_days(reponse_data.non_working_days[0]);
                        }
                        break;
                    default:
                        break;
                }

            }

            function get_saving_range_fees(data) {
                settingsModel.saving_range_fees([]);
                ko.utils.arrayForEach(data, function(range_value) {
                    var saving_range_fee = new RangeFee();
                    saving_range_fee.min_range = range_value.min_range;
                    saving_range_fee.max_range = range_value.max_range;
                    saving_range_fee.range_amount = range_value.range_amount;
                    saving_range_fee.id = range_value.id;
                    //let's get the particular account obj from the list of the accounts
                    saving_range_fee.calculatedas_id(get_range(range_value.calculatedas_id));
                    settingsModel.saving_range_fees.push(saving_range_fee);
                });
            }

            function get_loan_range_fees(data) {
                settingsModel.loan_range_fees([]);
                ko.utils.arrayForEach(data, function(range_value) {
                    var loan_range_fee = new RangeFee();
                    loan_range_fee.min_range = range_value.min_range;
                    loan_range_fee.max_range = range_value.max_range;
                    loan_range_fee.range_amount = range_value.range_amount;
                    loan_range_fee.id = range_value.id;
                    //let's get the particular account obj from the list of the accounts
                    loan_range_fee.calculatedas_id(get_range(range_value.calculatedas_id));
                    settingsModel.loan_range_fees.push(loan_range_fee);
                });

            }

            function get_range(calculatedas_id) {
                var check = ko.utils.arrayFirst(settingsModel.amountCalOptions(), function(current_type) {
                    //console.log(current_type.amountcalculatedas_id,calculatedas_id);
                    return current_type.amountcalculatedas_id == calculatedas_id;
                });
                return check;
            }

            function set_selects(data, formId) {
                switch (formId) {
                    case 'formLoan_fee':
                        get_loan_range_fees(data.ranges);
                        break;
                    case 'formSaving_fees':
                        get_saving_range_fees(data.ranges);
                        break;
                }
                edit_data(data, formId);
            }
        </script>