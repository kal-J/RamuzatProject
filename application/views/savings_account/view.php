<?php
$start_date = date('d-m-Y', strtotime($fiscal_active['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_active['end_date']));

if (!empty($selected_account['child_name'])) {
    $selected_account['member_name'] = $selected_account['child_name'] . ' - [ ' . $selected_account['member_name'] . ' ]';
}

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
</style>

<?php 
    $link = $selected_account['client_type'] == 1? site_url("member/member_personal_info/".$selected_account['member_id']) :  site_url("group/view/".$selected_account['member_id'])
?>

<div class="ibox-title">
    <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("savings_account"); ?>">Saving Accounts</a></li>
        <li><span style="font-weight:bold; color:gray;  font-size:14px;"> <?php echo $title; ?> </span> ( <a
                    style="font-weight:bold;   font-size:14px;"
                    href="<?php echo $link?>"><?php echo $selected_account['member_name']; ?></a>
            )
        </li>
    </ul>
    <ul class="list-unstyled" style="margin-bottom: 0px !important;">
        <li class="dropdown pull-right">
            <!-- <i class="fa fa-modx"></i> -->
            <a class="dropdown-toggle" style=" color: #0079BF;" data-toggle="dropdown" href="#" aria-expanded="false"><i class="fa fa-tasks" style="font-size: 20px;" aria-hidden="true"></i> Actions </a>
            <ul class="dropdown-menu" x-placement="bottom-start" style="position: absolute; top: 39px; left: 0px; will-change: top, left;">
                <?php if (in_array('24', $savings_privilege)) { ?>
                    <li data-bind="visible:($root.selected_account().state_id==7 || $root.selected_account().state_id==5||$root.selected_account().state_id==17)">
                        <a href="#" class="btn btn-sm" data-toggle="modal" data-target="#add_transaction" data-bind="click: transaction"><i class="fa fa-arrow-up"></i> Deposit Cash</a>
                    </li>
                <?php }
                if (in_array('23', $savings_privilege)) { ?>
                    <!-- ko with: selected_account -->
                    <li data-bind="visible:( ($root.selected_account().state_id==7 || $root.selected_account().state_id==17) )">
                        <a href="#" class="btn btn-sm" data-bind="click: ($root.selected_account().transaction)" data-toggle="modal" data-target="#add_witdraw"><i class="fa fa-arrow-down"></i> Withdraw Cash</a>
                    </li>
                    <!-- /ko -->
                <?php }
                if (in_array('8', $savings_privilege)) { ?>
                    <li data-bind="visible: $root.selected_account().state_id !=5 && $root.selected_account().state_id !=7 && $root.selected_account().state_id !=17">
                        <a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm" data-bind="click:function(){set_action(5);}"><i class="fa fa-hourglass-start"></i>Reverse A/C
                            State</a>
                    </li>
                <?php }
                if (in_array('5', $savings_privilege)) { ?>
                    <li data-bind="visible :($root.selected_account().state_id !=7 && $root.selected_account().state_id !=18)">
                        <a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm" data-bind="click:function(){set_action(7);}"><i class="fa fa-check-circle"></i> Activate A/C</a>
                    </li>
                <?php }
                if (in_array('9', $savings_privilege)) { ?>
                    <li data-bind="visible :($root.selected_account().state_id!=17  && $root.selected_account().state_id !=5 && $root.selected_account().state_id !=18 && $root.selected_account().state_id !=12)">
                        <a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm" data-bind="click:function(){set_action(17);}"><i class="fa fa-bars"></i> Mark A/C Dormant</a>
                    </li>
                <?php }
                if (in_array('18', $savings_privilege)) { ?>
                    <li data-bind="visible : ($root.selected_account().state_id !=12 && $root.selected_account().state_id !=17  && $root.selected_account().state_id !=5 && $root.selected_account().state_id !=18)">
                        <a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm" data-bind="click:function(){set_action(12);}"><i class="fa fa-lock"></i> Lock A/C </a>
                    </li>
                <?php }
                if (in_array('4', $savings_privilege)) { ?>
                    <li data-bind="visible : ($root.selected_account().state_id !=7 && $root.selected_account().state_id !=18)">
                        <a href="#" data-toggle="modal" data-target="#change_states_modal" class="btn btn-sm" data-bind="click:function(){set_action(18);}"> <i class="fa fa-trash"></i> Delete A/C</a>
                    </li>
                <?php } ?>
            </ul>
        </li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">

            <ul class="nav nav-tabs " role="tablist">
                <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-overview"><i class="fa fa-address-book-o"></i>Account overview</a></li>
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-transaction"><i class="fa fa-bars"></i>Transaction</a></li>
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-locked"><i class="fa fa-lock"></i>Locked Amount</a></li>

                <li data-bind="visible: $root.selected_account().producttype ==2"><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-fixed"><i class="fa fa-money"></i>Fixed Amount</a></li>

                <li data-bind="visible: $root.selected_account().mandatory_saving ==1"><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-savings_schedule"><i class="fa fa-bars"></i>Schedules</a></li>

                <?php if ((in_array('12', $modules)) && (in_array('17', $modules))) { ?>
                    <!--  <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-shares"><i class="fa fa-money"></i>Shares</a></li> -->
                <?php } ?>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-overview" class="tab-pane active">
                    <div class="panel-body">
                        <div class="panel-title pull-right">
                            <div class="btn-group">
                                <!--ko with: selected_account -->
                                <span class="btn btn-sm " data-bind="text: (client_type==1)?'Individual Account' : 'Group Account', css:$root.switch_client_type_classes"></span>
                                <!--/ko -->
                                <?php if (in_array('3', $savings_privilege)) { ?>
                                    <a href="#add_savings_account" data-bind="click: initialize_edit" data-toggle="modal" class="btn btn-default btn-sm">
                                        <i class="fa fa-pencil"></i> Edit</a>

                                <?php
                                }
                                $modalTitle = "Edit Saving Account";
                                $saveButton = "Update";
                                $this->view('savings_account/add_savings_account');
                                ?>
                            </div>
                        </div>
                        <table class="table table-user-information  table-bordered table-stripped  m-t-md">
                            <tbody data-bind="with: selected_account">
                                <tr>
                                    <td><strong>Account No.</strong></td>
                                    <td colspan="5"><a data-bind="text: (account_no)?account_no:'None'"></a></td>
                                </tr>
                                <tr>
                                    <td><strong>Account Holder</strong></td>
                                    <td colspan="5" data-bind="text: (member_name )?member_name :'None'"></td>
                                </tr>
                                <tr>
                                    <td><strong>Product:</strong></td>
                                    <td colspan="3" data-bind="text: productname"></td>
                                    <td><strong>Interest Rate:</strong></td>
                                    <td data-bind="text: (interest_rate)?(interest_rate)+' %' :0"></td>
                                </tr>
                                <tr>
                                    <td><strong>Description</strong></td>
                                    <td colspan="5" data-bind="text: (description)?description:'None'"></td>
                                </tr>
                                <tr>
                                    <td><strong>Opening balance </strong></td>
                                    <td data-bind="text: (opening_balance)?curr_format(opening_balance*1):0" colspan="3"></td>
                                    <td><strong>Status </strong></td>
                                    <td data-bind="text: (state_id)?((state_id==7)?'Active':((state_id==12)?'Locked':((state_id==0)?'Deleted':(
										(state_id==5)?'Pending':((state_id==17)?'Dormant':((state_id==18)?'Deleted':'Undefined')))))):'None'"></td>
                                </tr>
                            </tbody>
                            <!-- for another table-->
                            <tbody data-bind="with: selected_account">

                                <tr>
                                    <td colspan="6">
                                        <div class="pull-left col-lg-4" data-bind="visible: $root.group_members().length">
                                            <br>
                                            <br>
                                            <strong><u>Group Members</u></strong>
                                            <div class="text-sm border-bottom">
                                                <span>
                                                    Name:&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span class="pull-right">
                                                    Contribution
                                                </span>
                                            </div>
                                            <div style="max-height:250px; overflow:auto">
                                                <!--ko foreach: $root.group_members -->
                                                <div class="text-muted text-sm">
                                                    <span>
                                                        <span class=" input-xs" data-bind="text:(member_name)?((group_leader==1)?member_name+' (GL)':member_name+' '): 'None'"></span>
                                                    </span>
                                                    <span class="pull-right">
                                                        <span class=" input-xs" data-bind="text:(real_bal)?curr_format(real_bal):' -'"></span>
                                                    </span>
                                                </div>
                                                <!--/ko-->
                                            </div>
                                            <div class="border-top">
                                                <span>
                                                    Number of members:&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span class="pull-right">
                                                    <span data-bind="text: $root.group_members().length"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="pull-right">
                                            <br>
                                            <br>
                                            <strong><u>Account Summary</u></strong>
                                            <div class="text-muted text-sm">
                                                <span>
                                                    Account Balance:&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span class="pull-right">
                                                    <span data-bind="text: curr_format(real_bal*1)"></span>
                                                </span>
                                            </div>
                                            <div class="text-muted text-sm">
                                                <span>
                                                    Locked Amount:&nbsp;&nbsp;&nbsp;
                                                </span>
                                                <span class="pull-right">
                                                    <span data-bind="text:curr_format((real_bal-cash_bal)*1)"></span>
                                                </span>
                                            </div>
                                            <div class="hr-line-dashed"></div>
                                            <span>
                                                Amount Available for withdraw :&nbsp;&nbsp;&nbsp;
                                            </span>
                                            <strong>
                                                <span class="pull-right">
                                                    <span data-bind="text:' '+curr_format((cash_bal)*1)"></span>
                                                </span>
                                            </strong>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                            <!--end of the second table-->
                        </table>
                        <br>


                    </div>
                </div>
                <?php $this->load->view('savings_account/transaction/transaction_tab.php'); ?>
                <?php $this->load->view('savings_account/transaction/reverse_modal.php'); ?>
                <?php $this->load->view('savings_account/locked/tab_view.php'); ?>
                <?php $this->load->view('savings_account/shares/shares_tab.php'); ?>
                <?php $this->load->view('savings_account/schedules/schedule_tab.php'); ?>
                <?php $this->load->view('savings_account/fixed/fixed_tab.php'); ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('savings_account/deposits/add_modal'); ?>
