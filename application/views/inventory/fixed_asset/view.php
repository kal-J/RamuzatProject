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
                <li><a href="<?php echo site_url("inventory"); ?>">Assets</a></li>
                <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
            </ul>
        </div>
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist" data-bind="with: fixed_asset_detail">
                        <li><a class="nav-link active" data-toggle="tab" href="#tab-details"><i class="fa fa-list-alt"></i> Asset Details</a></li> 
                        <li data-bind="visible:  parseInt(depre_appre_id) ==parseInt(1)"><a class="nav-link" data-toggle="tab" data-bind="click: $parent.display_table" href="#tab-depreciation"><i class="fa fa-line-chart"></i> Depreciation</a></li> 
                        <!--appreciation-->
                          <li data-bind="visible:  parseInt(depre_appre_id) ==parseInt(2)"><a class="nav-link" data-toggle="tab" data-bind="click: $parent.display_table" href="#tab-appreciation"><i class="fa fa-line-chart"></i> Appreciation</a></li> 
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: $parent.display_table" href="#tab-asset_payment"><i class="fa fa-line-chart"></i> Payments</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: $parent.display_table" href="#tab-asset_income"><i class="fa fa-line-money"></i> Income</a></li>
                        <li><a class="nav-link" data-toggle="tab" data-bind="click: $parent.display_table" href="#tab-asset_expense"><i class="fa fa-line-chart"></i> Expenses</a></li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-details" class="tab-pane active">
                            <?php $this->load->view('inventory/fixed_asset/details_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-depreciation" class="tab-pane">
                            <?php $this->load->view('inventory/fixed_asset/depreciation/tab_view'); ?>
                        </div>
                        <!--apreciation-->

                         <div role="tabpanel" id="tab-appreciation" class="tab-pane">
                            <?php $this->load->view('inventory/fixed_asset/appreciation/tab_view'); ?>
                        </div>

                        <div role="tabpanel" id="tab-asset_payment" class="tab-pane">
                            <?php $this->load->view('inventory/fixed_asset/payments/tab_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-asset_income" class="tab-pane">
                            <?php $this->load->view('inventory/fixed_asset/income/tab_view'); ?>
                        </div>
                        <div role="tabpanel" id="tab-asset_expense" class="tab-pane">
                            <?php $this->load->view('inventory/fixed_asset/expense/tab_view'); ?>
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
        $('form#formDepreciation').validate({submitHandler: saveData2});
        $('form#formAppreciation').validate({submitHandler: saveData2});
        $('form#formAsset_payment').validate({submitHandler: saveData2});
        $('form#formAsset_selling').validate({submitHandler: saveData2});
        $('form#formAsset_income').validate({submitHandler: saveData2});
        $('form#formAsset_expense').validate({submitHandler: saveData2});
        $('form#formAddAsset_income').validate({submitHandler: saveData2});
        $('form#formReverseAsset_payment').validate({submitHandler: saveData2});
        //$('form#formAddAsset_expense').validate({submitHandler: saveData2});
        //$('form#formAddAsset_payment').validate({submitHandler: saveData2});

       $("form#formAddAsset_expense").validate({
        rules: {
              
                fund_source_account_id:{
                    remote: {
                    url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                    type: "post",
                    data: {
                        amount: function () {
                            return $("form#formAddAsset_expense input[name='amount']").val();
                        },
                        payment_mode: function () {
                            return $("form#formAddAsset_expense select[name='payment_id']").val();
                        },
                        fisc_date_from: moment(start_date,'X').format('YYYY-MM-DD'), 
                        fisc_date_to: moment(end_date,'X').format('YYYY-MM-DD'),
                        account_id: function () {
                            return $("form#formAddAsset_expense select[name='fund_source_account_id']").val();
                        }
                    }
                }
               }
       },submitHandler: saveData2});


       $("form#formAddAsset_payment").validate({
        rules: {
              
                fund_source_account_id:{
                    remote: {
                    url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                    type: "post",
                    data: {
                        amount: function () {
                            return $("form#formAddAsset_payment input[name='amount']").val();
                        },
                        payment_mode: function () {
                            return $("form#formAddAsset_payment select[name='payment_id']").val();
                        },
                        fisc_date_from: moment(start_date,'X').format('YYYY-MM-DD'), 
                        fisc_date_to: moment(end_date,'X').format('YYYY-MM-DD'),
                        account_id: function () {
                            return $("form#formAddAsset_payment select[name='fund_source_account_id']").val();
                        }
                    }
                }
               }
       },submitHandler: saveData2});
       //form asset selling 
        $("form#formAddAsset_selling").validate({
        rules: {
              
                fund_source_account_id:{
                    remote: {
                    url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                    type: "post",
                    data: {
                        amount: function () {
                            return $("form#formAddAsset_selling input[name='amount']").val();
                        },
                        payment_mode2: function () {
                            return $("form#formAddAsset_selling select[name='payment_id']").val();
                        },
                        fisc_date_from: moment(start_date,'X').format('YYYY-MM-DD'), 
                        fisc_date_to: moment(end_date,'X').format('YYYY-MM-DD'),
                        account_id: function () {
                            return $("form#formAddAsset_selling select[name='fund_source_account_id']").val();
                        }
                    }
                }
               }
       },submitHandler: saveData2});


        var ViewModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
                TableManageButtons.init(displayed_tab);
            };
            self.pay_with = ko.observable([{"id": "Cash", "name": "Cash"}, {"id": "Bank", "name": "Bank"}, {"id": "Credit", "name": "Credit"}]);
            self.account_pay = ko.observable();
            self.fixed_asset_detail = ko.observable(<?php echo json_encode($fixed_asset); ?>);
            self.asset_paid_amount = ko.observable(<?php echo json_encode($asset_paid_amount); ?>);
            self.depreciation_amount = ko.observable(0);
            self.appreciation_amount = ko.observable(0);
            self.transaction_channel = ko.observable();
            self.account_name = ko.observable();
            self.account_name2 = ko.observable();
            self.income_items = ko.observable(<?php echo json_encode($income_items); ?>);
            self.income_item = ko.observable();
            self.expense_items = ko.observable(<?php echo json_encode($expense_items); ?>);
            self.expense_item = ko.observable();
            self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
            self.subcat_list = ko.observableArray(<?php echo json_encode($subcat_list); ?>);
            self.transaction_channels = ko.observableArray(<?php echo json_encode($transaction_channels);?>);
            self.paymentModeList = ko.observableArray(<?php echo json_encode($payment_modes); ?>);
            self.paymentModeAccList = ko.observableArray();
            self.payment_mode = ko.observable();
            self.payment_mode2 = ko.observable();
            self.bank_or_cash = ko.observable();

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

             self.pay_label2 = ko.computed(function () {
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
             //disposal methods
               this.disposal_method = ko.observable();
               this.hide_or_shown2;
         
               this.disposalMethodList = ko.observable([
                {"id": "1", "name": "Disposal with no gain or loss"}, 
                {"id": "2", "name": "Disposal with a loss"},
                {"id": "3", "name": "Disposal with a gain"}
                ]);
           
        };

        viewModel = new ViewModel();
        ko.applyBindings(viewModel);
        var handleDataTableButtons = function (tabClicked) {
<?php $this->load->view("inventory/fixed_asset/depreciation/table_js"); ?>
<?php $this->load->view("inventory/fixed_asset/appreciation/table_js"); ?>
<?php $this->load->view("inventory/fixed_asset/payments/table_js"); ?>
<?php $this->load->view("inventory/fixed_asset/income/table_js"); ?>
<?php $this->load->view("inventory/fixed_asset/expense/table_js"); ?>
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
        TableManageButtons.init("tab-asset_expense");

    });

 function reload_data(formId, reponse_data) {
        switch (formId) {
           case 'formAsset_expense':
                dTable['tblAsset_expense'].ajax.reload(null, false);
                break;
            case "formAsset_income":
                dTable['tblAsset_income'].ajax.reload(null, false);
            break;
            case "formAddAsset_income":
                dTable['tblAsset_income'].ajax.reload(null, false);
            break;
            case "formAddAsset_expense":
                dTable['tblAsset_income'].ajax.reload(null, false);
            break;
            case "formReverseAsset_payment":
                dTable['tblAsset_payment'].ajax.reload(null, false);
            break;
            case "formAsset_payment":
                dTable['tblAsset_payment'].ajax.reload(null, false);
            break;
            case "formAddAsset_payment":
                dTable['tblAsset_payment'].ajax.reload(null, false);
               // viewModel.fixed_asset_detail(reponse_data.fixed_asset );
                viewModel.asset_paid_amount( sumUp( reponse_data.asset_payment, 'amount' ) );
            break;
             case "formAddAsset_selling":
                dTable['tblAsset_payment'].ajax.reload(null, false);
               // viewModel.fixed_asset_detail(reponse_data.fixed_asset );
                viewModel.asset_paid_amount( sumUp( reponse_data.asset_payment, 'amount' ) );
            break;
            case "formDepreciation":
                dTable['tblDepreciation'].ajax.reload(null, false);

            break;

             case "formAppreciation":
                dTable['tblAppreciation'].ajax.reload(null, false);
                viewModel.asset_paid_amount( sumUp( reponse_data.asset_payment, 'amount' ) );
                
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
        edit_data(data, 'formAsset_expense');
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
  //hide or show payment modes based on the disposal scenario.

   $("#disposal_method").on('change',function(){
    var disposal_id=$(this).val();
    if(disposal_id==1){
    $(".show_or_hide").hide();
    }
    else{
    $(".show_or_hide").show();
   }

  

       });
       let monthlyAssetPayment = () =>{
        const fixed_asset_id = viewModel.fixed_asset_detail().id;
        const depre_appre_id = viewModel.fixed_asset_detail().depre_appre_id;
        $.ajax({
            url:"<?php echo site_url('inventory/monthlyDepreAprePayDate')?>",
            method:'POST',
            dataType: 'json',
            data:{id:fixed_asset_id,depre_appre_id:depre_appre_id},
            success: function (response) { 
                
               var s = '<option value="-1">--select--</option>';  
               for (var i = 0; i < response.length; i++) {  
                  
                   s += '<option value="' + response[i] + '">' + response[i] + '</option>';  
               }  
               $("#transaction_date").html(s); 
               $("#appre_transaction_date").html(s); 

               $("#transaction_date").on('change',()=>{
                const year =  $("#transaction_date").val();
                const financial_yr = year.substr(0,4);
                const fy_option ='<option value="' + financial_yr+ '">' + financial_yr + '</option>';
               $('#financial_year_id').html(fy_option);
               })
            
               
              
              }
               
          
        })
    }

</script>

