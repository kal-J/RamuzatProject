<?php
$start_date = date('d-m-Y', strtotime($fiscal_active['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_active['end_date']));
?>
<div class="row">
    <div class="col-lg-12">
        <div class="tabs-container">
          
            <ul class="nav nav-tabs " role="tablist">
                <li><a class="nav-link active" data-toggle="tab" data-bind="click: display_table" href="#tab-overview"><i class="fa fa-address-book-o"></i>Account overview</a></li>
                <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-transaction"><i class="fa fa-bars"></i>Transaction</a></li>
                <!-- <li><a class="nav-link" data-toggle="tab" data-bind="click: display_table" href="#tab-charges"><i class="fa fa-money"></i>Charges</a></li> -->
                
            </ul>
            <div class="tab-content">
                <div role="tabpanel" id="tab-overview" class="tab-pane active">
                    <div class="panel-body">
                        <div class="panel-title pull-right">
                            <div class="btn-group">
                            <!--ko with: selected_account -->
                            <span class="btn btn-sm " data-bind="text: (client_type==1)?'Individual Account' : 'Group Account', css:$root.switch_client_type_classes"></span>
                            <!--/ko -->
                           
                            </div>
                        </div>
                        <table class="table table-user-information  table-bordered table-stripped  m-t-md">
                            <tbody data-bind="with: selected_account">
                                <tr>
                                    <td><strong>Account No.</strong></td>
                                    <td colspan="5"><a data-bind="text: (account_no)?account_no:'None'" ></a></td>
                                </tr>
                                <tr>
                                    <td><strong>Account Holder</strong></td>
                                    <td colspan="5" data-bind="text: (member_name )?member_name :'None'"></td>
                                </tr>
                                <tr>
                                    <td><strong>Account Type:</strong></td>
                                    <td colspan="3" data-bind="text: productname"></td>
                                    <td><strong>Interest Rate:</strong></td>
                                    <td  data-bind="text: (interest_rate)?(interest_rate)+' %' :0"> </td>
                                </tr>
                               
                                <tr>
                                    <td><strong>Opening balance </strong></td>
                                    <td  data-bind="text: (opening_balance)?curr_format(opening_balance*1):0" colspan="3"></td>
                                    <td><strong>Status </strong></td>
                                    <td  data-bind="text: (state_id)?((state_id==7)?'Active':((state_id==12)?'Locked':((state_id==0)?'Deleted':(
										(state_id==5)?'Pending':((state_id==17)?'Dormant':((state_id==18)?'Deleted':'Undefined')))))):'None'"></td>
                                </tr>
                            </tbody>
                            <!-- for another table-->
                            <tbody data-bind="with: selected_account">
                                                        
                                <tr>
                                <td colspan="6">
                                <div class="pull-left col-lg-4" data-bind="visible: $root.group_members().length"  >  
                                <br> 
                                <br>     
                                <strong><u>Group Members</u></strong>
                                            <div  class="text-sm border-bottom">
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
                                                        <span  class=" input-xs"  data-bind="text:(member_name)?((group_leader==1)?member_name+' (GL)':member_name+' '): 'None'"></span>   
                                                    </span>
                                                    <span class="pull-right">
                                                        <span class=" input-xs"  data-bind="text:(real_bal)?curr_format(real_bal):' -'" ></span>
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
                                            <div  class="text-muted text-sm">
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
                                            <strong >
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
                <?php $this->load->view('client/savings/transaction/transaction_tab.php'); ?>
                <?php //$this->load->view('client/savings/charges/charges_tab.php'); ?>
                <?php $this->load->view('client/savings/shares/shares_tab.php'); ?>
            </div>
        </div>
    </div>