<?php $this->load->view('savings_account/fixed/edit_modal'); ?>
<?php $this->load->view('savings_account/locked/add_modal'); ?>
<?php $this->load->view('savings_account/transaction/edit_transaction.php'); ?>
<?php $this->load->view('savings_account/withdraws/add_modal'); ?>
<?php $this->load->view('savings_account/states/change_state_modal'); ?>
<script>
    var dTable = {};
    var saveAcctDetailModel = {};
    var TableManageButtons = {};
    var start_date, end_date;
    var displayed_tab = '';
    var current_balance = 0;
    $(document).ready(function() {
        $(".select2able").select2({
            allowClear: true
        });
        $('form#formSavings_account').validate({
            submitHandler: saveData2
        });
        $('form#formChange_state').validate({
            submitHandler: saveData2
        });
        $('form#formDeposit').validate({
            submitHandler: saveData2
        });
        $('form#formWithdraw').validate({
            submitHandler: saveData2
        });
        $('form#formTransaction').validate({
            submitHandler: saveData2
        });
        $('form#formLock_savings').validate({
            submitHandler: saveData2
        });
        $('form#formFixed_savings').validate({
            submitHandler: saveData2
        });
        $('form#formReverseTransaction').validate({
            submitHandler: saveData2
        });


        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");

        var SaveAcctDetailModel = function() {
            var self = this;
            self.ProductOptions = ko.observable(<?php echo json_encode($products); ?>);
            //self.organisationFormats = ko.observable(<?php //echo json_encode($organisation_format); 
                                                        ?>);
            self.Product = ko.observable();
            self.interestpaid = ko.observable();
            self.action_msg = ko.observable();
            self.term_lenght = ko.observable();
            self.new_account_no = ko.observable();
            self.account_state = ko.observable();
            self.selected_account = self.accountw = ko.observable(<?php echo json_encode($selected_account); ?>);
            self.fixed_type = ko.observable(0);
            self.saving_ahead = ko.observable();
            self.repayment_made_every = function(repayment_key) {
                return periods[parseInt(repayment_key) - parseInt(1)];
            }

            self.active_loans = ko.observableArray(<?php echo json_encode($active_loans); ?>);
            self.loan_id = ko.observable();
            self.paid_principal = ko.observable(0);
            self.amountCalOptions = ko.observableArray(<?php echo json_encode($amountcalculatedas); ?>);
            self.payment_modes = ko.observable(<?php echo (isset($payment_modes)) ? json_encode($payment_modes) : ''; ?>);
            self.payment_mode = ko.observable();

            self.amountcalculatedas = ko.observable();
            self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
            self.withdraw_fees = ko.observableArray(<?php echo json_encode($withdraw_fees); ?>);
            self.deposit_fees = ko.observableArray(<?php echo json_encode($deposit_fees); ?>);
            self.account_balance = ko.observable(self.selected_account().cash_bal);
            self.group_members = ko.observableArray(<?php echo (isset($group_members) === true) ? json_encode($group_members) : ''; ?>);
            self.fees = ko.observable();
            self.User = ko.observable();
            self.tchannels = ko.observable();
            self.deposit_amount = ko.observable(self.selected_account().mindepositamount);
            self.withdraw_amount = ko.observable(0);
            self.isPrinting = ko.observable(false);

            self.transaction = function() {
                self.selected_account(<?php echo json_encode($selected_account); ?>);
                self.account_state();
            };

            self.switch_client_type_classes = ko.pureComputed(function() {
                return (parseInt(self.selected_account().client_type) == 1) ? "btn-primary" : "btn-secondary";
            }, this);

            //range fees charge calculation
            self.available_savings_range_fees = ko.observableArray(<?php echo (!empty($available_savings_range_fees) ? json_encode($available_savings_range_fees) : '') ?>);
            self.compute_fee_amount = function(savings_fee_id, amount) {
                var available_ranges;
                var fee_amount = 0;
                if (self.available_savings_range_fees()) {
                    available_ranges = ko.utils.arrayFilter(self.available_savings_range_fees(), function(data) {
                        return parseInt(data.saving_fee_id) == parseInt(savings_fee_id);
                    });

                    for (var i = 0; i <= available_ranges.length - 1; i++) {
                        if (parseFloat(available_ranges[i].max_range) != '0.00') {

                            if (parseFloat(amount) >= parseFloat(available_ranges[i].min_range) && parseFloat(amount) <= parseFloat(available_ranges[i].max_range)) {

                                fee_amount = parseInt(available_ranges[i].calculatedas_id) == 1 ? (parseFloat(available_ranges[i].range_amount) * parseFloat(amount) / parseFloat(100)) : parseFloat(available_ranges[i].range_amount);
                                break;
                            }
                        } else if (parseFloat(available_ranges[i].max_range) == '0.00' && parseFloat(available_ranges[i].min_range) != '0.00') {
                            if (parseFloat(amount) >= parseFloat(available_ranges[i].min_range)) {
                                fee_amount = parseInt(available_ranges[i].calculatedas_id) == 1 ? (parseFloat(available_ranges[i].range_amount) * parseFloat(amount) / parseFloat(100)) : parseFloat(available_ranges[i].range_amount);
                                break;
                            }
                        }
                    }
                }
                return fee_amount;
            }

            //interest range rate calculation
            self.available_interest_range_rates = ko.observableArray(<?php echo (!empty($available_interest_range_rates) ? json_encode($available_interest_range_rates) : '') ?>);
            self.compute_rate_amount = function(product_id, term_length) {
                var available_rates;
                var range_rate = 0;
                if (self.available_interest_range_rates()) {
                    available_rates = ko.utils.arrayFilter(self.available_interest_range_rates(), function(data) {
                        return parseInt(data.product_id) == parseInt(product_id);
                    });

                    for (var i = 0; i <= available_rates.length - 1; i++) {
                        if (parseFloat(available_rates[i].max_range) != '0.00') {
                            if (parseFloat(term_length) >= parseFloat(available_rates[i].min_range) && parseFloat(term_length) <= parseFloat(available_rates[i].max_range)) {
                                range_rate = available_rates[i].range_amount;
                                break;
                            }
                        } else if (parseFloat(available_rates[i].max_range) == '0.00' && parseFloat(available_rates[i].min_range) != '0.00') {
                            if (parseFloat(term_length) >= parseFloat(available_rates[i].min_range)) {
                                range_rate = available_rates[i].range_amount;
                                break;
                            }
                        }
                    }
                }
                return range_rate;
            }


            //After charges on deposit
            self.totaldepositCharges = ko.computed(function() {
                total = 0;
                ko.utils.arrayForEach(self.deposit_fees(), function(depositfee) {
                    if (depositfee.cal_method_id == 1) {
                        total += (parseFloat(depositfee.amount) * (self.deposit_amount())) / 100;
                    } else if (depositfee.cal_method_id == 3) {
                        total += parseFloat(self.compute_fee_amount(depositfee.savings_fees_id, self.deposit_amount()));
                    } else {
                        total += parseFloat(depositfee.amount);
                    }
                });
                return total; //to be reviewed later...
            }, this);


            //After withdraw charges
            self.totalwithdrawCharges = ko.computed(function() {
                total = 0;
                ko.utils.arrayForEach(self.withdraw_fees(), function(withdrawfee) {
                    if (withdrawfee.cal_method_id == 1) { //1-percentage 2-fixed
                        total += (parseFloat(withdrawfee.amount) * (self.withdraw_amount())) / 100;
                    } else if (withdrawfee.cal_method_id == 3) {
                        total += parseFloat(self.compute_fee_amount(withdrawfee.savings_fees_id, self.withdraw_amount()));
                    } else {
                        total += parseFloat(withdrawfee.amount);
                    }
                });
                return total; //to be reviewed later...
            }, this);

            self.printStatement = function() {
                if (typeof moment(start_date, 'X').format('YYYY-MM-DD') !== 'undefined') {
                    window.open("<?php echo site_url('savings_account/AcStatement/') . $acc_id . "/"; ?>" + moment(start_date, 'X').format('YYYY-MM-DD') + "/" + moment(end_date, 'X').format('YYYY-MM-DD'), '_blank');

                    /* window.location = "<?php //echo site_url('savings_account/AcStatement/') . $acc_id . "/"; 
                                            ?>" + moment(start_date, 'X').format('YYYY-MM-DD') + "/" + moment(end_date, 'X').format('YYYY-MM-DD'); */
                }
            }
            self.printTransaction = function() {
                if (typeof moment(start_date, 'X').format('YYYY-MM-DD') !== 'undefined') {
                    window.open("<?php echo site_url('transaction/savings_transaction_print_out2/') . $acc_id . "/"; ?>" + moment(start_date, 'X').format('YYYY-MM-DD') + "/" + moment(end_date, 'X').format('YYYY-MM-DD'), '_blank');

                   
                }
            }

            self.initialize_edit = function() {
                edit_data(self.selected_account(), "formSavings_account");
                //edit_data(self.selected_account(),"formDepositProductInterest");
            }
            self.set_action = function(state_id) {

                if (state_id == 5) {
                    self.action_msg("change the account state to pending");
                    self.account_state(5);
                }
                if (state_id == 7) {
                    self.action_msg("activate this account");
                    self.account_state(7);
                }
                if (state_id == 12) {
                    self.action_msg("lock this account");
                    self.account_state(12);
                }
                if (state_id == 17) {
                    self.action_msg("make this acount dormant");
                    self.account_state(17);
                }
                if (state_id == 18) {
                    self.action_msg("delete this account");
                    self.account_state(18);
                }
            };

            self.display_table = function(data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
        };

        var handleDataTableButtons = function(tabClicked) {
            <?php $this->load->view('savings_account/transaction/transaction_js'); ?>
            <?php $this->load->view('savings_account/locked/table_js'); ?>
            <?php $this->load->view('savings_account/shares/shares_js');  ?>
            <?php $this->load->view('savings_account/schedules/schedule_js');  ?>
            <?php $this->load->view('savings_account/fixed/fixed_js');  ?>
            <?php //$this->load->view('savings_account/withdraws/withdraws_js.php');   
            ?>
        };


        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        daterangepicker_initializer();

        TableManageButtons.init("tab-transaction");
        TableManageButtons.init("tab-overview");
        TableManageButtons.init("tab-charges");

        saveAcctDetailModel = new SaveAcctDetailModel();
        ko.applyBindings(saveAcctDetailModel);
        //alert(savePdtDetailModel.totalwithdrawCharges())

    }); //end of document.ready

    function handleDateRangePicker(startDate, endDate) {
        if (typeof displayed_tab !== 'undefined') {

        }
        start_date = startDate;
        end_date = endDate;
        TableManageButtons.init(displayed_tab);
        //dashModel.updateData();
    }

    function changeFunc() {
        var selectBox = document.getElementById("selectBox");
        var selectedValue = selectBox.options[selectBox.selectedIndex].value;
        if (selectedValue == "0") {
            $('#textboxes').show();
        } else {
            $('#textboxes').hide();
        }
    }

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formSavings_account":
            case "formChange_state":
            case "formWithdraw":
            case "formLock_savings":
            case "formDeposit":
                if (typeof response.insert_id !== 'undefined') {
                    window.location = "<?php echo site_url('transaction/print_receipt/'); ?>" + response.insert_id;
                }
                dTable['tblTransaction'].ajax.reload(null, true);
                saveAcctDetailModel.selected_account(response.accounts);
                //saveAcctDetailModel.group_members(response.group_members);
                break;
            case "formReverseTransaction":
                dTable['tblTransaction'].ajax.reload(null, true);
                break;
            case "formFixed_savings":
                dTable['tblFixed_savings'].ajax.reload(null, true);
                break;
            default:
                //nothing really to do here
                break;
        }
    }

    function unlock_savings(msg, data, url, tblId) {

        Swal.fire({
            title: "Are you sure?",
            text: msg,
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "green",
            confirmButtonText: "Yes, Unlock!",
            cancelButtonText: "No, cancel!",
            closeOnConfirm: false
        }).then(function(result) {
            if(result.isConfirmed){
              Swal.showLoading();
                $.post(
                    url,
                    data,
                    function (response) {
                        if (response.success) {
                            setTimeout(function () {
                                toastr.success(response.message, "Success!");
                                //any other tasks(function) to be run here
                                if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                                    dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                                }
                                if (typeof reload_data === "function") {
                                    reload_data(tblId.replace("tbl", "form"), response);
                                }
                            }, 1000);
                        } else {
                            toastr.error("", "Operation failed. Reason(s):<ol>" + response.message + "</ol>", "Deletion failure!");
                        }
                    },
                    'json').fail(function (jqXHR, textStatus, errorThrown) {
                    network_error(jqXHR, textStatus, errorThrown, $("#myform"));
                });

            }
        })
        /*
        Swal.fire({
                title: "Are you sure?",
                text: msg,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "green",
                confirmButtonText: "Yes, Unlock!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false
            },
            function() {
                $.post(
                    url,
                    data,
                    function(response) {
                        if (response.success) {
                            setTimeout(function() {
                                toastr.success(response.message, "Success!");
                                //any other tasks(function) to be run here
                                if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                                    dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                                }
                                if (typeof reload_data === "function") {
                                    reload_data(tblId.replace("tbl", "form"), response);
                                }
                            }, 1000);
                        } else {
                            toastr.error("", "Operation failed. Reason(s):<ol>" + response.message + "</ol>", "Deletion failure!");
                        }
                    },
                    'json').fail(function(jqXHR, textStatus, errorThrown) {
                    network_error(jqXHR, textStatus, errorThrown, $("#myform"));
                });
                swal.close();
            });
            */
    }//End of the deleting function

    const filter_by_date = () => {
        dTable['tblTransaction'].ajax.reload(null, true);
    }
</script>