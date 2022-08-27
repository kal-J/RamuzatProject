<style type="text/css">
    h3{
        font-weight: bold;
        color: #3c8dbc;
        font-size: 22px;
    }
</style>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-content">
                <div class="tabs-container">
                    <ul class="nav nav-tabs" role="tablist">
                        <li><a class="nav-link active firsttab" data-toggle="tab" href="#tab-loan_details">Loan Details</a></li>
                       
                        <!-- <li class="dropdown dropdown-active">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-modx"></i>Security </a>
                            <ul class="nav-link dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-guarantor">Guarantor</a></li>
                                <li><a class="nav-link" data-toggle="tab" role="tab" href="#tab-collateral">Collateral</a></li>
                            </ul>
                        </li> -->
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-money"></i> Security </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-guarantor">Guarantor</a></li>
                                <li><a class="nav-link" data-toggle="tab" role="tab" data-bind="click: display_table" href="#tab-collateral">Collateral</a></li>
                            </ul>
                        </li>
                        
                        <!-- <li><a class="nav-link" data-toggle="tab" href="#tab-loan_fee" data-bind="click: display_table">Fees</a></li>
                        <li><a class="nav-link" data-toggle="tab" href="#tab-loan_attached_saving_acc" data-bind="click: display_table"> Attached Accounts</a></li> -->
                        <li data-bind="visible: (parseInt($root.loan_detail().state_id) >=7)"><a class="nav-link"  data-bind="click: display_table" data-toggle="tab" href="#tab-loan_installment_payment"><i class="fa fa-money"></i>Transactions</a></li>
                        <li class="dropdown">
                            <a class="dropdown-toggle" data-toggle="dropdown" href="#"><i class="fa fa-modx"></i> More </a>
                            <ul class="dropdown-menu">
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_docs"><i class="fa fa-file"></i>  Loan Docs</a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-repayment_schedule"><i class="fa fa-credit-card"></i> Loan Schedule</a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-monthly_expense"><i class="fa fa-modx"></i>Monthly-Expenses</a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-monthly_income"><i class="fa fa-modx"></i>Monthly-Income</a></li>
                                <li><a class="nav-link" data-bind="click: display_table" data-toggle="tab" href="#tab-loan_history"><i class="fa fa-file"></i> Loan History</a></li>
                            </ul>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div role="tabpanel" id="tab-loan_details" class="tab-pane active">
                            <div class="panel-body">
                                <div class="pull-left add-record-btn">
                                    <div class="panel-title" data-bind="with: loan_detail">
                                        <h3 data-bind="text: 'Ref#:  '+loan_no">
                                        </h3>
                                    </div>
                                </div>
                                <div style="padding-left: 12em" class="pull-left add-record-btn">
                                    <h3 class="btn btn-success btn-sm " data-bind="text: ($root.loan_detail().state_name)?((!($root.loan_detail().action_date=='0000-00-00'))?$root.loan_detail().state_name+' on '+moment($root.loan_detail().action_date,'YYYY-MM-DD').format('DD-MMM-YYYY'):$root.loan_detail().state_name):''">
                                    </h3>
                                </div>
                                 
                                   
                                <table class="table table-user-information  table-stripped  m-t-md">
                                    <tbody data-bind="with: loan_detail" >
                                        <tr>
                                            
                                            <td><strong>Credit officer</strong></td>
                                            <td colspan="10"><span data-bind="text: credit_officer_name"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Product name</strong></td>
                                            <td colspan="2"><span data-bind="text: product_name"></span></td>
                                            <td><strong>Loan Security</strong></td>
                                            <td colspan="3"><span data-bind="text: (min_collateral)?(min_collateral*1)+'%':''"></span></td>
                                            <td><strong>Requested amount</strong></td>
                                            <td colspan="5"><span data-bind="text: 'UGX '+curr_format(requested_amount*1)"></span></td>
                                        </tr>
                                        <tr>

                                            <td><strong>Application date</strong></td>
                                            <td colspan="2"><span data-bind="text: (!(application_date=='0000-00-00'))?moment(application_date,'YYYY-MM-DD').format('DD-MMM-YYYY'):'No Date'"></span></td>
                                           
                                            <td><strong>Interest Calculated On</strong></td>
                                            <td colspan="5"><span data-bind="text: (type_name)?type_name:''"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Link to deposit account</strong></td>
                                            <td colspan="2"><span data-bind="text: ( link_to_deposit_account ) = 1 ? 'Yes' : 'No'"></span></td>
                                            <td><strong>offset period</strong></td>
                                            <td colspan="3"><span data-bind="text: (offset_period)?((offset_every)?offset_period+' '+offset_every:''):''"></span></td>
                                            <td><strong>Repayment frequency</strong></td>
                                            <td colspan="5"><span data-bind="text: (repayment_frequency)?((made_every_name)?repayment_frequency+' '+made_every_name:''):''"></span></td>
                                        </tr >
                                        <tr>
                                            <td><strong>Installments</strong></td>
                                            <td colspan="2"><span data-bind="text: installments"></span></td>
                                            <td><strong>Penalty Tolerance(Grace) Period</strong></td>
                                            <td colspan="3"><span data-bind="text: grace_period+' Day(s)'"></span></td>
                                            <td><strong>Penalty charged</strong></td>
                                            <td colspan="5"><span data-bind="text: (penalty_rate_charged_per==1)?'Daily':((penalty_rate_charged_per==2)?'Weekly':((penalty_rate_charged_per==3)?'Monthly':'None'))"></span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Penalty rate</strong></td>
                                            <td colspan="2"><span data-bind="text: (penalty_rate*1) +  '%' "></span></td>
                                            <td><strong>Penalty calculation method</strong></td>
                                            <td colspan="8"><span data-bind="text: method_description"></span></td>
                                        </tr>
                                        <!-- ko if: parseInt(preferred_payment_id)==1 -->
                                        <tr>
                                            <td><strong>Payment Option</strong></td>
                                            <td colspan="2"><span data-bind="text: payment_mode"></span></td>
                                            <td><strong>Loan Purpose</strong></td>
                                            <td colspan="8"><span data-bind="text: (loan_purpose)?loan_purpose:'None'"></span></td>
                                        </tr>
                                        <!-- /ko -->
                                        <!-- ko if: parseInt(preferred_payment_id)==2 -->                                        
                                        <tr>
                                            <td><strong>Payment Option</strong></td>
                                            <td colspan="2"><span data-bind="text: payment_mode"></span></td>
                                            <td><strong>A/C Number</strong></td>
                                            <td colspan="3"><span data-bind="text: ac_number"></span></td>
                                            <td><strong>A/C Name</strong></td>
                                            <td colspan="5"><span data-bind="text: (ac_name)?ac_name:'None'"></span></td>
                                        </tr>                                        
                                        <tr>
                                            <td><strong>Bank Branch</strong></td>
                                            <td colspan="2"><span data-bind="text: bank_branch"></span></td>
                                            <td><strong>Bank Name</strong></td>
                                            <td colspan="3"><span data-bind="text: bank_name"></span></td>
                                            <td><strong>Loan Purpose</strong></td>
                                            <td colspan="5"><span data-bind="text: (loan_purpose)?loan_purpose:'None'"></span></td>
                                        </tr>
                                        <!-- /ko -->
                                        <!-- ko if: parseInt(preferred_payment_id)==4 -->                                        
                                        <tr>
                                            <td><strong>Payment Option</strong></td>
                                            <td colspan="2"><span data-bind="text: payment_mode"></span></td>
                                            <td><strong>Phone Number</strong></td>
                                            <td colspan="3"><span data-bind="text: phone_number"></span></td>
                                            <td><strong>Loan Purpose</strong></td>
                                            <td colspan="5"><span data-bind="text: (loan_purpose)?loan_purpose:'None'"></span></td>
                                        </tr>
                                        <!-- /ko -->
                                        <!-- ko if: parseInt(topup_application)==1 -->
                                        <tr>
                                            <td colspan="3"></td>
                                            <td colspan="5">                                            
                                            <div class="col-lg-12 form-group">
                                              <h4 data-bind="visible: ((typeof member_name !=='undefined') && (linked_loan_id !=null)) "><a data-bind="attr:{href: '<?php echo site_url('u/loans/view'); ?>'+'/'+linked_loan_id}" target="_blank" title='View this Loan details'>To view the parent loan details click here</a></h4>
                                              <h4 data-bind="visible: ((typeof group_name !=='undefined') && (group_loan_id !=null) ) "><a data-bind="attr:{href: '<?php echo site_url('u/loans/view'); ?>'+'/'+group_loan_id+'/1'}" target="_blank" title='View this Loan details'>To view parent loan details click here</a></h4>
                                            </div></td>
                                            <td colspan="3"></td>
                                        </tr>
                                        <!-- /ko -->

                                    </tbody>
                                </table>
                            </div>
                        </div> <!-- end of loan_details -->
                        <?php $this->view('client/loans/security/guarantor/tab_view'); ?>
                        <?php $this->view('client/loans/security/collateral/tab_view'); ?>
                        <?php $this->view('client/loans/fees/tab_view'); ?> 
                        <?php 
                            $this->view('client/loans/loan_attached_saving_accounts/tab_view'); ?>                      
                        <?php $this->view('client/loans/repayment_schedule/tab_view'); ?>
                        <?php $this->view('client/loans/loan_docs/tab_view'); ?>
                        <?php $this->view('client/loans/history/tab_view'); ?>
                        <?php $this->view('client/loans/income_and_expense/income/tab_view'); ?>
                        <?php $this->view('client/loans/income_and_expense/expense/tab_view'); ?>
                        <?php $this->view('client/loans/loan_transactions/tab_view'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    var dTable = {};
    var loanDetailModel = {};
    var TableManageButtons = {};
    $(document).ready(function () {
        var periods = ['days', 'weeks', 'months'];
        var loan_product_length = '';
        var LoanFee = function () {
            var self = this;
            self.selected_fee = ko.observable();
        };

        var SavingsAccount = function () {
            var self = this;
            self.selected_fee = ko.observable();
        };
        
      
        var LoanDetailModel = function () {
            var self = this;
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.loan_detail = ko.observable(<?php echo json_encode($loan_detail); ?>);
            self.loan_details = ko.observable();
            self.group_loan_details = ko.observable(<?php if (isset($group_loan_details)) {
                echo json_encode($group_loan_details);
             } ?>);
            self.selected_product = ko.observable();
            //rescheduling a loan
            self.schedule_detail = ko.observable();
            self.interest_rate = ko.observable();
            self.top_up_amount = ko.observable();
            self.current_installment = ko.observable();
            self.repayment_frequency = ko.observable();
            self.installments = ko.observable();
            self.new_date = ko.observable('<?php echo date('d-m-Y'); ?>');

            //paying for the loan
            self.payment_date = ko.observable('<?php echo date('d-m-Y');?>');
            self.payment_details = ko.observable();
            self.penalty_amount = ko.observable();

            self.repayment_made_every_detail = ko.observableArray(<?php echo json_encode($repayment_made_every); ?>);
            self.repayment_made_every = ko.observable();

            self.payment_summation = ko.observable();
            self.pay_off_data = ko.observable();
            self.approval_data = ko.observable();
            self.payment_schedule = ko.observableArray();
            self.product_names = ko.observable(<?php echo json_encode($loanProducts); ?>);
            self.product_name = ko.observable();
            
            self.guarantors = ko.observable(<?php echo json_encode($guarantors); ?>);
            self.guarantor = ko.observable();

            self.income_items = ko.observable(<?php echo json_encode($income_items); ?>);
            self.income_item = ko.observable();

            self.expense_items = ko.observable(<?php echo json_encode($expense_items); ?>);
            self.expense_item = ko.observable();

            self.savings_accs = ko.observable(<?php echo json_encode($savings_accs); ?>);
            self.savings_acc = ko.observable();

            self.guarantor_amount = ko.observable(0);
            self.loan_type = ko.observable('1');
            self.collateral_amount = ko.observable(0);
            self.available_guarantors = ko.observableArray();
            self.available_loan_fees = ko.observableArray(<?php echo (!empty($available_loan_fees) ? json_encode($available_loan_fees) : '') ?>);

            self.approval_date = ko.observable('<?php echo date('d-m-Y'); ?>');
            self.suggested_disbursement_date = ko.observable('<?php echo date('d-m-Y'); ?>');

            self.member_names = ko.observable(<?php echo json_encode($members); ?>);
            self.member_name = ko.observable();


            self.accounts_list = ko.observableArray(<?php echo json_encode($account_list); ?>);
            
            //paying for the loan
            self.installment_payment_date = ko.observable('<?php echo date('d-m-Y'); ?>');
            self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
            self.tchannels = ko.observable();
            self.payment_data=ko.observable();
            self.penalty_amount = ko.observable();            
            self.loan_ref_no = ko.observable();
            self.installment_number = ko.observable();   
             self.principal_amount = ko.observable(0);  
            self.interest_amount = ko.observable(0); 
            self.received_penalty_amount = ko.observable(0); 

            //for payment purposes
            self.active_loans = ko.observableArray(<?php echo json_encode($active_loans)?>);
            self.active_loan = ko.observable();

            self.loan_installments = ko.observableArray(<?php echo json_encode($installments)?>);
            self.loan_installment = ko.observable(); 

            //filtering the loan installments for a loan
            self.filtered_active_loan_installment = ko.computed(function () {
                var active_loan_installment;
                if (self.active_loan()) {
                    active_loan_installment = ko.utils.arrayFilter(self.loan_installments(), function (data) {
                        return parseInt(data.client_loan_id) == parseInt(self.active_loan().id);
                    });
                }
                return active_loan_installment;
            });


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
            self.loan_installment.subscribe(function (data) {
               var dataobj = {};
                if(typeof data != 'undefined' && typeof self.active_loan() !== 'undefined'){
                    dataobj['loan_ref_no'] = typeof self.active_loan().loan_no !== 'undefined' ? self.active_loan().loan_no :'';
                    dataobj['installment_number'] = typeof data.installment_number !== 'undefined' ? data.installment_number :'';
                    dataobj['call_type']=$('#call_type').val();
                    get_payment_detail(dataobj);
                }
            });
            self.installment_payment_date.subscribe(function (data) {
                if (typeof data !== 'undefined') {
                    get_new_penalty(data);
                }
            });

            //End of payment variables

           
            self.loan_product_length = ko.computed( function(){
            if(typeof self.product_name() != 'undefined'){
                var loan_product_length=(self.product_name().max_repayment_installments)*(self.product_name().repayment_frequency);
                var loan_product_period= periods[self.product_name().repayment_made_every-1];

                return loan_product_length+' '+loan_product_period;
            }else if(typeof self.selected_product() != 'undefined' && self.selected_product() != null){
                var loan_product_length=(self.selected_product().max_repayment_installments)*(self.selected_product().repayment_frequency);
                var loan_product_period= periods[self.selected_product().repayment_made_every-1];
                return loan_product_length+' '+loan_product_period;
            }else{
                return false;
            }
            }, this);

//
            self.product_date = ko.computed( function(){

                if (typeof self.product_name() != 'undefined') {
                    var loan_product_length = (self.product_name().max_repayment_installments) * (self.product_name().repayment_frequency);
                    var loan_product_period = periods[self.product_name().repayment_made_every - 1];

                return moment().add(loan_product_length,loan_product_period);
            }else if(typeof self.selected_product() != 'undefined' && self.selected_product() != null){
                var loan_product_length=(self.selected_product().max_repayment_installments)*(self.selected_product().repayment_frequency);
                var loan_product_period= periods[self.selected_product().repayment_made_every-1];
                 return moment().add(loan_product_length,loan_product_period);
            }else{
                return false;
            }
            }, this);
            self.available_loan_saving_accounts = ko.observableArray(<?php echo (!empty($savings_accs) ? json_encode($savings_accs) : '') ?>);
            self.attached_loan_saving_accounts = ko.observableArray([new SavingsAccount()]);

            self.applied_loan_fee = ko.observableArray([new LoanFee()]);
            self.addLoanFee = function () {
                self.applied_loan_fee.push(new LoanFee());
            };
            self.removeLoanFee = function (selected_member) {
                self.applied_loan_fee.remove(selected_member);
            };

            self.addSavingAcc = function () {
                self.attached_loan_saving_accounts.push(new SavingsAccount());
            };
            self.removeSavingAcc = function (selected_member) {
                self.attached_loan_saving_accounts.remove(selected_member);
            };
            self.payment_date.subscribe(function (data) {
                if (typeof data !== 'undefined') {
                    get_new_penalty(data);
                }
            });

            self.action_date = ko.observable('<?php echo date('d-m-Y'); ?>');
            self.initialize_edit = function () {
                edit_data(self.loan_detail(), "formClient_loan");
            };

            // self.get_payment_data = function () {
            //    var dataobj = {};
            //     dataobj['loan_ref_no'] = typeof self.loan_ref_no() !== 'undefined' ? self.loan_ref_no : '';
            //     dataobj['installment_number'] = typeof self.installment_number() !== 'undefined' ? self.installment_number : '';
            //     dataobj['call_type']=$('#call_type').val();
            //     get_payment_detail(dataobj);
            // };
            self.action_date.subscribe(function (new_date) {
                var dataobj = {action_date: new_date};
                if (typeof new_date !== 'undefined') {
                    get_new_schedule(dataobj, 1);
                }
            });

            self.new_date.subscribe(function (data) {
                var dataobj = {new_repayment_date: data};
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });
            self.interest_rate.subscribe(function (data) {
                var dataobj = {interest_rate: data};
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });

            self.repayment_frequency.subscribe(function (data) {
                var dataobj = {repayment_frequency: data};
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });

            self.repayment_made_every.subscribe(function (data) {
                if (typeof data !== 'undefined') {
                    var dataobj = {repayment_made_every: typeof data.id === 'undefined' ? data : data.id};
                    get_new_schedule(dataobj, 2);
                }
            });

            self.installments.subscribe(function (data) {
                var dataobj = {installments: data};
                if (typeof data !== 'undefined') {
                    get_new_schedule(dataobj, 2);
                }
            });

             //for generating the disbursement sheet 
            <?php $this->load->view('client_loan/loan_steps_files/application_knockoutjs.php');?>
            self.disburse = function () {

                self.loan_details(self.loan_detail());
                var data_set={};
                var data1={};
                var controller = "Client_loan";
                <?php if(($org['loan_app_stage']==0)||($org['loan_app_stage']==1)){ ?>
                    data_set=self.loan_detail();
                    var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement";
                <?php }elseif($org['loan_app_stage']==2){ ?>
                    var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement1";
                    data1['offset_period1']=self.loan_detail().offset_period;
                    data1['offset_made_every1']=self.loan_detail().offset_made_every;
                    data1['amount1']=self.loan_detail().requested_amount;
                    data1['product_type_id1']=self.loan_detail().product_type_id;
                    data1['interest_rate1']=self.loan_detail().interest_rate;
                    data1['installments1']=self.loan_detail().installments;
                    data1['repayment_made_every1']=self.loan_detail().repayment_made_every;
                    data1['repayment_frequency1']=self.loan_detail().repayment_frequency;
                    data1['loan_product_id1']=self.loan_detail().loan_product_id;
                    data_set=data1;
                <?php } ?>
                $.ajax({
                    url: url,
                    data: data_set,
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        self.action_date(null);
                        self.action_date('<?php echo date('d-m-Y'); ?>');
                        self.payment_schedule(null);
                        self.payment_schedule(response.payment_schedule);
                        self.payment_summation(response.payment_summation);

                    }
                });
            };

            self.re_finance = function () {
                self.loan_details(self.loan_detail());
                var controller = "Client_loan";
                var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement";
                $.ajax({
                    url: url,
                    data: self.loan_detail(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        self.action_date(null);
                        self.action_date('<?php echo date('d-m-Y'); ?>');
                        self.payment_schedule(null);
                        self.payment_schedule(response.payment_schedule);
                        self.payment_summation(response.payment_summation);

                    }
                });
            };

            self.approve_loan = function () {
                self.loan_details(self.loan_detail());
                var controller = "Client_loan";
                var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/get_approval_data";
                $.ajax({
                    url: url,
                    data: self.loan_detail(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        self.approval_data(null);
                        self.selected_product(null);
                        self.selected_product(response.selected_product);
                        self.approval_data(response.approval_data);

                    }
                });
            };
            self.action_on_loan = function () {
                self.loan_details(self.loan_detail());
            };

            self.pay_off = function () {
                self.loan_details(self.loan_detail());
                var controller = "Repayment_schedule";
                var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/get_pay_off_data";
                $.ajax({
                    url: url,
                    data: self.loan_detail(),
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        self.pay_off_data(null);
                        self.pay_off_data(response.pay_off_data);

                    }
                });
            };
        };
        loanDetailModel = new LoanDetailModel();
        ko.applyBindings(loanDetailModel);
        //loan period validation
    $.validator.addMethod("mustbelessthanProductMaxLoanPeriod", function(value, element) {
                $(element).attr('data-rule-mustbelessthanProductMaxLoanPeriod');
                var account_length=(parseInt($('#installment').val())*parseInt($('#paid_every').val()));
                var account_period=periods[parseInt($('#period_id').val())-1];

                var account_date= moment().add(account_length,account_period);

                if(typeof loanDetailModel.product_date() != 'undefined'){
                    var period_difference=loanDetailModel.product_date().diff(account_date,'days');

                    if(period_difference >= 0){
                        return true;
                    }else{
                        return false;
                    }

                }else{
                    return false;
                }
            },"This period exceedes the above stated period");

     $.validator.addMethod("mustbelessthantheProductMaxLoanPeriod", function(value, element) {
                $(element).attr('data-rule-mustbelessthantheProductMaxLoanPeriod');
                var account_length=(parseInt($('#approved_installments').val())*parseInt($('#approved_repayment_frequency').val()));
                var account_period=periods[parseInt($('#approved_repayment_made_every').val())-1];

                var account_date= moment().add(account_length,account_period);

                if(typeof loanDetailModel.product_date() != 'undefined'){
                    var period_difference=loanDetailModel.product_date().diff(account_date,'days');

                    if(period_difference >= 0){
                        return true;
                    }else{
                        return false;
                    }

                }else{
                    return false;
                }
            },"This period exceedes the above stated period");
        var handleDataTableButtons = function (tabClicked) {
<?php $this->view('client/loans/security/guarantor/table_js'); ?>
<?php $this->view('client/loans/security/collateral/table_js'); ?>
<?php $this->view('client/loans/repayment_schedule/table_js'); ?>
<?php $this->view('client/loans/loan_docs/table_js'); ?>
<?php $this->view('client/loans/fees/table_js'); ?>
<?php $this->view('client/loans/loan_attached_saving_accounts/table_js'); ?>
<?php $this->view('client/loans/history/table_js'); ?>
<?php $this->view('client/loans/income_and_expense/income/table_js'); ?>
<?php $this->view('client/loans/income_and_expense/expense/table_js'); ?>
<?php $this->view('client/loans/loan_transactions/table_js'); ?>
        };
        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        <?php if (in_array('15', $modules)){?>
         TableManageButtons.init("tab-guarantor");
        <?php } ?>
        TableManageButtons.init("tab-collateral");
    });

    $('table tbody').on('click', 'tr .reschedule_loan', function (e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var tbl = row.parent().parent();
        var tbl_id = $(tbl).attr("id");
        var dt = dTable[tbl_id];
        var data = dt.row(row).data();
        if (typeof (data) === 'undefined') {
            data = dt.row($(row).prev()).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev().prev()).data();
            }
        }
        //clear the the other fields because we are starting the selection afresh
        loanDetailModel.payment_summation(null);
        loanDetailModel.payment_schedule(null);
        loanDetailModel.schedule_detail(null);
        loanDetailModel.current_installment(data.installment_number);
        loanDetailModel.new_date(moment(data.repayment_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        loanDetailModel.installments(loanDetailModel.loan_detail().approved_installments);
        loanDetailModel.repayment_frequency(data.repayment_frequency);
        loanDetailModel.interest_rate(data.interest_rate);
        loanDetailModel.repayment_made_every(data.repayment_made_every);
        loanDetailModel.schedule_detail(data);
    });

    $('table tbody').on('click', 'tr .pay_for_installment', function (e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var tbl = row.parent().parent();
        var tbl_id = $(tbl).attr("id");
        var dt = dTable[tbl_id];
        var data = dt.row(row).data();
        if (typeof (data) === 'undefined') {
            data = dt.row($(row).prev()).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev().prev()).data();
            }
        }
        //clear the the other fields because we are starting the selection afresh
        loanDetailModel.payment_details(null);
        loanDetailModel.payment_details(data);

        var url = "<?php echo site_url("loan_installment_payment/get_penalty_data"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //populate the observables
                loanDetailModel.penalty_amount(response.penalty_data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    });

    $('table tbody').on('click', 'tr .re_finance_loan', function (e) {
        e.preventDefault();
        var row = $(this).closest('tr');
        var tbl = row.parent().parent();
        var tbl_id = $(tbl).attr("id");
        var dt = dTable[tbl_id];
        var data = dt.row(row).data();
        if (typeof (data) === 'undefined') {
            data = dt.row($(row).prev()).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev().prev()).data();
            }
        }
        //clear the the other fields because we are starting the selection afresh
        loanDetailModel.payment_summation(null);
        loanDetailModel.payment_schedule(null);
        loanDetailModel.schedule_detail(null);
        loanDetailModel.new_date(moment(data.repayment_date, 'YYYY-MM-DD').format('DD-MM-YYYY'));
        loanDetailModel.installments(loanDetailModel.loan_detail().installments);

        loanDetailModel.schedule_detail(data);
        loanDetailModel.current_installment(data.installment_number);
    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formClient_loan":
                if (typeof response.client_loan !== 'undefined' ) {
                   loanDetailModel.loan_detail(response.client_loan);
                }
                if (typeof response.group_loan !== 'undefined' ) {
                    window.location = "<?php  echo site_url('group_loan');?>";
                }
                break;
            case "formActive":
                if (typeof response.client_loan != 'undefined') {
                    loanDetailModel.loan_detail(response.client_loan);
                }
                dTable['tblRepayment_schedule'].ajax.reload(null, false);
                break;
         
            case "formReverse":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            case "formReverse_approval":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            case "formApplication_withdraw":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            case "formClient_loan_guarantor":
                if(typeof response.guarantors != 'undefined'){
                    loanDetailModel.guarantors(null);
                    loanDetailModel.guarantors(response.guarantors);
                }
                break;
            case "formLoan_fee_application":
                if (typeof response.available_loan_fees != 'undefined') {
                    loanDetailModel.available_loan_fees(response.available_loan_fees);
                }
                dTable['tblApplied_loan_fee'].ajax.reload(null, false);
                break;
            case "formLoan_detail_saving_accounts":
                if (typeof response.savings_accs != 'undefined') {
                    loanDetailModel.available_loan_saving_accounts(response.savings_accs);
                }
                dTable['tblLoan_attached_saving_accounts'].ajax.reload(null, false);
                break;              
            case "formReschedule_payment":
                dTable['tblRepayment_schedule'].ajax.reload(null, false);
                break;
            case "formPay_off":
                loanDetailModel.loan_detail(response.client_loan);
                dTable['tblLoan_installment_payment'].ajax.reload(null, false);
                break;
            case "formInstallment_payment": 
                loanDetailModel.loan_detail(response.client_loan);           
                TableManageButtons.init("tab-loan_installment_payment");
                dTable['tblLoan_installment_payment'].ajax.reload(null, false);
                break; 
            case "formRe_finance":
                dTable['tblRepayment_schedule'].ajax.reload(null, false);
                break;
            case "formForward_application":
                loanDetailModel.loan_detail(response.client_loan);
                break;
            default:
                break;
        }
    }

     //getting payment schedule for a loan at application stage
     function get_payment_schedule(data) {
        var new_data = {};
            new_data['application_date1'] = typeof data.application_date === 'undefined' ? loanDetailModel.application_date() : data.application_date;
            new_data['action_date1'] = typeof data.action_date === 'undefined' ? loanDetailModel.app_action_date() : data.action_date;

            new_data['loan_product_id1'] = typeof loanDetailModel.product_name() !== 'undefined' ? loanDetailModel.product_name().id:loanDetailModel.loan_details().loan_product_id;
            new_data['product_type_id1'] = typeof loanDetailModel.product_name() !== 'undefined' ?loanDetailModel.product_name().product_type_id:loanDetailModel.loan_details().product_type_id;
            
            new_data['amount1'] = typeof data.amount === 'undefined' ?((typeof loanDetailModel.app_amount() != 'undefined')? loanDetailModel.app_amount(): ( (typeof loanDetailModel.loan_details() !='undefined')?loanDetailModel.loan_details().requested_amount:'' ) ) : data.amount;
            new_data['offset_period1'] = typeof data.offset_period === 'undefined' ?((typeof loanDetailModel.app_offset_period() != 'undefined')?loanDetailModel.app_offset_period(): ( (typeof loanDetailModel.loan_details() !='undefined')?loanDetailModel.loan_details().offset_period:'' ) ): data.offset_period;            
            new_data['offset_made_every1'] = typeof data.offset_made_every === 'undefined' ?((typeof loanDetailModel.app_offset_every() != 'undefined')?loanDetailModel.app_offset_every():( (typeof loanDetailModel.loan_details() !='undefined' )?loanDetailModel.loan_details().offset_made_every:'' )): data.offset_every;            
            new_data['interest_rate1'] = typeof data.interest === 'undefined' ?((typeof loanDetailModel.app_interest() != 'undefined')?loanDetailModel.app_interest(): ( (typeof loanDetailModel.loan_details() !='undefined')?loanDetailModel.loan_details().interest_rate:'')): data.interest;
            new_data['repayment_made_every1'] = typeof data.repayment_made_every === 'undefined' ?((typeof loanDetailModel.app_repayment_made_every() != 'undefined')?loanDetailModel.app_repayment_made_every():( (typeof loanDetailModel.loan_details() !='undefined')?loanDetailModel.loan_details().repayment_made_every:'')): data.repayment_made_every;
            
            new_data['repayment_frequency1'] = typeof data.repayment_frequency === 'undefined' ?((typeof loanDetailModel.app_repayment_frequency() != 'undefined')?loanDetailModel.app_repayment_frequency(): ( (typeof loanDetailModel.loan_details() !='undefined')?loanDetailModel.loan_details().repayment_frequency:'')): data.repayment_frequency;            
           
            new_data['installments1'] = typeof data.installments === 'undefined' ?((typeof loanDetailModel.app_installments() != 'undefined')?loanDetailModel.app_installments():( (typeof loanDetailModel.loan_details() !='undefined')?loanDetailModel.loan_details().installments:'')) : data.installments;

        var url = "<?php echo site_url("client_loan/disbursement1"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //clear the the other fields because we are starting the selection afresh
                loanDetailModel.payment_summation(null);
                loanDetailModel.payment_schedule(null);
                //populate the observables
                loanDetailModel.payment_schedule(response.payment_schedule);
                loanDetailModel.payment_summation(response.payment_summation);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }


    