</div>
<script>
    var dTable = {};
    var saveAcctDetailModel = {};
    var TableManageButtons = {};
    var start_date, end_date;
    var displayed_tab = '';
    $(document).ready(function () {
        $(".select2able").select2({
            allowClear: true
        });
        $('form#formSavings_account').validate({submitHandler: saveData2});
        $('form#formChange_state').validate({submitHandler: saveData2});
        $('form#formDeposit').validate({submitHandler: saveData2});
        $('form#formWithdraw').validate({submitHandler: saveData2});
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY"); end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");



        var SaveAcctDetailModel = function () {
            var self = this;
            self.ProductOptions = ko.observable(<?php echo json_encode($products); ?>);
            self.organisationFormats = ko.observable(<?php echo json_encode($organisation_format); ?>);
            self.Product = ko.observable();
            self.action_msg = ko.observable();
            self.account_state = ko.observable();
            self.selected_account = self.accountw= ko.observable(<?php echo json_encode($selected_account); ?>);

            self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
            self.withdraw_fees = ko.observableArray(<?php echo json_encode($withdraw_fees); ?>);
            self.deposit_fees = ko.observableArray(<?php echo json_encode($deposit_fees); ?>);
            self.account_balance = ko.observable(self.selected_account().cash_bal);
            self.group_members = ko.observableArray(<?php echo (isset($group_members)===true)?json_encode($group_members):''; ?>);
            self.fees = ko.observable();
            self.User = ko.observable();
            self.tchannels = ko.observable();
            self.deposit_amount = ko.observable(self.selected_account().mindepositamount);
            self.withdraw_amount = ko.observable(1000);
           
            self.transaction = function () {
                self.selected_account(<?php echo json_encode($selected_account); ?>);
                self.account_state();
            };
          
            self.switch_client_type_classes = ko.pureComputed(function() {
                return  (parseInt(self.selected_account().client_type)==1) ? "btn-primary" : "btn-secondary";
            }, this);
            //After charges on deposit	
            self.totaldepositCharges = ko.computed(function () {
                total = 0;
                ko.utils.arrayForEach(self.deposit_fees(), function (depositfee) {
                    if (depositfee.cal_method_id == 1) {
                        total += (parseFloat(depositfee.amount) * (self.deposit_amount())) / 100;
                    } else {
                        total += parseFloat(depositfee.amount);
                    }
                });
                return total;   //to be reviewed later...
            }, this);

            //After withdraw charges
            self.totalwithdrawCharges = ko.computed(function () {
                total = 0;
                ko.utils.arrayForEach(self.withdraw_fees(), function (withdrawfee) {
                    if (withdrawfee.cal_method_id == 1) {  //1-percentage 2-fixed
                        total += (parseFloat(withdrawfee.amount) * (self.withdraw_amount())) / 100;
                    } else {
                        total += parseFloat(withdrawfee.amount);
                    }
                });
                return total;   //to be reviewed later...
            }, this);

           self.printStatement = function() {
            if (typeof moment(start_date,'X').format('YYYY-MM-DD') !== 'undefined') {
                window.location = "<?php echo site_url('u/savings/AcStatement/').$acc_id."/"; ?>" +  moment(start_date,'X').format('YYYY-MM-DD')+"/"+moment(end_date,'X').format('YYYY-MM-DD');
             }
           }
            
            self.initialize_edit = function () {
                edit_data(self.selected_account(), "formSavings_account");
                //edit_data(self.selected_account(),"formDepositProductInterest");
            }
            self.set_action = function (state_id) {

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

            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
        };

        var handleDataTableButtons = function (tabClicked) {
<?php $this->load->view('client/savings/transaction/transaction_js'); ?>
<?php $this->load->view('client/savings/charges/charges_js'); ?>
<?php $this->load->view('client/savings/shares/shares_js');  ?>
<?php //$this->load->view('client/savings/withdraws/withdraws_js.php');   ?>
        };
        TableManageButtons = function () {
           "use strict";
           return {
               init: function (tblClicked) {
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

});    //end of document.ready
 function handleDateRangePicker(startDate, endDate) {
        if (typeof displayed_tab !== 'undefined') {
          
        }
        start_date = startDate;
        end_date = endDate;
        TableManageButtons.init(displayed_tab);
        //dashModel.updateData();
    }
function reload_data(form_id, response){
   switch(form_id){
         case "formSavings_account":
         case "formChange_state":
         case "formWithdraw":
         case "formDeposit":
         if (typeof response.insert_id !== 'undefined') {
            window.location = "<?php echo site_url('transaction/print_receipt/'); ?>" + response.insert_id;
        }
          dTable['tblTransaction'].ajax.reload(null, true);
           saveAcctDetailModel.selected_account(response.accounts);
           //saveAcctDetailModel.group_members(response.group_members);
           break;
       default:
           //nothing really to do here
           break;
   }
}

</script>
