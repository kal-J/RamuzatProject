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
                <li><a href="<?php echo site_url("inventory"); ?>">Investment</a></li>
                <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
            </ul>
        </div>
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist" data-bind="with: investment_details">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-investment_trans"><i class="fa fa-bars"></i> Investment transactions</a></li> 
                        
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-investment_trans" class="tab-pane active">
                            <?php $this->load->view('inventory/investment/transactions/tab_view');?>
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var viewModel = {};
    var displayed_tab = '';
    var TableManageButtons = {};
    $(document).ready(function () {
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); 
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");

         $('form#formGainLoss').validate({submitHandler: saveData2});
          $('form#formReverseTransaction').validate({submitHandler: saveData2});
         


        var ViewModel = function (){
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
            self.pay_with = ko.observable([{"id": "Cash", "name": "Cash"}, {"id": "Bank", "name": "Bank"}]);

            self.account_pay = ko.observable();
            self.transaction_channel = ko.observable();
            self.account_name = ko.observable();
            self.account_name2 = ko.observable();
            self.investment_data=ko.observableArray(<?php echo !empty($investment_data) ?  json_encode($investment_data) : ''; ?>);
            self.income_items = ko.observable(<?php echo json_encode($income_items); ?>);
            self.income_item = ko.observable();
            self.expense_items = ko.observable(<?php echo json_encode($expense_items); ?>);
            self.expense_item = ko.observable();
            self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
            self.subcat_list = ko.observableArray(<?php echo json_encode($subcat_list); ?>);
            self.transaction_channels = ko.observableArray(<?php echo json_encode($transaction_channels);?>);
            self.paymentModeList = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
            self.paymentModeAccList = ko.observableArray();
            self.investment_details = ko.observableArray([]);
            self.payment_mode = ko.observable();
            self.payment_mode2 =ko.observable();
            self.bank_or_cash = ko.observable();

            self.pay_label = ko.computed(function (){
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

             self.pay_label2 = ko.computed(function (){
                var payment_mode_acc_text = "Cash";
                if (typeof self.payment_mode2() !== 'undefined' && self.payment_mode2()) {
                    if (self.payment_mode2().id == 2) {
                        payment_mode_acc_text = "Bank";
                    }
                    if (self.payment_mode2().id == 3) {
                        payment_mode_acc_text = "Receivable";
                    }
                }
                return payment_mode_acc_text;
            });
    
            self.filteredAccountToList = ko.computed(function () {
                if (self.account_name()) {
                    return ko.utils.arrayFilter(self.accounts_list(), function (accountto) {
                        return (parseInt(self.account_name().id) !== parseInt(accountto.id));
                    });
                }
            });
            //return concatenated names of the Account
            self.detail_accounts = ko.computed(function () {
                return ko.utils.arrayFilter(self.accounts_list(), function (account) {
                    //return account.account_type_id == 2;
                    return true;
                });
            });
            self.accountsList = ko.computed(function () {
                var data = $.map(self.accounts_list(), function (account) {
                    account.disabled = account.account_type_id == 1; // replace pk with your identifier
                    return account;
                });
                return data;
            });
            self.select2accounts = function (sub_category_id) {
                var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function (account) {
                    return Array.isArray(sub_category_id) ? (check_in_array(account.sub_category_id, sub_category_id)) : (account.sub_category_id == sub_category_id);
                });
                return filtered_accounts;
            };
            self.formatAccount2 = function (account) {
                return account.account_code + " " + account.account_name;
            };
            self.initialize_edit = function () {
            };
            self.payment_mode.subscribe(function (new_payment_mode) {
                if (typeof new_payment_mode !== 'undefined' && new_payment_mode != null) {
                    get_pay_with({bank_or_cash: new_payment_mode.id}, "<?php echo site_url("accounts/pay_with"); ?>");
                }
                self.paymentModeAccList(null);
                $('#account_pay_with_id').val(null).trigger('change');
            });

             self.payment_mode2.subscribe(function (new_payment_mode) {
                if (typeof new_payment_mode !== 'undefined' && new_payment_mode != null) {
                    get_pay_with({bank_or_cash: new_payment_mode.id,action:1}, "<?php echo site_url("accounts/pay_with"); ?>");
                }
                self.paymentModeAccList(null);
                $('#account_pay_with_id').val(null).trigger('change');
            });
             
                self.action_method = ko.observable();
                self.actionMethodList = ko.observable([ 
                {"id": "1", "name": "Add Money"},
                {"id": "2", "name": "Add a gain"},
                {"id": "3", "name": "Add a loss"},
                {"id": "4", "name": "Withdraw"}
                ]);
               
        };


        viewModel = new ViewModel();
        ko.applyBindings(viewModel);


        var handleDataTableButtons = function (tabClicked) {
          <?php  $this->load->view("inventory/investment/transactions/table_js"); ?>
 
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tabClicked) {
                    handleDataTableButtons(tabClicked);
                }
            };
        }();

        //TableManageButtons.init("tab-asset_income");
        TableManageButtons.init("tab-investment_trans");

    });

 function reload_data(formId, reponse_data) {
        switch (formId) {
           case 'formGainLoss':
                dTable['tblInvestment_trans'].ajax.reload(null, false);
                break;
          case 'formReverseTransaction':
                dTable['tblInvestment_trans'].ajax.reload(null, false);
                break;

            default:
                break;
        }
    }
    function get_pay_with(data, url) {
        //console.log(data);
        $.post(
                url,
                data,
                function (response) {
                    viewModel.paymentModeAccList(response.pay_with);
                    if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                        viewModel.payment_mode(ko.utils.arrayFirst(viewModel.paymentModeAccList(), function (current_payment_mode) {
                            return (parseInt(data.pay_with_id) === parseInt(current_payment_mode.id));
                        }));
                         viewModel.payment_mode2(ko.utils.arrayFirst(viewModel.paymentModeAccList(), function (current_payment_mode) {
                            return (parseInt(data.pay_with_id) === parseInt(current_payment_mode.id));
                        }));
                        $('#account_pay_with_id').val(data.pay_with_id).trigger('change');
                    }
                },
                'json').fail(function (jqXHR, textStatus, errorThrown) {
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
        });
    }
   
    function get_account(account_id) {
        return ko.utils.arrayFirst(viewModel.accounts_list(), function (current_account) {
            return current_account.id === account_id;
        });
    }
    function set_selects(data) {
        edit_data(data, 'formGainLoss');
        //set the account from accordingly
        $('#account_from_id').val(data.account_id).trigger('change');
        //as well as the account to object

        viewModel.account_to_id(ko.utils.arrayFirst(viewModel.accountToList(), function (accountto) {
            return (parseInt(data.second_account_id) === parseInt(accountto.id));
        }));
        $('#account_to_id').val(data.second_account_id).trigger('change');

        viewModel.cat_name(ko.utils.arrayFirst(viewModel.subcategoryList(), function (subcategory) {
            return (parseInt(data.pay_with_id) === parseInt(subcategory.id));
        }));
        $('#pay_with_id').val(data.pay_with_id).trigger('change');
        //as well as the subcounty object
        get_pay_with({bank_or_cash: data.pay_with_id}, "<?php echo site_url("accounts/pay_with"); ?>");
    }
    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        // //compute the sums of the collateral or guarantors
        // if (theData.length > 0) {
        //     if (theData[0]['financial_year_id']) {//depreciation data array
        //         viewModel.depreciation_amount(sumUp(theData, 'amount'));
        //     }
        //     if (theData[0]['transaction_channel_id']) {//depreciation data array
        //         viewModel.asset_paid_amount(sumUp(theData, 'amount'));
        //     }
        // }
    }
 
    
</script>