//getting new schedule
    function get_new_schedule(data, call_type) {
        var new_data = {};
        if (call_type === 1) {
            new_data['action_date'] = typeof data.action_date === 'undefined' ? loanDetailModel.action_date() : data.action_date;
        } else {
            new_data['new_repayment_date'] = typeof data.new_repayment_date === 'undefined' ? loanDetailModel.new_date() : data.new_repayment_date;
            new_data['interest_rate'] = typeof data.interest_rate === 'undefined' ? loanDetailModel.interest_rate() : data.interest_rate;
            new_data['repayment_made_every'] = typeof data.repayment_made_every === 'undefined' ? loanDetailModel.repayment_made_every() : data.repayment_made_every;
            new_data['repayment_frequency'] = typeof data.repayment_frequency === 'undefined' ? loanDetailModel.repayment_frequency() : data.repayment_frequency;
            new_data['installments'] = typeof data.installments === 'undefined' ? loanDetailModel.installments() : data.installments;
            new_data['current_installment'] = typeof data.current_installment === 'undefined' ? loanDetailModel.current_installment() : data.current_installment;
        }
        new_data['id'] = loanDetailModel.loan_detail().id;
        var url = "<?php echo site_url("client_loan/disbursement"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //clear the the other fields because we are starting the selection afresh
                loanDetailModel.payment_summation(null);
                loanDetailModel.payment_schedule(null);
                //populate the observables
                loanDetailModel.payment_schedule(response.payment_schedule);
                loanDetailModel.payment_summation(response.payment_summation);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
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
                loanDetailModel.payment_data(response.payment_data);
                loanDetailModel.penalty_amount(response.penalty_data);
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
        data['client_loan_id'] = loanDetailModel.payment_data().id;
        data['installment_number'] = loanDetailModel.payment_data().installment_number;
        var url = "<?php echo site_url("loan_installment_payment/get_penalty_data"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //populate the observables
                loanDetailModel.penalty_amount(response.penalty_data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
    }
    
    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        //compute the sums of the collateral or guarantors
        if (theData.length > 0) {
            if (theData[0]['item_value']) {//collateral data array
                loanDetailModel.collateral_amount(sumUp(theData, 'item_value'));
            }
            if (theData[0]['amount_locked']) {//guarantor data array
                loanDetailModel.guarantor_amount(sumUp(theData, 'amount_locked' ) );
       }
   }
}
</script>
