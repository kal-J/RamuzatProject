<div class="ibox-title">
     <ul class="breadcrumb">
        <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
        <li><a href="<?php echo site_url("setting"); ?>">Settings</a></li>
        <li><span  style="font-weight:bold; color:gray;  font-size:14px;"><?php echo $title; ?></span></li>
    </ul>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
            <ul class="nav nav-tabs" role="tablist">
                <li><a class="nav-link active" data-toggle="tab" href="#tab-1">Product Details</a></li>
                <!-- ko with: product -->
                <li data-bind="visible: parseInt(interestpaid)===1&&parseInt(producttype)===2"><a class="nav-link" data-toggle="tab" href="#tab-2" >Interest Rate per term lenght (Fixed deposit)</a></li>
                <!-- /ko -->
                <li ><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-savings_product_fee" >Product Fees</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-1" class="tab-pane active">
                    <div class="panel-body">
                    <div class="panel-title pull-right">
                    <?php if(in_array('3', $deposit_product_privilege)){ ?>
                        <a href="#add_deposit_product-modal" data-bind="click: initialize_edit" data-toggle="modal"  class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Edit</a>
                    <?php }?>
                            <?php
                            $modalTitle = "Edit Savings Product Info";
                            $saveButton = "Update";
                            $this->view('setting/savings/deposit_product/add_deposit_product');
                            ?>
                        </div>
                        <table class="table table-user-information  table-stripped  m-t-md">
                            <tbody data-bind="with: product">
                                <tr>
                                    <td><strong>Product Name</strong></td>
                                    <td><a data-bind="text: (productname)?productname:'None'" ></a></td>
                                    <td><strong>Product Type</strong></td>
                                    <td data-bind="text: (producttype)?typeName :'None'"></td>
                                    <td><strong>Available To</strong></td>
                                    <td data-bind="text: name_av"> </td>
                                </tr>
                                <tr>
                                    
                                    <td><strong>Description</strong></td>
                                    <td colspan="3" data-bind="text: (description)?description:'None'"></td>
                                    <td><strong>Auto Payment?</strong></td>
                                    <td  data-bind="text:(auto_payment==='1')?'Yes':'No'"> </td>
                                   
                                </tr>   
                                <tr>
                                    <td><strong>Minimum Deposit</strong></td>
                                    <td data-bind="text: (mindepositamount)?curr_format(mindepositamount):'None'"></td> 
                                    <td><strong>Maximum Withdraw</strong></td>
                                    <td data-bind="text: (maxwithdrawalamount)?(parseInt(withdraw_cal_method_id) === parseInt(1))?(maxwithdrawalamount*1)+'%':curr_format(maxwithdrawalamount):'None'" ></td>
                                    <td><strong>Minimum Balance</strong></td>
                                    <td data-bind="text: (min_balance)?(parseInt(bal_cal_method_id) === parseInt(1))?(min_balance*1)+'%':curr_format(min_balance):'None'" ></td>
                                </tr>  
                                <tr>
                                    <td data-bind="visible: parseInt(producttype) !== parseInt(3)"><strong>Savings (Payable) Account</strong></td>
                                    
                                    <td data-bind="visible: parseInt(producttype) === parseInt(3)"><strong>Share (Capital) Account</strong></td>
                                    <td data-bind="text:(account_name_main)?account_name_main:'None'" ></td> 

                                    <td><strong>Mandatory Saving?</strong></td>
                                    <td data-bind="text:(mandatory_saving==='1')?'Yes':'No'" ></td> 

                                    <td data-bind="visible: parseInt(mandatory_saving) === parseInt(1)" ><strong>Saving Every</strong></td>
                                    <td data-bind="visible: parseInt(mandatory_saving) === parseInt(1),text: saving_frequency + $root.repayment_made_every(saving_made_every)" ></td>                                     
                                </tr>  
                                <tr data-bind="visible: parseInt(mandatory_saving) === parseInt(1)">
                                    <td><strong>Minimum saving amount</strong></td>
                                    <td data-bind="text: min_saving_amount"></td>
                                    <td><strong>Reminder settings</strong></td>
                                    <td colspan="3" data-bind="text:reminder_frequency +'day(s) ' + $root.reminder_made_every(reminder_made_every) "></td>
                                </tr>  
                                <tr>
                                    <td><strong>Interest Paid?</strong></td>
                                    <td data-bind="text:(interestpaid==='1')?'Yes':'No'" ></td> 
                                    
                                </tr>
                                <tr data-bind="visible: parseInt(producttype)===2">
                                <td><strong>Min Interest Rate Range (%)</strong></td>
                                <td data-bind="text:(mininterestrate) ? mininterestrate:''"></td>
                                <td><strong>Max Interest Rate Range (%)</strong></td>
                                <td  colspan="3" data-bind="text:(maxinterestrate) ? maxinterestrate:''"></td>
                                </tr>
                                <tr>
                                    <td><strong>Penalty?</strong></td>
                                    <td data-bind="text:(penalty==='1')?'Yes':'No'" ></td>
                                    <td><strong>Penalty Amount</strong></td> 
                                    <td data-bind="text: penalty_amount" ></td>
                                    <td><strong>Penalty Calculated as</strong></td> 
                                    <td data-bind="text:(penalty_calculated_as==='1')?'Percentage':'Fixed Amount'" ></td>
                                </tr>
                                               
                                <tr>
                                    <td colspan="6">
                                       <!--  <fieldset class="col-lg-12">     
                                            <legend>Opening Balance</legend>

                                            <div class="form-group row">  
                                                <label class="col-lg-2 col-form-label"><strong>Default </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (defaultopeningbal)? 	curr_format(defaultopeningbal):'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Minimum </strong> </label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (minopeningbal)? 	curr_format(minopeningbal):'None'"></div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Maximum </strong></label>
                                                <div class="col-lg-2">
                                                    <div data-bind="text: (maxopeningbal)? 	curr_format(maxopeningbal):'None'"></div>
                                                </div>
                                            </div>
                                        </fieldset> -->
                                        <fieldset data-bind="visible: parseInt(interestpaid) === parseInt(1)" class="col-lg-12" >     
                                            <legend>Interest Rate Settings</legend>
                                           <div class="form-group row">
                                            <label  class="col-lg-2" ><strong>Days in Year</strong></label>
                                            <div class="col-lg-2" data-bind="text: (name_diy)?name_diy:'None'"></div> 
                                       
                                            <label class="col-lg-2" ><strong>Interested Paid A/C</strong></label>
                                            <div class="col-lg-2"  data-bind="text: (account_name_paid)?account_name_paid:'None'"></div> 
                                            <label class="col-lg-2" ><strong>Interest Earned Payable A/C</strong></label>
                                            <div class="col-lg-2"  data-bind="text: (account_name_earned)?account_name_earned:'None'" ></div>
                                        </div>   

                                            <div class="form-group row" > 
                                             <label  class="col-lg-2 col-form-label"><strong>Calculation Method</strong></label>
                                                <div  class="col-lg-2">
                                                    <div data-bind="text: (interest_method)?  interest_method:'None'"></div>
                                                </div> 
                                                <label class="col-lg-3 col-form-label" data-bind="visible: parseInt(producttype)!==2"><strong>Interest Rate per annum </strong> </label>
                                                <div   data-bind="visible: parseInt(producttype)!==2" class="col-lg-1">
                                                    <span class="badge badge-success" data-bind="text: (defaultinterestrate)?  defaultinterestrate + ' %':'None'"></span>
                                                </div>

                                                <label class="col-lg-3 col-form-label" data-bind="visible: parseInt(producttype)===2"><strong>Minimum Term Length </strong> </label>
                                                <div data-bind="visible: parseInt(producttype)===2" class="col-lg-1">
                                                    <div data-bind="text: (mintermlength)? 	mintermlength +' Months':'None'"></div>
                                                </div>
                                                <label data-bind="visible: parseInt(producttype)===2" class="col-lg-3 col-form-label"><strong>Maximum Term Length</strong></label>
                                                <div data-bind="visible: parseInt(producttype)===2" class="col-lg-1">
                                                    <div data-bind="text: (maxtermlength)? 	maxtermlength +' Months':'None'"></div>
                                                </div>
                                               
                                                <label data-bind="visible: parseInt(producttype)===2" class="col-lg-3 col-form-label"><strong>Account Balance for Interest Calculation</strong></label>
                                                <div  class="col-lg-3">
                                                    <div data-bind="text: (account_balance_for_interest_name)?  account_balance_for_interest_name:'None'"></div>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div role="tabpanel" id="tab-2" class="tab-pane">
                    <div class="panel-body">
                    <div class="panel-title pull-right">
                    <?php if(in_array('3', $deposit_product_privilege)){ ?>
                        <a href="#add_interest-modal" data-bind="click: initialize_edit" data-toggle="modal"  class="btn btn-default btn-sm">
                            <i class="fa fa-pencil"></i> Edit Settings</a>
                    <?php } ?>
                            <?php
                            $saveButton = "Update";
                            $this->view('setting/savings/deposit_product/interest_fees/add_interest_fees');
                            ?>
                        </div>
                        <br>
                        <table class="table table-user-information  table-stripped  m-t-md">
                            <tbody data-bind="with: product">
                                <tr>
                                    <td colspan="9">
                                        <fieldset class="col-lg-12">     
                                            <legend>Interest Rate per Term Lenght</legend>
                                            <div class="form-group row" data-bind='foreach:ranges'>  
                                                <label class="col-lg-2 col-form-label"><strong>From </strong> </label>
                                                <div class="col-lg-2">
                                                    <div ><span class="badge badge-primary" data-bind="text:min_range"></span> Months</div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>To </strong> </label>
                                                <div class="col-lg-2">
                                                    <div ><span class="badge badge-primary" data-bind="text:max_range"></span> Months</div>
                                                </div>
                                                <label class="col-lg-2 col-form-label"><strong>Rate (%) per annum </strong></label>
                                                <div class="col-lg-2">
                                                    <div  class="badge badge-success"data-bind="text: (range_amount)?range_amount+' %':'None'"></div>
                                                </div>
                                               
                                            </div> 
                                        </fieldset>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
				<div role="tabpanel" id="tab-savings_product_fee" class="tab-pane">
					<div class="panel-body">
                        <p class="">
                            <a class="btn btn-primary btn-sm pull-right" data-toggle="modal" href="#add_savings_product_fee-modal">
                            <i class="fa fa-plus-circle"></i> Add saving product fee </a>
                        </p>
                        <br>
                        <div class="col-lg-12">
                        <div class="table-responsive">
						    <table class="table  table-bordered table-hover" id="tblSavings_product_fee" width="100%" >
                                <thead>
                                    <tr>
                                        <th>Fee</th>
                                        <th>Fees income A/C</th>
                                        <th>Fees income Receivable A/C</th>
                                        <th>Status</th>  
                                        <th>Action</th>  
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
							</table>
							</div>
						</div>
					</div>
				</div>
            </div>
        </div>
    </div>
