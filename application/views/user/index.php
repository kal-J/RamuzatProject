<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<style>
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


<div id="div_member_bio_print_out" style="display: none;"></div>
 
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                 <ul class="breadcrumb">
                  <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                    <?php if ($type == "member") { ?>
                  <li><a href="<?php echo site_url("member"); ?>"><?php echo $this->lang->line('cont_client_name_p');?></a></li>
                    <?php } else { ?>
                  <li><a href="<?php echo site_url("staff"); ?>">Staff</a></li>
                    <?php } ?>
                  <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
                </ul> 
            </div>
            <div class="ibox-content">
                <div class="tabs-container">
                    <?php if ($type == "member") { ?>
                        <ul class="nav nav-tabs" role="tablist">
                            <li><a class="nav-link active"  data-toggle="tab" href="#tab-biodata" onclick="handleTabClick('tab-personalinfo')"><i class="fa fa-user"></i> BioData</a></li>
                            <?php if(in_array('6', $modules)){ ?>
                            <li><a class="nav-link"  data-toggle="tab" href="#tab-savings" onclick="handleTabClick('tab-active_accounts')"><i class="fa fa-money"></i> Savings Account</a></li>
                            <?php } if(in_array('4', $modules)){ ?>
                            <li><a class="nav-link"  data-toggle="tab" href="#tab-loans" onclick="handleTabClick('tab-active')"><i class="fa fa-money"></i> Loans</a></li>
                            <?php } if(in_array('9', $modules)){ ?>
                            <li id='subscriptions'><a class="nav-link"  data-toggle="tab" href="#tab-client_subscriptions" data-bind="click: display_table"><i class="fa fa-money"></i> <?php echo $this->lang->line('cont_subscription');  ?></a></li>
                            <?php } if(in_array('21', $modules)){ ?>
                            <li id='applied_member_fees'><a class="nav-link"  data-toggle="tab" href="#tab-member_fees" data-bind="click: display_table"><i class="fa fa-money"></i> Membership Fee(s) </a></li>
                            <?php } if(in_array('12', $modules)){ ?>
                            <li id='member_shares'><a class="nav-link"  data-toggle="tab" href="#tab-member_shares" data-bind="click: display_table"><i class="fa fa-money"></i> Shares </a></li>
                            <li id='dividends'><a class="nav-link"  data-toggle="tab" href="#tab-dividends" data-bind="click: display_table"><i class="fa fa-money"></i> Dividends </a></li>
                            <?php } ?>
                        </ul>
                    <?php } ?>
                    <div class="tab-content" id="parent_tabs">  
                        <div id="tab-biodata" class="tab-pane active">
                            <?php $this->load->view('user/member/biodata.php'); ?>
                        </div><!-- ======================END TAB-BIO DATA ==================-->
                        <?php if ($type == "member") { ?>
                            <?php  if(in_array('6', $modules)){ ?>
                            <div id="tab-savings" class="tab-pane">
                                <?php $this->load->view('savings_account/savings_tab_data.php'); ?>
                            </div><!-- =========================END TAB-SAVINGS =====================-->
                            <?php } ?>
                            <?php  if(in_array('4', $modules)){ ?>
                            <div id="tab-loans" class="tab-pane">
                                <?php $this->load->view('client_loan/client_loan_tab_data.php'); ?>
                            </div><!-- =========================START OF client income_and_expenses =====================-->
                            <?php } ?>
                            <?php  if(in_array('9', $modules)){ ?>
                            <div id="tab-client_subscriptions" class="tab-pane">
                                <?php $this->load->view('user/member/subscriptions/tab_view.php'); ?>
                            </div><!-- =========================END TAB-SUBSCRIPTIONS =====================-->
                            <?php } ?>
                            <?php  if(in_array('21', $modules)){ ?>
                            <div id="tab-member_fees" class="tab-pane">
                                <?php $this->load->view('user/member/member_fees/tab_view.php'); ?>
                            </div><!-- =========================END TAB-MEMBER FEES =====================-->
                            <?php } ?>
                            <?php  if(in_array('12', $modules)){ ?>
 
                            <div id="member_shares" class="tab-pane">
                           
                            </div>
                            <!--<div id="tab-member_shares" class="tab-pane"></div>-->
                           
                            <div id="share_modals">
                           
                            </div>

                            <div id="tab-dividends" class="tab-pane">
                                <?php $this->load->view('user/member/dividends/tab_view'); ?>
                            </div>
                                <?php $this->load->view('shares/share_account/states/active/tab_view'); ?>
                                <?php $this->load->view('shares/share_account/states/pending/tab_view'); ?>
                                <?php $this->load->view('shares/share_account/states/inactive/tab_view'); ?>
                                <?php $this->load->view('shares/share_account/states/pending/add_modal'); ?>
                                <?php $this->load->view('shares/transaction/buy_shares'); ?>
                                <?php $this->load->view('shares/transaction/transfer'); ?>
                                <?php $this->load->view('shares/transaction/convert'); ?>
                                <?php $this->load->view('shares/transaction/bulk_transaction_modal'); ?>
                                <?php $this->load->view('shares/transaction/bulk_deposit_template-modal'); ?>

                            <!-- =========================END TAB-SHARES AND DIVIDENDS FEES =====================-->
                            <?php } ?>
                        <?php } ?>
                    </div> <!-- 1 -->
                </div> 
            </div>
        </div>
    </div>
