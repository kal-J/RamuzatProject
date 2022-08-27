<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist" id="primary_tabs">
                        <li><a class="nav-link active" data-toggle="tab" role="tab" href="#tab-details">Details</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" role="tab"
                                href="#tab-membership">Membership</a></li>
                        <?php if(in_array('6', $modules)){ ?>
                        <li><a class="nav-link" data-toggle="tab" href="#tab-savings"><i class="fa fa-money"></i>
                                Savings Account</a></li>
                        <?php } if(in_array('4', $modules)){ ?>
                        <li><a class="nav-link" data-toggle="tab" href="#tab-loans"><i class="fa fa-money"></i>
                                Loans</a></li>
                        <?php } ?>
                        <?php if(in_array('12', $modules)){ ?>
                        <li><a class="nav-link" data-toggle="tab" href="#tab-group-shares"><i class="fa fa-money"></i>
                                Shares</a></li>
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
                            <?php $this->view("client_loan/group_loan/types/solidarity/add_group_loan"); ?>
                        </div>
                        <div id="tab-group-shares" class="tab-pane">
                            <?php $this->load->view('shares/shares_tab_data.php'); ?>
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
$(document).ready(function() {
    $(".select2able").select2({
        allowClear: true
    });

    //************************************Group member modal*************************************************// 
    var GroupMember = function() {
        var self = this;
    };
    //knockout files for savings account, client_loan and shares
    <?php $this->load->view('savings_account/savings_knockout.php'); ?>
    <?php $this->load->view('client_loan/group_loan/group_loan_knockout.php'); ?>
    <?php $this->load->view('shares/shares_knockout'); ?>

    // Group Member table_js


    var ViewModel = function() {
        var self = this;
        self.group = ko.observable(<?php echo json_encode($group); ?>);
        //self.company = ko.observable(<?php echo json_encode($group); ?>);
        self.initialize_edit = function() {
            edit_data(self.group(), "formGroup");
           // edit_data(self.company(), "formCompany");
        };
        self.display_table = function(data, click_event) {
            TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
        };

        self.available_group_members = ko.observableArray();
        self.added_group_members = ko.observableArray([new GroupMember()]);
        self.addGroupMember = function() {
            self.added_group_members.push(new GroupMember());
        };
        self.removeGroupMember = function(selected_member) {
            self.added_group_members.remove(selected_member);
        };
        //self.group_leader_present = ko.observable(false);
    };
    viewModel = new ViewModel();

    /* let isBound = function(id) {
        return !!ko.dataFor(document.getElementById(id));
    }; */

    ko.applyBindings(viewModel, $("#tab-details")[0]);
    ko.applyBindings(viewModel, $("#primary_tabs")[0]);
    ko.applyBindings(viewModel, $("#tab-membership")[0]);
    ko.applyBindings(sharesModel, $("#tab-group-shares")[0]);

    /* if (!isBound('tab-details')) {
        ko.applyBindings(viewModel, $("#tab-details")[0]);
    }
    if (!isBound('primary_tabs')) {
        ko.applyBindings(viewModel, $("#primary_tabs")[0]);
    }
    if (!isBound('tab-membership')) {
        ko.applyBindings(viewModel, $("#tab-membership")[0]);
    } */

    $('form#formGroup').validator().on('submit', saveData);
    $('form#formCompany').validator().on('submit', saveData);
    $('form#formGroup_member').validator().on('submit', saveData);
    $('form#formGroup_loan').validator().on('submit', saveData);
    /* PICK DATA FOR DATA TABLE  */

    var handleDataTableButtons = function(tabClicked) {
        console.log('\n', tabClicked , '\n')
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

        //=========================== START SHARES JS ======================
        <?php // $this->load->view('shares/share_account/states/pending/pending_js'); ?>
        <?php $this->load->view('shares/share_account/states/active/active_js'); ?>
        <?php //$this->load->view('shares/share_account/states/inactive/inactive_js'); ?>
    };

    

    TableManageButtons = function() {
        "use strict";
        return {
            init: function(tabClicked) {
                handleDataTableButtons(tabClicked);
            }
        };
    }();

    TableManageButtons.init("tab-pending_approval");
     <?php if(in_array('12', $modules)){ ?>
    TableManageButtons.init("tab-share_active_accounts");
    <?php } if(in_array('6', $modules)){ ?>
    TableManageButtons.init("tab-active_accounts");
    <?php } if(in_array('4', $modules)){ ?>
    TableManageButtons.init("tab-pure_loan");
    <?php } ?>

   

});
 function draw_basic_bar_graph(chart_id, chart_title, tooltip, clients, s_amount) {
        Highcharts.chart(chart_id, {

            title: {
                text: chart_title
            },

            subtitle: {
                text: 'Showing clients total savings'
            },
            xAxis: {
                type: 'category',
                categories: clients,
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '13px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Uganda Shillings'
                }
            },
            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: tooltip
            },

            series: [{
                type: 'column',
                colorByPoint: false,
                data: s_amount,
                showInLegend: false
            }]
        });
    }
