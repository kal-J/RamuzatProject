<?php
    $start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
    $end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
             <ul class="breadcrumb">
                <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                <li><a href="<?php echo site_url("accounts"); ?>">Dividend Declaration</a></li>
                <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
            </ul>
        </div>
            <div class="ibox-content">
            <div class="pull-right add-record-btn">
            <?php if(in_array('3', $accounts_privilege)){ ?>
               <!-- <button class="btn btn-primary btn-sm" type="button" data-toggle="modal" data-target="#add_income-modal"><i class="fa fa-plus-circle"></i> Update Income Details </button> -->
           <?php } ?>
           </div>
                <div class="tabs-container">
                <ul class="nav nav-tabs" role="tablist">
                    <li><a class="nav-link active" data-toggle="tab" href="#tab-details"><i class="fa fa-list-alt"></i>Declaration Details</a></li> 
                    <li><a class="nav-link" data-toggle="tab"  data-bind="click: display_table" href="#tab-dividend_paid"><i class="fa fa-money"></i>Dividends Payment</a></li>
                    <li><a class="nav-link" data-toggle="tab"  data-bind="click: display_table" href="#tab-stakeholders"><i class="fa fa-users"></i>Stakeholders</a></li>
                </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-details" class="tab-pane active">
                            <?php $this->load->view('accounts/dividend/declaration/details'); ?>
                        </div>
                        <div role="tabpanel" id="tab-dividend_paid" class="tab-pane">
                            <?php $this->load->view('accounts/dividend/declaration/dividend_paid/tab_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-stakeholders" class="tab-pane">
                            <?php $this->load->view('accounts/dividend/declaration/share_holder/tab_view'); ?>
                        </div>
                    </div>
                </div>
             <?php $this->load->view('accounts/dividend/declaration/dividend_paid/add_modal'); ?>
             <?php $this->load->view('accounts/dividend/declaration/dividend_paid/print_modal'); ?>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var viewModel = {};
    var inventoryModel = {};
    var TableManageButtons = {};
    var start_date, end_date,drp;
    $(document).ready(function () {
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); 
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
        $('.dividend_payment_selects').select2({dropdownParent: $("#pay_dividend-modal")});
        $("form#formDividend_payment").validate({
            rules: {

                fund_source_account_id:{
                    remote: {
                        url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                        type: "post",
                        data: {
                            amount: function () {
                                return $("form#formDividend_payment input[name='total_dividends']").val();
                            },
                            payment_mode: function () {
                                return $("form#formDividend_payment select[name='payment_id']").val();
                            },
                            fisc_date_from: moment(start_date,'X').format('YYYY-MM-DD'),
                            fisc_date_to: moment(end_date,'X').format('YYYY-MM-DD'),
                            account_id: function () {
                                return $("form#formDividend_payment select[name='fund_source_account_id']").val();
                            }
                        }
                    }
                }
            },submitHandler: saveData2});
        var ViewModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
            TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.dividend_declaration = ko.observable(<?php echo json_encode($dividend_declaration); ?>);


            //payment mode
            self.paymentModeList = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
            self.paymentModeAccList = ko.observableArray();
            self.payment_mode = ko.observable();
            self.pay_label = ko.computed(function () {
                var payment_mode_acc_text = "Cash";
                if (typeof self.payment_mode() !== 'undefined' && self.payment_mode()) {
                    if (self.payment_mode().id == 2) {
                        payment_mode_acc_text = "Bank";
                    }
                    if (self.payment_mode().id == 3) {
                        payment_mode_acc_text = "Credit";
                    }
                }
                return payment_mode_acc_text;
            });
            self.payment_mode.subscribe(function (new_payment_mode) {
                if (typeof new_payment_mode !== 'undefined' && new_payment_mode != null) {
                    get_pay_with({bank_or_cash: new_payment_mode.id}, "<?php echo site_url("accounts/pay_with"); ?>");
                }
                self.paymentModeAccList(null);
                $('#account_pay_with_id').val(null).trigger('change');
            });
            self.formatAccount2 = function (account) {
                return account.account_code + " " + account.account_name;
            };

            ///end

            self.authorizer_list = ko.observableArray(<?php echo json_encode($staff_list); ?>);
            self.initialize_edit = function () {
                edit_data(self.income_detail(), "formIncome");
            };
        };

        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
         daterangepicker_initializer();

        var handleDataTableButtons = function (tabClicked) {
<?php $this->load->view("accounts/dividend/declaration/dividend_paid/table_js"); ?>
<?php $this->load->view("accounts/dividend/declaration/share_holder/table_js"); ?>
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init();

    });

    function get_pay_with(data, url) {
        console.log(data);
        $.post(
            url,
            data,
            function (response) {
                viewModel.paymentModeAccList(response.pay_with);
                if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                    viewModel.payment_mode(ko.utils.arrayFirst(viewModel.paymentModeAccList(), function (current_payment_mode) {
                        return (parseInt(data.pay_with_id) === parseInt(current_payment_mode.id));
                    }));
                    $('#account_pay_with_id').val(data.pay_with_id).trigger('change');
                }
            },
            'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }

    function set_selects(data) {
        edit_data(data, 'formIncome');
        //set the account from accordingly
        $('#account_from_id').val(data.account_id).trigger('change');
        //as well as the account to object

        viewModel.account_to_id(ko.utils.arrayFirst(viewModel.accountToList(), function (accountto) {
            return (parseInt(data.second_account_id) === parseInt(accountto.id));
        }));
        $('#account_to_id').val(data.second_account_id).trigger('change');
    }
    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        //compute the sums of the collateral or guarantors
        if (theData.length > 0) {
            if (theData[0]['transaction_channel_id']) {//depreciation data array
                viewModel.income_paid_amount(sumUp(theData, 'amount'));
            }
        }
    }
     function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
            TableManageButtons.init(displayed_tab);
    }
</script>