</div>


<script>

    const handleTabClick = (tabClicked) => {
        if(tabClicked === 'tab-personalinfo') {
            $('#tab-personalinfo').addClass('active');
        } else {
            TableManageButtons.init(tabClicked);
        }
        
    }

    var dTable = {};
    var TableManageButtons = {};
    var userDetailModel = {};
    var subscriptionViewModel = {};
    var applied__fees_on_member ={};
    var shareTabs = {};
    $(document).ready(function () {
        $("#tab-share_active_accounts").removeClass("active");
        
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        var loan_product_length='';
        <?php $this->load->view('user/profile_pic_js.php'); ?>
        <?php $this->load->view('user/signature/signature_pic_js.php'); ?>
        $("#village_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });
        $("#subcounty_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });
        $("#parish_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });
        $("#district_id").select2({allowClear: true, dropdownParent: $("#add_address-modal") });
        $("#subscription_selects").select2({allowClear: true, dropdownParent: $("#add_client_subscription-modal") });


            var GeneralTabs = function () {
                var self = this;
                
                self.display_table = function (data, click_event) {
                    TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
                };
            };
            var general_tabs = new GeneralTabs();

       
            

        var MemberFee = function () {
            var self = this;
            self.selected_fee = ko.observable();
        };
        var MemberFee1 = function () {
            var self = this;
            self.selected_fee1 = ko.observable();
        };

        //**************************************************************************************************************//
        var UserDetailModel = function () {
            var self = this;

            self.user = ko.observable(<?php echo json_encode($user); ?>);
            self.signature = ko.observable(<?php echo json_encode($user_signature); ?>);
            self.initialize_edit = function () {
                edit_data(self.user(), "form<?php echo ucfirst($type); ?>");
            };
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };


            // end memeber income and expenses
            self.client_no = ko.observable("<?php echo (isset($new_client_no))?$new_client_no:'';?>");

            self.districtsList = ko.observableArray(<?php echo json_encode($districts); ?>);
            self.subcountiesList = ko.observableArray();
            self.parishesList = ko.observableArray();
            self.villagesList = ko.observableArray();
            self.members = ko.observableArray(<?php echo (isset($sorted_users))?json_encode($sorted_users):''; ?>);
            self.member = ko.observable();

            self.district = ko.observable();
            self.subcounty = ko.observable();
            self.parish = ko.observable();
            self.village = ko.observable();
            self.marital_status_id = ko.observable();
            self.date_of_birth = ko.observable();
            self.end_date=  ko.observable();
            self.checkbox = ko.observable();
            self.oncheck= function() {
                    return true;
            };

            self.district.subscribe(function (new_district) {
                if (typeof new_district !== 'undefined') {
                    get_filtered_admin_units(1, {district_id: new_district.id}, "<?php echo site_url("subcounty/jsonList"); ?>");
                }
                //clear the the other fields because we are starting the selection afresh
                self.subcountiesList(null);
                self.parishesList(null);
                self.villagesList(null);

                $('#subcounty_id').val(null).trigger('change');
                $('#parish_id').val(null).trigger('change');
                $('#village_id').val(null).trigger('change');
            });
            self.subcounty.subscribe(function (new_subcounty) {
                if (typeof new_subcounty !== 'undefined') {
                    get_filtered_admin_units(2, {subcounty_id: new_subcounty.id}, "<?php echo site_url("parish/jsonList"); ?>");
                }
                //clear the the parish and village fields because we are starting the selection afresh
                self.parishesList(null);
                self.villagesList(null);
                $('#parish_id').val(null).trigger('change');
                $('#village_id').val(null).trigger('change');
            });
            self.parish.subscribe(function (new_parish) {
                if (typeof new_parish !== 'undefined') {
                    get_filtered_admin_units(3, {parish_id: new_parish.id}, "<?php echo site_url("village/jsonList"); ?>");
                }
                //clear the the village field because we are starting the selection afresh
                self.villagesList(null);
                $('#village_id').val(null).trigger('change');
            });
        };
        userDetailModel = new UserDetailModel();
        ko.applyBindings(userDetailModel, $("#tab-biodata")[0]);
   
        //.......................end of address.................
        $('#form<?php echo ucfirst($type); ?>').validate({submitHandler: saveData2});
        $('#formNextOfKin').validator().on('submit', saveData);
        // for the address modal
        $('#formAddress').validator().on('submit', saveData);
        $('#formBusiness').validator().on('submit', saveData);
        //$('#formContact').validator().on('submit', saveData);
        $('#formContact').validate({submitHandler: saveData2});
        $('#formDocument').validator().on('submit', saveData);
        $('#formEmployment').validator().on('submit', saveData);
        $('#formUser_role').validator().on('submit', saveData);
        $('#formPassword').validator().on('submit', saveData);
        $('#formChildren').validator().on('submit', saveData);
        $('#formApplied_member_fees').validate({submitHandler: saveData2});
        $('#formApplied_member_fees1').validate({submitHandler: saveData2});
        $('#formReverseClient_subscription').validate({submitHandler: saveData2});
         $('#formClient_subscription').validator().on('submit', saveData); 
        $('#formClient_subscription1').validator().on('submit', saveData);
        //.......................END OF BIODATA.................
        <?php if ($type == "member") { ?>
            <?php  if(in_array('6', $modules)){ ?>
            //============ START SAVINGS KNOCKOUT AND FORM VALIDATIONS ================
        <?php $this->load->view('savings_account/savings_knockout.php'); ?>
                <?php } ?>
                    <?php  if(in_array('4', $modules)){ ?>
        //initializing the date range picker
        daterangepicker_initializer();
        <?php $this->load->view('client_loan/client_loan_knockout.php'); ?>
            //============ END SAVINGS KNOCKOUT AND FORM VALIDATIONS ================
                <?php } ?>

            <?php  if(in_array('9', $modules)){ ?>
            //============ START SUBCRIPTIONS KNOCKOUT AND FORM VALIDATIONS ================
            var SubscriptionViewModel = function () {
                var self = this;
                self.display_table = function (data, click_event) {
                    TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
                };
                self.subscription_plan = ko.observable(<?php echo isset($subscription_plan)?json_encode($subscription_plan):"{}"; ?>);
                var default_date = moment("<?php echo $user['date_registered']>$fiscal_year['start_date']?$user['date_registered']:$fiscal_year['start_date']; ?>", "YYYY-MM-DD").format("DD-MM-YYYY");
                self.next_payment_date = ko.observable(default_date);
                self.transaction_channels = ko.observableArray(<?php echo json_encode($tchannel); ?>);
                self.trans_channel = ko.observable();
                self.sub_fee_paid = ko.observable();

                self.available_saving_accounts = ko.observableArray(<?php echo (!empty($savings_accs) ? json_encode($savings_accs) : '') ?>);
                // self.payment_modes_other = ko.observableArray(<?php //echo json_encode($payment_modes_other); ?>);
                // self.payment_mode_other = ko.observable();
                self.payment_modes = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
                self.payment_mode = ko.observable();
                self.amount_payable = ko.observable(typeof self.subscription_plan()!=='undefined'?self.subscription_plan().amount_payable:0);
                self.get_last_subscription_date = function (json_data) {
                    var json_data_length = json_data.length;
                   if(json_data_length){
                       self.next_payment_date(moment(json_data[json_data_length-1]['subscription_date'], 'YYYY-MM-DD').add(typeof self.subscription_plan()!=='undefined'?self.subscription_plan().repayment_frequency:0, (typeof self.subscription_plan()!=='undefined'?self.subscription_plan().made_every_name:"days").toString().toLowerCase().replace("(","").replace(")","")).format("DD-MM-YYYY"));
                   }else{
                       self.next_payment_date(default_date);
                   }
                };
            };
            subscriptionViewModel = new SubscriptionViewModel();
            ko.applyBindings(subscriptionViewModel, $("#subscriptions")[0]);
            ko.applyBindings(subscriptionViewModel, $("#tab-client_subscriptions")[0]);
            //============ END SUBCRIPTIONS KNOCKOUT AND FORM VALIDATIONS ================
            <?php }  if(in_array('21', $modules)){ ?>
            //============ START APPLIED MEMBER FEES KNOCKOUT AND FORM VALIDATIONS ================
            var Applied_member_fees = function () {
                var self = this;
                
                self.display_table = function (data, click_event) {
                    TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
                };
             self.transaction_channels = ko.observableArray(<?php echo json_encode($tchannel); ?>);
                self.trans_channel = ko.observable();
                self.available_saving_accounts = ko.observableArray(<?php echo (!empty($savings_accs) ? json_encode($savings_accs) : '') ?>);
                self.payment_modes = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
                self.payment_mode = ko.observable();
                self.available_member_fees = ko.observableArray(<?php echo (!empty($available_member_fees) ? json_encode($available_member_fees) : '') ?>);

            self.applied_member_fee = ko.observableArray([new MemberFee()]);
            self.addMemberFee = function () {
                self.applied_member_fee.push(new MemberFee());
            };
            self.removeMemberFee = function (selected_member) {
                self.applied_member_fee.remove(selected_member);
            };

            self.fee_paid = ko.observable();

            self.attach_member_fees = ko.observableArray(<?php echo (!empty($attach_member_fees) ? json_encode($attach_member_fees) : '') ?>);

            self.attach_member_fee = ko.observableArray([new MemberFee1()]);
            self.addMemberFee1 = function () {
                self.attach_member_fee.push(new MemberFee1());
            };
            self.removeMemberFee1 = function (selected_member) {
                self.attach_member_fee.remove(selected_member);
            };
            };
            applied__fees_on_member = new Applied_member_fees();
            ko.applyBindings(applied__fees_on_member, $("#applied_member_fees")[0]);
            ko.applyBindings(applied__fees_on_member, $("#tab-member_fees")[0]);
            
            //============ END APPLIED MEMBER FEES KNOCKOUT AND FORM VALIDATIONS ================
<?php 
    }?>
            //============ END SUBCRIPTIONS KNOCKOUT AND FORM VALIDATIONS ================
            <?php   if(in_array('12', $modules)){ ?>
            //============ START SHARE DIVIDENDS KNOCKOUT AND FORM VALIDATIONS ================
            ko.applyBindings(general_tabs, $("#dividends")[0]);
            ko.applyBindings(general_tabs, $("#tab-dividends")[0]);

             
            
            // var ShareTabs = function () {
            //     var self = this;
                
            //     self.display_table = function (data, click_event) {
            //         TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            //     };
            // };
            
            // shareTabs = new ShareTabs();
            
            // ko.applyBindings(shareTabs, $("#member_shares")[0]);
            // ko.applyBindings(shareTabs, $("#tab-member_shares")[0]);
            
        
            /* var ShareTabs = function () {
                var self = this;
                
                self.display_table = function (data, click_event) {
                    console.log("Here mf", $(click_event.target).prop("hash").toString().replace("#", ""));

                    TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
                };
            }; */

            //shareTabs = new ShareTabs();

            /* ko.applyBindings(shareTabs, $("#member_shares")[0]);
            ko.applyBindings(shareTabs, $("#tab-member_shares")[0]); */
            //============ END SHARE DIVIDENDS KNOCKOUT AND FORM VALIDATIONS ================

            //============ START SHARE MODULE KNOCKOUT =============
            <?php $this->load->view('shares/shares_knockout'); ?>
            ko.applyBindings(sharesModel, $("#member_shares")[0]);
            ko.applyBindings(sharesModel, $("#tab-member_shares")[0]);
            ko.applyBindings(sharesModel, $("#tab-share_active_accounts")[0]);
            ko.applyBindings(sharesModel, $("#tab-share_pending_accounts")[0]);
            ko.applyBindings(sharesModel, $("#tab-share_inactive_accounts")[0]);
            ko.applyBindings(sharesModel, $("#share_modals")[0]);
            //============ END SHARE MODULE KNOCKOUT =============
<?php 
  } }
 ?>

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tabClicked) {
                    handleDataTableButtons(tabClicked);
                }
            };
        }();

        //contact javascript 
        var handleDataTableButtons = function (tabClicked) {
            console.log(tabClicked);
<?php if ($type == "member") { ?>
                //============================ START SAVINGS JS -=================
    <?php $this->load->view('savings_account/states/active/savings_account_js.php'); ?>
    <?php $this->load->view('savings_account/states/pending/savings_account_pending_js.php'); ?>
    <?php $this->load->view('savings_account/states/inactive/savings_account_inactive_js.php'); ?>
    <?php $this->load->view('savings_account/states/suspended/savings_account_suspended_js.php'); ?>
    <?php $this->load->view('savings_account/states/deleted/savings_account_deleted_js.php'); ?>
    <?php $this->load->view('savings_account/deposit_withdraw_js.php'); ?>
                //=========================== END SAVINGS JS ======================
                //=========================== START LOAN JS ======================
    <?php $this->view('client_loan/states/pending/table_js'); ?>
    <?php $this->view('client_loan/states/approved/table_js'); ?>
    <?php $this->view('client_loan/states/rejected/table_js'); ?>
    <?php $this->view('client_loan/states/cancled/table_js'); ?>
    <?php $this->view('client_loan/states/withdrawn/table_js'); ?>
    <?php $this->view('client_loan/states/partial/table_js'); ?>
    <?php $this->view('client_loan/states/active/table_js'); ?>
    <?php $this->view('client_loan/states/written_off/table_js'); ?>
    <?php $this->view('client_loan/states/paid_off/table_js'); ?>
    <?php $this->view('client_loan/states/locked/table_js'); ?>
    <?php $this->view('client_loan/states/defaulters/table_js'); ?>
    <?php $this->view('client_loan/states/risky_loans/table_js'); ?>
    <?php $this->view('client_loan/states/obligations_met/table_js'); ?>
    <?php $this->view('client_loan/states/in_arrears/table_js'); ?>
    <?php $this->view('client_loan/states/refinanced/table_js'); ?>
    <?php $this->view('client_loan/fees/table_js'); ?>
    //============================END LAON JS ============================
    //=========================== START SUBSCRIPTION JS ======================
    <?php $this->view('user/member/subscriptions/table_js'); ?>
    <?php $this->view('user/member/member_fees/table_js'); ?>
    <?php $this->view('user/member/dividends/table_js'); ?>
    <?php //$this->view('user/member/shares/table_js'); ?>
    <?php $this->view('user/member/shares/table_js'); ?>
    //=========================== END SUBSCRIPTION JS ======================
    
    //=========================== START SHARE JS ====================
    <?php $this->load->view('shares/share_account/states/pending/pending_js'); ?>
    <?php $this->load->view('shares/share_account/states/active/active_js'); ?>
    <?php $this->load->view('shares/share_account/states/inactive/inactive_js'); ?>
    <?php $this->load->view('shares/transaction/transaction_js'); ?>
    <?php $this->load->view('shares/transaction/transaction_log_js'); ?>
    <?php $this->load->view('shares/transaction/report/shares_report_js'); ?>
    <?php $this->load->view('shares/transaction/report/shares_performance_report_js'); ?>
    <?php $this->load->view('shares/transaction/report/alert_setting_js'); ?>
    //=========================== END SHARE JS ======================
        <?php } ?>
        <?php $this->load->view('user/contact/contact_js'); ?>
                    // document ajax
        <?php $this->load->view('user/document/document_js'); ?>

                    // nextofkin ajax
        <?php $this->load->view('user/nextofkin/nextofkin_js'); ?>
                    //  employment javascript 
        <?php $this->view('user/employment/employment_js'); ?>
        <?php $this->view('user/staff/role/user_role_js'); ?>
        <?php $this->load->view('user/address/address_js.php'); ?>
                    //  business javascript 
        <?php $this->load->view('user/member/business/business_js.php'); ?>

        <?php $this->view('user/member/children/children_js'); ?>
        
        if(tabClicked === 'tab-member_shares') {
            //console.log(tabClicked);
            TableManageButtons.init("tab-share_active_accounts");
        }
      
     
};

        
        <?php if(in_array('4', $modules)){ ?>
        TableManageButtons.init("tab-active");
        <?php } if(in_array('6', $modules)){ ?>
        TableManageButtons.init("tab-active_accounts");
        <?php } if(in_array('12', $modules)){  ?>
            TableManageButtons.init("tab-share_active_accounts");
        <?php } ?>

        

        TableManageButtons.init("tab-client_monthly_income");
        TableManageButtons.init("tab-client_monthly_expenses");

        
    });
