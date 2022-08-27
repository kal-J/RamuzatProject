<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist" id="primary_tabs">
                        <li><a class="nav-link active" data-toggle="tab" role="tab" href="#tab-details">Details</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" role="tab" href="#tab-membership">Membership</a></li>
                        <?php if(in_array('6', $modules)){ ?>
                        <li><a class="nav-link"  data-toggle="tab" href="#tab-savings"><i class="fa fa-money"></i> Savings Account</a></li>
                        <?php } if(in_array('4', $modules)){ ?>
                        <li><a class="nav-link" data-toggle="tab" href="#tab-loans"><i class="fa fa-money"></i> Loans</a></li>
                        <?php } ?>
                    </ul>
                    <div class="tab-content">
                        <!-- ================== START YOUR TAB CONTENT HERE =============== -->
                        <?php $this->view('group/view_tab'); ?>
                        <?php $this->view('group/member/view_tab'); ?>
                        <div id="tab-savings" class="tab-pane">
                            <?php $this->load->view('savings_account/savings_tab_data.php'); ?>
                        </div>
                        <div id="tab-loans" class="tab-pane">
                            <?php $this->load->view('client_loan/group_loan/group_loan_tab_data.php'); ?>
                            <?php $this->view("client_loan/states/partial/add_modal"); ?>
                        </div>
                        <!-- ================== END YOUR  TAB CONTENT HERE =============== -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var dTable = {};
    var TableManageButtons = {};
    var viewModel = {};
    var userDetailModel = {};
    var subscriptionViewModel = {};
    $(document).ready(function () {
        $(".select2able").select2({
            allowClear: true
        });
        //************************************Group member modal*************************************************// 
        var GroupMember = function () {
            var self = this;
        };
        //knockout files for savings account and client_loan
<?php $this->load->view('savings_account/savings_knockout.php'); ?>
<?php $this->load->view('client_loan/group_loan/group_loan_knockout.php'); ?>

        var ViewModel = function () {
            var self = this;

            self.group = ko.observable(<?php echo json_encode($group); ?>);
            self.initialize_edit = function () {
                edit_data(self.group(), "formGroup");
            };
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };

            self.available_group_members = ko.observableArray();
            self.added_group_members = ko.observableArray([new GroupMember()]);
            self.addGroupMember = function () {
                self.added_group_members.push(new GroupMember());
            };
            self.removeGroupMember = function (selected_member) {
                self.added_group_members.remove(selected_member);
            };

        };
        viewModel = new ViewModel();
        ko.applyBindings(viewModel, $("#tab-details")[0]);
        ko.applyBindings(viewModel, $("#primary_tabs")[0]);
        ko.applyBindings(viewModel, $("#tab-membership")[0]);

        $('form#formGroup').validator().on('submit', saveData);
        $('form#formGroup_member').validator().on('submit', saveData);
        $('form#formGroup_loan').validator().on('submit', saveData);
        /* PICK DATA FOR DATA TABLE  */

        var handleDataTableButtons = function (tabClicked) {
<?php $this->load->view('group/member/group_member_js.php'); ?>
            //============================ START SAVINGS JS -=================
<?php $this->load->view('savings_account/states/active/savings_account_js.php'); ?>
<?php $this->load->view('savings_account/transaction/transaction_js'); ?>

<?php $this->load->view('savings_account/states/pending/savings_account_pending_js.php'); ?>
<?php $this->load->view('savings_account/states/inactive/savings_account_inactive_js.php'); ?>
<?php $this->load->view('savings_account/states/suspended/savings_account_suspended_js.php'); ?>
<?php $this->load->view('savings_account/states/deleted/savings_account_deleted_js.php'); ?>
<?php $this->load->view('savings_account/deposit_withdraw_js.php'); ?>
            //=========================== END SAVINGS JS ======================
            //=========================== START LOAN JS ======================

<?php $this->load->view('client_loan/group_loan/types/pure/table_js'); ?>
<?php $this->load->view('client_loan/group_loan/types/solidarity/table_js'); ?>
            //============================END LAON JS ============================

        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tabClicked) {
                    handleDataTableButtons(tabClicked);
                }
            };
        }();
        TableManageButtons.init("tab-pending_approval");
        <?php if(in_array('6', $modules)){ ?>
        TableManageButtons.init("tab-active_accounts");
        <?php } if(in_array('4', $modules)){ ?>
        TableManageButtons.init("tab-pure_loan");
        <?php } ?>

    });
    function reload_data(form_id, response) {
        switch (form_id) {
            case "formGroup_member":
                //viewModel.available_group_members(response.available_group_members);
                break;
            case "formGroup_loan":
                dTable['tblGroup_loan'].ajax.reload(null, false);
                dTable['tblPure_group_loan'].ajax.reload(null, false);
                break;
            
//============== START savings module ========
            case "formSavings_account":
                TableManageButtons.init("tab-savings_account_pending");
                if(typeof response.accounts !== 'undefined' && response.accounts != ''){
                   savingsModel.clients(response.accounts);
                }
                if(typeof response.organisation_format !== 'undefined'){
                    savingsModel.organisationFormats(response.organisation_format);
                }
                break;
            case "formChange_state":
            case "formWithdraw":
            case "formDeposit":
            if (typeof response.insert_id !== 'undefined') {
                    window.location = "<?php echo site_url('transaction/print_receipt/'); ?>" + response.insert_id;
                }
                dTable['tblSavings_account'].ajax.reload(null, true);
                dTable['tblSavings_account_pending'].ajax.reload(null, true);
                savingsModel.clients(response.accounts);
                break;
//=================END savings module ======================  
            default:
                //nothing really to do here
                break;
        }
    }
</script>