function reload_data(form_id, response) {
    switch (form_id) {
        case "formPending_shares":
            dTable['tblShares_Active'].ajax.reload(null, false);
            break;
        case "formBuy_shares":
        case "formConvert_shares":
        case "formTransfer":
            dTable['tblShares_Active_Account'].ajax.reload(null, false);
            break;
        case "formDeposit":
            dTable['tblShares_Active'].ajax.reload(null, true);
            break;
        case "formReverseShare_transaction":
        case "formReverseShare_transaction":
            dTable['tblShare_transaction'].ajax.reload(null, true);
            break;
        case "formRefund":
            dTable['tblShares'].ajax.reload(null, true);
            break;
        case "formChange_state":
            sharesModel.share_details(response.share_details);
            break;
        case "formShares":
            dTable['tblShares_Active_Account'].ajax.reload(null, true);
            break;
        case "formShares_application":
        case "formShares_state":
            dTable['tblShares_Pending_application'].ajax.reload(null, true);
            break;
        case "formBulk_deposit":
            if (typeof response.failed !== 'undefined') {
                sharesModel.name_error(response.failed);
            } else {
                sharesModel.name_error(0);
                dTable['tblShare_transaction'].ajax.reload(null, true);
                //dTable['tblSavings_account_pending'].ajax.reload(null, true);
                sharesModel.clients(response.accounts);
            }
            break;
        case "formGroup_member":
            //viewModel.available_group_members(response.available_group_members);
             get_group_members(data.ranges);
            break;
        case "formGroup_loan":
            dTable['tblGroup_loan'].ajax.reload(null, false);
            dTable['tblPure_group_loan'].ajax.reload(null, false);
            break;
        case "formGroup":
            dTable['tblCompanies'].ajax.reload(null, false);
           // dTable['tblPure_group_loan'].ajax.reload(null, false);
            break;

            //============== START savings module ========
        case "formSavings_account":
            TableManageButtons.init("tab-savings_account_pending");
            if (typeof response.accounts !== 'undefined' && response.accounts != '') {
                savingsModel.clients(response.accounts);
            }
            if (typeof response.organisation_format !== 'undefined') {
                savingsModel.organisationFormats(response.organisation_format);
            }
            break;
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


  function get_group_members(data) {
    settingsModel.added_group_members([]);
    ko.utils.arrayForEach(data, function(value) {
        var added_group_member = new RangeFee();
        added_group_member.member_id = value.member_id;
        added_group_member.group_leader = value.group_leader;
        added_group_member.group_id = value.group_id;
        settingsModel.added_group_members.push(added_group_member);
    });
 }


$(document).ready(() => {
    $('#add_share_account-modal').on('show.bs.modal', function(e) {
        let group_id = '<?php echo $group['id']; ?>';
        sharesModel.member({id: group_id});
    });

    $('#select_savings_product').select2({
        dropdownParent: $('#add_savings_account')
    });
});
</script>