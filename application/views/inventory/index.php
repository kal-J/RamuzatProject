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
                <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
            </ul>
            <div class="pull-right" style="padding-left: 2%">
                <div id="reportrange" class="reportrange">
                    <i class="fa fa-calendar"></i>
                    <span>December 30, 2018 - Feb 11, 2019</span> <b class="caret"></b>
                </div>
            </div>
        </div>
            
            <div class="ibox-content">
                <div class="tabs-container">  
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table"  href="#tab-fixed_asset"><i class="fa fa-list"></i> Assets</a></li>
                         <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table"  href="#tab-investment"><i class="fa fa-dollar text-bold"></i> Investments</a></li>
                    </ul>
                    <div class="tab-content">
                        <?php $this->view('inventory/fixed_asset/tab_view'); ?>
                        <?php $this->view('inventory/investment/tab_view'); ?>
                        <?php $this->view('inventory/investment/transactions/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('inventory/fixed_asset/add_modal'); ?>
 
<script>
    var dTable = {};
    var inventoryModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date, end_date,drp;
    $(document).ready(function () {
        
        jQuery.validator.addMethod("valid_acc_no", function (value, element) {
            return this.optional(element) || /^[0-9]{1,5}$/.test(value);
        }, "Please provide digits for account no");
       // $('.asset_creation').select2({dropdownParent: $("#add_asset-modal")});
        $('.service_category_modal').select2({dropdownParent: $("#add_service_category-modal")});
       
        $('form#formInventory').validate({submitHandler: saveData2});
        $('form#formInvestment').validate({submitHandler: saveData2});
        $('form#formGainLoss').validate({submitHandler: saveData2});
        $('form#formReverseTransaction').validate({submitHandler: saveData2});

        var InventoryModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
            self.pay_with = ko.observable([{"id": "Cash", "name": "Cash"}, {"id": "Bank", "name": "Bank"}, {"id": "Credit", "name": "Credit"}]);
            self.account_pay = ko.observable();

            self.account_name = ko.observable();
            self.account_name2 = ko.observable();
            self.subcat_list = ko.observableArray(<?php echo json_encode($subcat_list); ?>);
            self.depre_appre_type = ko.observable(<?php echo json_encode($depre_appre_type); ?>);
            self.depre_appre = ko.observable();
            self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
            self.transaction_channels = ko.observableArray(<?php echo json_encode($transaction_channels); ?>);
            self.transaction_channel = ko.observable();
            self.paymentModeList = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
            //added.
             self.years_since_purchase = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
             
             self.investment_details = ko.observableArray();
             
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
            self.bank_or_cash = ko.observable();
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
                edit_data(self.formatOptions(), "form");
            };
            self.payment_mode.subscribe(function (new_payment_mode) {
                if (typeof new_payment_mode !== 'undefined' && new_payment_mode != null) {
                    get_pay_with({bank_or_cash: new_payment_mode.id}, "<?php echo site_url("accounts/pay_with"); ?>");
                }
                self.paymentModeAccList(null);
                $('#account_pay_with_id').val(null).trigger('change');
            });
         
        };
            self.action_method = ko.observable();
         
               self.actionMethodList = ko.observable([ 
                {"id": "1", "name": "Add Money"},
                {"id": "2", "name": "Add a gain"},
                {"id": "3", "name": "Add a loss"},
                {"id": "4", "name": "Withdraw"}
                ]);
        inventoryModel = new InventoryModel();
        ko.applyBindings(inventoryModel);

        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>', "DD-MM-YYYY");

        daterangepicker_initializer(false, "<?php echo '01-01-2012'; ?>", "<?php echo ($end_date>date("d-m-Y"))?date("d-m-Y"):$end_date; ?>");
        var handleDataTableButtons = function (tabClicked) {
          <?php $this->view('inventory/fixed_asset/table_js'); ?>
          <?php $this->view('inventory/investment/table_js'); ?>
        };
    

         TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-fixed_asset");

    });

   function reload_data(formId, reponse_data) {
        switch (formId) {
           case 'formGainLoss':
                dTable['tblInvestment'].ajax.reload(null, false);
                break;
            case "formInvestment":
                dTable['tblInvestment'].ajax.reload(null, false);
            break;
            default:
                break;
        }
    }
   

    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        if (theData.length > 0) {
            if (theData[0]['account_name'] && theData[0]['account_code'] && theData[0]['normal_balance_side']) {//the accounts list array
                inventoryModel.accounts_list(theData);
            }
            if (theData[0]['service_category_code'] && theData[0]['service_category_name']) {//if income categories
                inventoryModel.incomeCategoryList(theData);
            }
            if (theData[0]['supplier_type_id'] && theData[0]['supplier_names']) {//if income categories
                inventoryModel.supplier_list(theData);
            }
        }
    }
    function get_pay_with(data, url) {
        console.log(data);
        $.post(
                url,
                data,
                function (response) {
                    inventoryModel.paymentModeAccList(response.pay_with);
                    if (typeof data.id !== 'undefined' && !isNaN(data.id)) {
                        inventoryModel.payment_mode(ko.utils.arrayFirst(inventoryModel.paymentModeAccList(), function (current_payment_mode) {
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
        return ko.utils.arrayFirst(inventoryModel.accounts_list(), function (current_account) {
            return current_account.id === account_id;
        });
    }
    function set_selects(data) {
       edit_data(data, 'formInventory');
        inventoryModel.cat_name(ko.utils.arrayFirst(inventoryModel.subcategoryList(), function (subcategory) {
            return (parseInt(data.pay_with_id) === parseInt(subcategory.id));
        }));
        $('#pay_with_id').val(data.pay_with_id).trigger('change');
        //as well as the subcounty object
        get_pay_with({bank_or_cash: data.pay_with_id}, "<?php echo site_url("accounts/pay_with"); ?>");
    }

     function set_selects(data) {
       edit_data(data, 'formInvestment');
        inventoryModel.cat_name(ko.utils.arrayFirst(inventoryModel.subcategoryList(), function (subcategory) {
            return (parseInt(data.pay_with_id) === parseInt(subcategory.id));
        }));
        $('#pay_with_id').val(data.pay_with_id).trigger('change');
        //as well as the subcounty object
        get_pay_with({bank_or_cash: data.pay_with_id}, "<?php echo site_url("accounts/pay_with"); ?>");
    }
    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;
        if(typeof displayed_tab !== 'undefined'){
            TableManageButtons.init(displayed_tab);
        }
    }

  


</script>