<?php if ($type == "member") { ?>
        //================== CLIENT DTABLES LOAN JS ========
    <?php $this->load->view('client_loan/client_loan_dtables_js.php'); ?>
        //==================END CLIENT LOAN DTABLES JS ========
<?php } ?>
    function reload_data(form_id, response) {
        switch (form_id) {
            case "form<?php echo ucfirst($type); ?>":
                userDetailModel.user(response.user);
                break;
            case "formApplied_member_fees":
            case "formApplied_member_fees1":
                dTable['tblApplied_member_fees'].ajax.reload(null, false);
                break;
            case "formClient_subscription":
            case "formClient_subscription1":
                dTable['tblClient_subscription'].ajax.reload(null, false);
                console.log(response.subscription_date);
                if(typeof response.subscription_date !== 'undefined'){
                    subscriptionViewModel.get_last_subscription_date(response.subscription_date);
                }
                
                break;
            case "formPassword":
                userDetailModel.user(response.user);
                break;
//============== START CLIENT LOAN ========
           case "formClient_loan":
                TableManageButtons.init("tab-partial_application");
                if( typeof response.members != 'undefined' ){
                    client_loanModel.member_names( null );
                    client_loanModel.member_names( response.members );
                    client_loanModel.group_loan_details(response.group_loan_details );
                }
                if(typeof response.loan_ref_no !== 'undefined'){
                    client_loanModel.loan_ref_no(response.loan_ref_no);
                }
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                break;
            case "formApprove":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);

                }if (typeof dTable['tblApproved_client_loan'] !== 'undefined') {
                dTable['tblApproved_client_loan'].ajax.reload(null, false);
                    
                }if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                dTable['tblPartial_application_loan'].ajax.reload(null, false);
                    
                }
                break;
            case "formReject":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                    dTable['tblPartial_application_loan'].ajax.reload(null, false); 
                }
                if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);
                }
                break;
            case "formCancle":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
               if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblRejected_loan'] !== 'undefined') {
                    dTable['tblRejected_loan'].ajax.reload(null, false);
                }
                break; 
            case "formApplication_withdraw":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals );
                }
               if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                    dTable['tblPartial_application_loan'].ajax.reload(null, false); 
                }
                if (typeof dTable['tblRejected_loan'] !== 'undefined') {
                    dTable['tblRejected_loan'].ajax.reload(null, false);
                }
                break; 
            case "formForward_application":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
               if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                    dTable['tblPartial_application_loan'].ajax.reload(null, false); 
                }
                break;
            case "formActive":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                TableManageButtons.init("tab-active");
                break;
            case "formLock":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                dTable['tblActive_client_loan'].ajax.reload(null, false);
                break;
            case "formWrite_off":
                if( typeof response.state_totals != 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                dTable['tblIn_arrears_loans'].ajax.reload(null, false);
                break;
            case "formPay_off":
                if( typeof response.state_totals !== 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                if( typeof response.installments !== 'undefined'){
                    client_loanModel.loan_installments( null );
                    client_loanModel.loan_installments(response.loan_installments );
                }
                dTable['tblActive_client_loan'].ajax.reload(null, false);
                break;
            case "formInstallment_payment":
                if( typeof response.state_totals !== 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                if( typeof response.installments !== 'undefined'){
                    client_loanModel.loan_installments( null );
                    client_loanModel.loan_installments(response.loan_installments );
                }
                dTable['tblActive_client_loan'].ajax.reload(null, false);
                break;                
            case "formReverse":
                if( typeof response.state_totals !== 'undefined'){
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals );
                }
                if (typeof dTable['tblRejected_loan'] !== 'undefined') {
                    dTable['tblRejected_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblCancled_loan'] !== 'undefined') {
                    dTable['tblCancled_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblWithdrawn_loan'] !== 'undefined') {
                    dTable['tblWithdrawn_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblLocked_loans'] !== 'undefined') {
                    dTable['tblLocked_loans'].ajax.reload(null, false);
                }
                break;
            case "formReverse_approval":
                if( typeof response.state_totals !== 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                dTable['tblApproved_client_loan'].ajax.reload(null, false);
                break; 
            case "formClient_loan1":
                if( typeof response.state_totals !== 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                if( typeof response.installments !== 'undefined'){
                    client_loanModel.loan_installments( null );
                    client_loanModel.loan_installments(response.loan_installments );
                }
                if( typeof response.members !== 'undefined'){
                    client_loanModel.member_names( null );
                    client_loanModel.member_names(response.members );
                    client_loanModel.group_loan_details(response.group_loan_details );
                }

                if(typeof response.loan_ref_no !== 'undefined'){
                    client_loanModel.loan_ref_no(response.loan_ref_no);
                }
               <?php if($org['loan_app_stage']==0){ ?>
                    TableManageButtons.init("tab-pending_approval");
                <?php }elseif($org['loan_app_stage']==1){ ?>
                    TableManageButtons.init("tab-approved");
                <?php }elseif($org['loan_app_stage']==2){ ?>
                    TableManageButtons.init("tab-active");                  
                <?php } ?>
                break;
            case "formTopup_loan":
                if( typeof response.state_totals !== 'undefined'){
                    client_loanModel.state_totals( null );
                    client_loanModel.state_totals(response.state_totals );
                }
                if( typeof response.installments !== 'undefined'){
                    client_loanModel.loan_installments( null );
                    client_loanModel.loan_installments(response.loan_installments );
                }
                if( typeof response.members !== 'undefined'){
                    client_loanModel.member_names( null );
                    client_loanModel.member_names(response.members );
                    client_loanModel.group_loan_details(response.group_loan_details );
                }
                
                if(typeof response.loan_ref_no !== 'undefined'){
                    client_loanModel.loan_ref_no(response.loan_ref_no);
                }
               <?php if($org['loan_app_stage']==0){ ?>
                    TableManageButtons.init("tab-pending_approval");
                <?php }elseif($org['loan_app_stage']==1){ ?>
                    TableManageButtons.init("tab-approved");
                <?php }elseif($org['loan_app_stage']==2){ ?>
                    TableManageButtons.init("tab-active");                  
                <?php } ?>
                break;   
//=================END CLIENT LOAN ======================
//============== START savings module ========
            case "formSavings_account":
                TableManageButtons.init("tab-savings_account_pending");
                if(typeof response.accounts !== 'undefined' && response.accounts != ''){
                   savingsModel.clients(response.accounts);
                }
                if(typeof response.organisation_format !== 'undefined'){
                    savingsModel.organisationFormats(response.organisation_format);
                }
                if( typeof response.state_totals !== 'undefined'){
                    // savingsModel.ac_state_totals(null);
                    // savingsModel.ac_state_totals(response.state_totals );
                }
                break;
            
            case "formChange_state":                
                if( typeof response.state_totals !== 'undefined'  && response.state_totals !== ''){
                    // savingsModel.ac_state_totals(null);
                    // savingsModel.ac_state_totals(response.state_totals );
                }
                dTable['tblSavings_account'].ajax.reload(null, true);
                dTable['tblSavings_account_pending'].ajax.reload(null, true);
                savingsModel.clients(response.accounts);
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
            case "formReverseClient_subscription":    
                dTable['tblClient_subscription'].ajax.reload(null, true);
             break;
//================ START Share module =======================
            case "formPending_shares":
                dTable['tblShares_Active'].ajax.reload(null, false);
                break;
            case "formBuy_shares":
                  dTable['tblShares_Active_Account'].ajax.reload(null,true);
                  break;
            case "formConvert_shares":
              dTable['tblShares_Active_Account'].ajax.reload(null,true);
                  break;
            case "formTransfer_share":
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
            } else{
            sharesModel.name_error(0);
            dTable['tblShare_transaction'].ajax.reload(null, true);
            //dTable['tblSavings_account_pending'].ajax.reload(null, true);
            sharesModel.clients(response.accounts);
            }
            break;
            default:
                //nothing really to do here
                break;
        }
    }

 
     function display_footer_sum(api, columns) {
        $.each(columns, function (key, col) {
            //var page_total = api.column(col, {page: 'current'}).data().sum();
            var overall_total = api.column(col).data().sum();
            $(api.column(col).footer()).html((overall_total===parseInt(100))?overall_total:'<font color="red">'+overall_total+'    {Percentage is less than 100}</font>');
            //viewModel.income_total(overall_total);
            //viewModel.expens_total(overall_total);
            //$(api.column(col).footer()).html(curr_format(page_total) + "(" + curr_format(overall_total) + ") ");
        });
    }
    
    function get_filtered_admin_units(admin_unit_type, data, url) {
        $.post(
                url,
                data,
                function (response) {
                    //if (response.success) {
                    switch (admin_unit_type) {
                        case 1: //subcounties
                            userDetailModel.subcountiesList(response.subcounties);
                            if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                                userDetailModel.subcounty(ko.utils.arrayFirst(userDetailModel.subcountiesList(), function (currentSubCounty) {
                                    return (parseInt(data.subcounty_id) === parseInt(currentSubCounty.id));
                                }));
                                $('#subcounty_id').val(data.subcounty_id).trigger('change');
                            }
                            //userDetailModel.subcountiesList().valueHasMutated();
                            break;
                        case 2: //parishes
                            userDetailModel.parishesList(response.parishes);
                            if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                                userDetailModel.parish(ko.utils.arrayFirst(userDetailModel.parishesList(), function (currentParish) {
                                    return (parseInt(data.parish_id) === parseInt(currentParish.id));
                                }));
                                $('#parish_id').val(data.parish_id).trigger('change');
                            }
                            break;
                        case 3: //villages
                            userDetailModel.villagesList(response.villages);
                            if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                                userDetailModel.village(ko.utils.arrayFirst(userDetailModel.villagesList(), function (currentVillage) {
                                    return (data.village_id === currentVillage.id);
                                }));
                                $('#village_id').val(data.village_id).trigger('change');
                            }
                            break;
                        default: //do nothing if wrong call
                            break;
                    }
                    //}
                },
                'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }
    function set_selects(data) {
        edit_data(data, 'formAddress');
        //we need to set the district object  accordingly
        userDetailModel.district(ko.utils.arrayFirst(userDetailModel.districtsList(), function (currentDistrict) {
            return (parseInt(data.district_id) === parseInt(currentDistrict.id));
        }));
        $('#district_id').val(data.district_id).trigger('change');
        //as well as the subcounty object
        get_filtered_admin_units(1, data, "<?php echo site_url("subcounty/jsonList"); ?>");
        //we need to set the parish object accordingly
        get_filtered_admin_units(2, data, "<?php echo site_url("parish/jsonList"); ?>");
        //then finally thevillage object
        get_filtered_admin_units(3, data, "<?php echo site_url("village/jsonList"); ?>");
    }

     //getting new schedule
    function get_new_schedule(data, call_type) {
        var new_data = {};
        if (call_type === 1) {
            new_data['action_date'] = typeof data.action_date === 'undefined' ? client_loanModel.action_date() : data.action_date;
            new_data['id'] = client_loanModel.loan_details().id;
            new_data['loan_product_id'] = typeof data.loan_product_id === 'undefined' ? client_loanModel.loan_details().loan_product_id : data.loan_product_id;

        } else {
            new_data['amount'] = typeof data.amount === 'undefined' ? client_loanModel.amount : data.amount;
            new_data['loan_product_id'] = typeof data.loan_product_id === 'undefined' ? client_loanModel.product_name().id : data.loan_product_id;
            new_data['interest_rate'] = typeof data.interest_rate === 'undefined' ? client_loanModel.interest_rate() : data.interest_rate;
            new_data['repayment_made_every'] = typeof data.repayment_made_every === 'undefined' ? client_loanModel.repayment_made_every() : data.repayment_made_every;
            new_data['repayment_frequency'] = typeof data.repayment_frequency === 'undefined' ? client_loanModel.repayment_frequency() : data.repayment_frequency;
            new_data['installments'] = typeof data.installments === 'undefined' ? client_loanModel.installments() : data.installments;
            new_data['new_repayment_date'] = typeof data.new_repayment_date === 'undefined' ? client_loanModel.payment_date() : data.new_repayment_date;
        }
        
        var url = "<?php echo site_url("client_loan/disbursement"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //clear the the other fields because we are starting the selection afresh
                client_loanModel.payment_summation(null);
                client_loanModel.payment_schedule(null);
                client_loanModel.available_loan_fees(null);

                //populate the observables
                client_loanModel.payment_schedule(response.payment_schedule);
                client_loanModel.payment_summation(response.payment_summation);
                client_loanModel.available_loan_fees(response.available_loan_fees);
                
                },
            fail:function (jqXHR, textStatus, errorThrown) {
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
            dataType:'json',
            success:function (response) {
                //populate the observables
                client_loanModel.payment_data(response.payment_data);
                client_loanModel.penalty_amount(response.penalty_data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }

    //getting new penalty data
    function get_new_penalty(new_data) {
        var data = {};
        data['payment_date']=new_data;
        data['client_loan_id'] = client_loanModel.payment_data().id;
        data['installment_number'] = client_loanModel.payment_data().installment_number;
        var url = "<?php echo site_url("loan_installment_payment/get_penalty_data"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //populate the observables
                client_loanModel.penalty_amount(response.penalty_data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
    function get_payment_schedule(data) {
        var new_data = {};
            new_data['application_date1'] = typeof data.application_date === 'undefined' ? client_loanModel.application_date() : data.application_date;
            new_data['action_date1'] = typeof data.action_date === 'undefined' ? client_loanModel.app_action_date() : data.action_date;

            new_data['loan_product_id1'] = typeof client_loanModel.product_name() !== 'undefined' ? client_loanModel.product_name().id:client_loanModel.loan_details().loan_product_id;
            new_data['product_type_id1'] = typeof client_loanModel.product_name() !== 'undefined' ?client_loanModel.product_name().product_type_id:client_loanModel.loan_details().product_type_id;
            
            new_data['amount1'] = typeof data.amount === 'undefined' ?((typeof client_loanModel.app_amount() != 'undefined')? client_loanModel.app_amount(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().requested_amount:'' ) ) : data.amount;
            new_data['offset_period1'] = typeof data.offset_period === 'undefined' ?((typeof client_loanModel.app_offset_period() != 'undefined')?client_loanModel.app_offset_period(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().offset_period:'' ) ): data.offset_period;            
            new_data['offset_made_every1'] = typeof data.offset_made_every === 'undefined' ?((typeof client_loanModel.app_offset_every() != 'undefined')?client_loanModel.app_offset_every():( (typeof client_loanModel.loan_details() !='undefined' )?client_loanModel.loan_details().offset_made_every:'' )): data.offset_every;            
            new_data['interest_rate1'] = typeof data.interest === 'undefined' ?((typeof client_loanModel.app_interest() != 'undefined')?client_loanModel.app_interest(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().interest_rate:'')): data.interest;
            new_data['repayment_made_every1'] = typeof data.repayment_made_every === 'undefined' ?((typeof client_loanModel.app_repayment_made_every() != 'undefined')?client_loanModel.app_repayment_made_every():( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().repayment_made_every:'')): data.repayment_made_every;
            
            new_data['repayment_frequency1'] = typeof data.repayment_frequency === 'undefined' ?((typeof client_loanModel.app_repayment_frequency() != 'undefined')?client_loanModel.app_repayment_frequency(): ( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().repayment_frequency:'')): data.repayment_frequency;            
           
            new_data['installments1'] = typeof data.installments === 'undefined' ?((typeof client_loanModel.app_installments() != 'undefined')?client_loanModel.app_installments():( (typeof client_loanModel.loan_details() !='undefined')?client_loanModel.loan_details().installments:'')) : data.installments;

        var url = "<?php echo site_url("client_loan/disbursement1"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //clear the the other fields because we are starting the selection afresh
                client_loanModel.payment_summation(null);
                client_loanModel.payment_schedule(null);
                client_loanModel.available_loan_fees(null);
                

                //populate the observables
                client_loanModel.payment_schedule(response.payment_schedule);
                client_loanModel.payment_summation(response.payment_summation);
                client_loanModel.available_loan_fees(response.available_loan_fees);

                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }

    function fetch_installments(client_loan_id) {
        var url = "<?php echo site_url("repayment_schedule/jsonList2"); ?>";
        var new_data ={
                    payment_status:[2,4],
                    status_id:1,
                    client_loan_id:client_loan_id
                };
        // var data = 'payment_status <> 1 AND repayment_schedule.status_id=1 AND client_loan_id='+client_loan_id;

         $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //populate the observables
                client_loanModel.loan_installments(response.data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
    
    //for dateranger picker
    function handleDateRangePicker(startDate, endDate) {
                
        if(typeof displayed_tab !== 'undefined'){
                start_date = startDate;
                end_date = endDate;
                TableManageButtons.init(displayed_tab);
            }
    }
    <?php $this->load->view('savings_account/deposits/function_js'); ?>
     function printDiv(divName) {
         var printContents = document.getElementById(divName).innerHTML;
         var originalContents = document.body.innerHTML;
         
         document.body.innerHTML = printContents;

         window.print();

         document.body.innerHTML = originalContents;
    }
    function draw_basic_bar_graph(chart_id,chart_title,tooltip,clients,s_amount){
            Highcharts.chart(chart_id, {
                   
            title: {
                text: chart_title
            },

            subtitle: {
                text: 'Showing clients total savings'
            },
            xAxis: {
                    type: 'category',
                    categories:clients,
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

        const balance_end_date_preview = (e) => {
            e.preventDefault();
            e.stopPropagation();
            dTable['tblSavings_account'].ajax.reload(null, true);
        }

        const handlePrint_active_savings = async() => {
            let balance_end_date = $('#balance_end_date').val();
            savingsModel.isPrinting(true);
            $.ajax({
                url: '<?php echo site_url("savings_account/active_savings_print_out"); ?>',
                data: {
                    state_id: 7,
                    balance_end_date: balance_end_date,
                },
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    console.log(response)
                    savingsModel.isPrinting(false);
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    savingsModel.isPrinting(false);
                    console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
            savingsModel.isPrinting(false);
        }

        const handlePrint_member_bio_data = () => {
        $('#btn_printing_member_bio_data').css('display', 'flex');
        $('#btn_print_member_bio_data').css('display', 'none');
        $.ajax({
            url: '<?php echo site_url("member/print"); ?>',
            data: {
               status_id: 1,
               id:userDetailModel.user().id
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                $('#btn_printing_member_bio_data').css('display', 'none');
                $('#btn_print_member_bio_data').css('display', 'flex');
                $('#printable_member_bio_data').css('display', 'flex');

                $('#div_member_bio_print_out').html(response.the_page_data);
                printJS({printable: 'printable_member_bio_data', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title});
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                $('#btn_printing_member_bio_data').css('display', 'none');
                $('#btn_print_member_bio_data').css('display', 'flex');
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error:  function (err) {
                $('#btn_printing_member_bio_data').css('display', 'none');
                $('#btn_print_member_bio_data').css('display', 'flex');
            }
        });
       
    }

   
</script>