</div>
 <?php $this->load->view('setting/savings/savings_product_fee/add_savings_product_fee-modal'); ?>
 <script>
    var dTable = {};
    var reminder_data = ['Once before & after','Once after','Daily before & after','Daily after'];
    var savePdtDetailModel = {};
    var TableManageButtons = {};
     var RangeFee = function () {
            var self = this;
            self.calculatedas_id =ko.observable();
                self.max_range = "";
                self.min_range = "";
                self.range_amount = "";
                self.id = "";
        };
$(document).ready(function() {
    $('form#formDepositProduct').validate({submitHandler: saveData2});  
    $('form#formDepositProductInterest').validator().on('submit', saveData);
    $('form#formSavings_product_fee').validator().on('submit', saveData);
    var SavePdtDetailModel = function() {
        var self = this;
        self.product = ko.observable(<?php echo json_encode($product);?>);
        self.deposit_producttype = ko.observable(<?php echo json_encode($deposit_product_type); ?>);
        self.producttype = ko.observable();
        self.savingspdtfeesOption = ko.observable(<?php echo json_encode($savingspdtfees); ?>);
        self.savingspdtfees = ko.observable();
        self.schedule_start_date = ko.observable();
        self.mandatory_saving = ko.observable(<?php echo $product['mandatory_saving']; ?>);
        self.penalty = ko.observable(<?php echo $product['penalty']; ?>);
        self.penalty_calculated_as = ko.observable(<?php echo $product['penalty_calculated_as']; ?>);
        self.penalty_amount  = ko.observable(<?php echo $product['penalty_amount']; ?>);

        self.interestpaid = ko.observable(<?php echo $product['interestpaid']; ?>);

        self.repayment_made_every = function(repayment_key) {
            return periods[parseInt(repayment_key)-parseInt(1)];
        }
        self.reminder_made_every = function(reminder_key) {
            return reminder_data[parseInt(reminder_key)-parseInt(1)];
        }
        self.amountCalOptions = ko.observableArray(<?php echo json_encode($amountcalculatedas); ?>);
        self.Amountcal = ko.observable();
        self.initialize_edit = function(){
            edit_data(self.product(),"formDepositProduct");
            set_selects(self.product(),"formDepositProductInterest");

        }
        self.display_table = function (data, click_event) {
            TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };

        self.interest_rate_ranges=ko.observableArray([new RangeFee()]);

        self.addRangeRate = function () {
            self.interest_rate_ranges.push(new RangeFee());
            };
        
        self.removeRangeRate = function (calculatedas_id) {
            self.interest_rate_ranges.remove(calculatedas_id);
        };

       self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
        self.formatAccount2 = function (account) {
            return account.account_code + " " + account.account_name;
        };

       self.select2accounts = function (sub_category_id) {
                //its possible to send multiple subcategories as the parameter
            var filtered_accounts = ko.utils.arrayFilter(self.accounts_list(), function (account) {
                return Array.isArray(sub_category_id)?(check_in_array(account.sub_category_id,sub_category_id)):(account.sub_category_id == sub_category_id);
            });
            return filtered_accounts;
        };


        };
    var handleDataTableButtons = function (tabClicked) {
        <?php $this->load->view('setting/savings/savings_product_fee/savings_product_fee_js'); ?>
     };
		TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

     TableManageButtons.init("tab-transaction"); 


    savePdtDetailModel = new SavePdtDetailModel();
    ko.applyBindings(savePdtDetailModel);
    });
function reload_data(form_id, response){
   switch(form_id){
       case "formDepositProduct":
           savePdtDetailModel.product(response.product);
           break;
       case "formDepositProductInterest":
           savePdtDetailModel.product(response.product);
           break;
       default:
           //nothing really to do here
           break;
   }
}

 function get_range_rates(data) {
    console.log(data);
        savePdtDetailModel.interest_rate_ranges([]);
            ko.utils.arrayForEach(data, function (range_value) {
                var range_rates = new RangeFee();
                range_rates.min_range = range_value.min_range;
                range_rates.max_range = range_value.max_range;
                range_rates.range_amount = range_value.range_amount;
                range_rates.id = range_value.id;
                //let's get the particular account obj from the list of the accounts
                savePdtDetailModel.interest_rate_ranges.push(range_rates);
            });
}

function set_selects(data, formId) {
        switch (formId) {
            case 'formDepositProductInterest':
               get_range_rates(data.ranges);
                break;
        }
        edit_data(data, formId);
    }

</script>
