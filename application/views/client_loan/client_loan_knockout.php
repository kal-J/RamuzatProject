    var allMoneyInputs = [];

     $("#formTopup_loan").steps({
        labels: {
                finish: "Submit",
                cancel: "Cancel"
                },
        bodyTag: "section",
        onInit: function (event, current) {
            //alert(current);
        },
        onStepChanging: function (event, currentIndex, newIndex)
        {
            // Always allow going backward even if the current step contains invalid fields!
            if (currentIndex > newIndex)
            {
                return true;
            }

            var form = $(this);

            // Clean up if user went backward before
            if (currentIndex < newIndex)
            {
                // To remove error styles
                $(".body:eq(" + newIndex + ") label.error", form).remove();
                $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
            }

            // Disable validation on fields that are disabled or hidden.
            form.validate().settings.ignore = ":disabled,:hidden";

            // Start validation; Prevent going forward if false
            return form.valid();
        },
        onFinishing: function (event, currentIndex){
            var form = $(this);
            // Disable validation on fields that are disabled.
            // At this point it's recommended to do an overall check (mean ignoring only disabled fields)
            form.validate().settings.ignore = ":disabled";

            // Start validation; Prevent form submission if false
            return form.valid();
        },
        onFinished: function (event, currentIndex){
            var form = $(this);
            // Submit form input
            saveData2(form);
        }
    }).validate({
                errorPlacement: function (error, element){
                    element.before(error);
                }, 
                rules: {
                    installments: {
                    mustbelessthanProductMaxLoanPeriod: true
                    },
                    repayment_frequency: {
                    mustbelessthanProductMaxLoanPeriod: true
                    },
                    <?php if($org['loan_app_stage']==20){ ?>
                    fund_source_account:{
                        remote: {
                        url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                        type: "post",
                        data: {
                                amount: function () {
                                    return $("form#formClient_loan1 input[name='requested_amount']").val();
                                },
                                account_id: function () {
                                return $("form#formClient_loan1 input[name='source_fund_account_id']").val();
                               }

                                }
                            }
                    },
                <?php } ?>
                    repayment_made_every: {
                    mustbelessthanProductMaxLoanPeriod: true
                    }
                }
    });
    
    $("#formClient_loan1").steps({
    labels: {
            finish: "Submit",
            cancel: "Cancel"
            },
    bodyTag: "section",
    onInit: function (event, current) {
        //alert(current);
    },
    onStepChanging: function (event, currentIndex, newIndex)
    {
        // Always allow going backward even if the current step contains invalid fields!
        if (currentIndex > newIndex)
        {
            return true;
        }

        var form = $(this);

        // Clean up if user went backward before
        if (currentIndex < newIndex)
        {
            // To remove error styles
            $(".body:eq(" + newIndex + ") label.error", form).remove();
            $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
        }

        // Disable validation on fields that are disabled or hidden.
        form.validate().settings.ignore = ":disabled,:hidden";

        // Start validation; Prevent going forward if false
        return form.valid();
    },
    onFinishing: function (event, currentIndex){
        var form = $(this);
        // Disable validation on fields that are disabled.
        // At this point it's recommended to do an overall check (mean ignoring only disabled fields)
        form.validate().settings.ignore = ":disabled";

        // Start validation; Prevent form submission if false
        return form.valid();
    },
    onFinished: function (event, currentIndex){
        var form = $(this);
        // Submit form input
        saveData2(form);
    }
    }).validate({
            errorPlacement: function (error, element){
                element.before(error);
            }, 
            rules: {
                installments: {
                mustbelessthanProductMaxLoanPeriod: true
                },
                repayment_frequency: {
                mustbelessthanProductMaxLoanPeriod: true
                },
                <?php if($org['loan_app_stage']==2){ ?>
                source_fund_account_id:{
                    remote: {
                    url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                    type: "post",
                    data: {
                            amount: function () {
                                return $("form#formClient_loan1 input[name='requested_amount']").val();
                            },
                            account_id: function () {
                            return $("form#formClient_loan1 select[name='source_fund_account_id']").val();
                           }

                            }
                        }
                },
            <?php } ?>
                repayment_made_every: {
                mustbelessthanProductMaxLoanPeriod: true
                }
            }
        });

   

        //select2 elements
        var loan_product_length='';
        $("#loan_product_id").select2({dropdownParent:$("#add_pending_approval-modal")});
        <?php if ((isset($case2) && $case2 !='group_loan') && ($type =='client_loan' || (isset($case2) && $case2 =='client_loan')) ):?>
        $("#member_id").select2({dropdownParent:$("#add_pending_approval-modal")});
        <?php endif; ?>
        $("#credit_officer_id").select2({dropdownParent:$("#add_pending_approval-modal")});

         $("#loan_product_id1").select2({dropdownParent:$("#add_client_loan-modal")});
        <?php if ((isset($case2) && $case2 !='group_loan') && ($type =='client_loan' || (isset($case2) && $case2 =='client_loan')) ):?>
        $("#member_id1").select2({dropdownParent:$("#add_client_loan-modal")});
        <?php endif; ?>
        $("#credit_officer_id1").select2({dropdownParent:$("#add_client_loan-modal")});
        $("#collateral_type_id").select2({dropdownParent:$("#add_client_loan-modal")});
        $("#loan_ref_no").select2({dropdownParent:$("#installment_payment-modal")});
        $("#member_id2").select2({dropdownParent:$("#top_client_loan-modal")});
        $("#active_loan_id").select2({dropdownParent:$("#top_client_loan-modal")});
        $("#credit_officer_id2").select2({dropdownParent:$("#top_client_loan-modal")});

        $("form#formClient_loan").validate({
        rules: {
                installments: {
                mustbelessthanProductMaxLoanPeriod: true
                },
                repayment_frequency: {
                mustbelessthanProductMaxLoanPeriod: true
                },
                repayment_made_every: {
                mustbelessthanProductMaxLoanPeriod: true
                }
    },submitHandler: saveData2});
    
$("form#formApprove").validate({
        rules: {
                approved_installments: {
                mustbelessthantheProductMaxLoanPeriod: true
                },
                approved_repayment_frequency: {
                mustbelessthantheProductMaxLoanPeriod: true
                },
                approved_repayment_made_every: {
                mustbelessthantheProductMaxLoanPeriod: true
                }
                
                
    },submitHandler: saveData2});

 $("form#formActive").validate({
        rules: {
                source_fund_account_id:{
                    remote: {
                    url: "<?php echo site_url('journal_transaction_line/check_acc_balance'); ?>",
                    type: "post",
                    data: {
                        amount: function () {
                            return $("form#formActive input[name='amount_approved']").val();
                        },
                        account_id: function () {
                            return $("form#formActive select[name='source_fund_account_id']").val();
                        }
                    }
                }
               }
                
    },submitHandler:  <?php if($org['mobile_payments']==1){ ?>saveData9 <?php }else{ ?>saveData2<?php } ?>});

$('form#formReject').validator().on('submit',saveData);
$('form#formCancle').validator().on('submit',saveData);
$('form#formApplication_withdraw').validator().on('submit',saveData);
$('form#formForward_application').validator().on('submit',saveData);
//$('form#formActive').validate({submitHandler: saveData2});
$('form#formLock').validator().on('submit',saveData);  
$('form#formWrite_off').validate({submitHandler: saveData2});  
$('form#formPay_off').validate({submitHandler: saveData2});  
$('form#formReverse').validator().on('submit',saveData);
$('form#formReverse_approval').validator().on('submit',saveData); 
$('form#formInstallment_payment').validate({
    submitHandler: saveData2,
    //your validation rules
        ignore: ':hidden:not(.do-not-ignore)',
        errorPlacement: function(error, element) {

            if (element.attr("name") == "extra_principal") {
                error.insertBefore($(".loan_curtailment_error"));
            } else if (element.attr("name") == "extra_amount_use") {
            error.insertAfter($(".after-p"));

            } else {
                error.insertAfter(element);
            }
    }
}); 
$('form#formInstallment_payment_multiple').validate({submitHandler: saveData2, ignore: ':hidden:not(.do-not-ignore)'});
/**********Page Data Model (Knockout implementation)***************************/

var Expense_type = function () {
        var self = this;
        self.selected_expense = ko.observable();
    };
    var Income_type = function () {
        var self = this;
        self.selected_income = ko.observable();
    };
    var Collateral_type = function () {
        var self = this;
        self.selected_collateral = ko.observable();
    };

    var Guarantor = function () {
        var self = this;
        self.selected_guarantor = ko.observable();

        // Do a guarantor qualification check
        self.selected_guarantor.subscribe((guarantor) => {
            if(guarantor) {
                let savings_acc_id = guarantor.id;
                let member_id = guarantor.member_id;

                maximum_guarantor_check(member_id).then((result) => {
                    client_loanModel.saving_guarantor_check({
                        valid: result,
                        member_name: guarantor.member_name
                    });
                    if(!result) self.selected_guarantor(null);
                }).catch(() => {
                    self.selected_guarantor(null);
                    client_loanModel.saving_guarantor_check(false);
                });
            }

        });
    };

    var MemberGuarantor = function () {
        var self = this;
        self.selected_member_guarantor = ko.observable();
    };
    var ShareGuarantor = function () {
        var self = this;
        self.selected_share_guarantor = ko.observable();
    };
    var LoanFee = function () {
        var self = this;
        self.selected_fee = ko.observable();
    };
    var SavingsAccount = function () {
        var self = this;
        self.selected_ac = ko.observable();
    };
     var ShareAccount = function () {
        var self = this;
        self.selected_share_ac = ko.observable();
    };
    var Loan_doc_type = function () {
        var self = this;
        self.selected_loan_doc_type = ko.observable();
    };

var Client_loanModel = function () {
    var self = this;
    self.interest_amount_bf = ko.observable(0);
    self.compute_interest_from_disbursement_date = ko.observable("0");
    self.compute_interest_from_disbursement_date.subscribe(() => {
        let new_date = self.action_date();
        let dataobj = {action_date: new_date};
        if (typeof new_date !== 'undefined' && new_date !='') {
            get_new_schedule(dataobj, 1);
        }
    });
    self.selected_ac  = ko.observable();
    self.trans_channel = ko.observable();

    self.saving_guarantor_check = ko.observable();

    //adding expenses on step by step process
        self.added_expense_type = ko.observableArray([new Expense_type()]);
        self.addExpense_type = function () {
            self.added_expense_type.push(new Expense_type());
        };
        self.removeExpense_type = function (selected_type) {
            self.added_expense_type.remove(selected_type);
        };
    //end of the observables

    //adding income on step by step process
        self.added_income_type = ko.observableArray([new Income_type()]);
        self.addIncome_type = function () {
            self.added_income_type.push(new Income_type());
        };
        self.removeIncome_type = function (selected_type) {
            self.added_income_type.remove(selected_type);
        };
    //end of the observables

    //adding collateral on step by step process
        self.added_collateral_type = ko.observableArray([new Collateral_type()]);

        self.added_existing_collateral = ko.observableArray([
            
        ]);

        self.remove_existing_collateral = function (selected_col) {
            self.added_existing_collateral.remove(selected_col);
            self.existing_collateral.push(selected_col);
        }
        self.add_existing_collateral = function (new_collateral) {
            self.added_existing_collateral.push(new_collateral);
            self.existing_collateral.remove(new_collateral);
        }

        self.addCollateral_type = function () {
            self.added_collateral_type.push(new Collateral_type());
            // add select2 to security fees modal
            document.querySelectorAll('.loan_security_fees').forEach(select => {
                $(select).select2();
            });
        };
        self.removeCollateral_type = function (selected_type) {
            self.added_collateral_type.remove(selected_type);
        };
    //end of the observables

    //adding/removing guarantor on step by step process
        self.added_guarantor = ko.observableArray([new Guarantor()]);
        self.addGuarantor = function () {
            self.added_guarantor.push(new Guarantor());
            // add select2 to security fees modal
            document.querySelectorAll('.loan_security_fees').forEach(select => {
                $(select).select2();
            });
        };
        self.removeGuarantor = function (selected_type) {
            self.added_guarantor.remove(selected_type);
        };
    //end of the observables

    //adding/removing members as guarantors (without savings or shares)
    self.added_member_guarantor = ko.observableArray([new MemberGuarantor()]);
    
        self.addMemberGuarantor = function () {
            self.added_member_guarantor.push(new MemberGuarantor());
            // add select2 to security fees modal
            document.querySelectorAll('.loan_security_fees').forEach(select => {
                $(select).select2();
            });
        };
        self.removeMemberGuarantor = function (selected_type) {
            self.added_member_guarantor.remove(selected_type);
        };
    //end of the observables

  //adding/removing share guarantor on step by step process
        self.added_share_guarantor = ko.observableArray([new ShareGuarantor()]);
        self.addShareGuarantor = function () {
            self.added_share_guarantor.push(new ShareGuarantor());
             document.querySelectorAll('.loan_security_fees').forEach(select => {
                $(select).select2();
            });
        };
        self.removeShareGuarantor = function (selected_type) {
            self.added_share_guarantor.remove(selected_type);
        };
    //end of the observables

    //adding/removing loan fees on step by step process
        self.applied_loan_fee = ko.observableArray([new LoanFee()]);
        self.addLoanFee = function () {
            self.applied_loan_fee.push(new LoanFee());
            // add select2 to security fees modal
            document.querySelectorAll('.loan_security_fees').forEach(select => {
                $(select).select2();
            });
        };
        self.removeLoanFee = function (selected_loanfee) {
            self.applied_loan_fee.remove(selected_loanfee);
        };
     //end of the observables

    //adding/removing a savings a/c on step by step process
        self.attached_loan_saving_accounts = ko.observableArray([new SavingsAccount()]);
        self.addSavingAcc = function () {
            self.attached_loan_saving_accounts.push(new SavingsAccount());
            // add select2 to security fees modal
            document.querySelectorAll('.loan_security_fees').forEach(select => {
                $(select).select2();
            });
        };
        self.removeSavingAcc = function (selected_account) {
            self.attached_loan_saving_accounts.remove(selected_account);
        };
    //end of the observables

     //adding/removing a share a/c on step by step process
        self.attached_loan_share_accounts = ko.observableArray([new ShareAccount()]);
        self.addShareAcc = function () {
            self.attached_loan_share_accounts.push(new ShareAccount());
            // add select2 to security fees modal
            document.querySelectorAll('.loan_security_fees').forEach(select => {
                $(select).select2();
            });
        };
        self.removeShareAcc = function (selected_share_account) {
            self.attached_loan_share_accounts.remove(selected_share_account);
        };
    //end of the observables

    //adding/removing loan doc on step by step process
        self.added_loan_doc_type = ko.observableArray([new Loan_doc_type()]);
        self.addLoan_doc = function () {
            self.added_loan_doc_type.push(new Loan_doc_type());
        };
        self.removeLoan_doc = function (selected_member) {
            self.added_loan_doc_type.remove(selected_member);
        };
    //end of the observables


        self.income_items = ko.observable(<?php echo (isset($income_items))?json_encode($income_items):''; ?>);
        self.income_item = ko.observable();

        self.expense_items = ko.observable(<?php echo (isset($expense_items))?json_encode($expense_items):''; ?>);
        self.expense_item = ko.observable();

        self.collateral_list = ko.observable(<?php echo (isset($collateral_types))?json_encode($collateral_types):''; ?>);
        self.collateral = ko.observable();

        
      
        self.guarantors = ko.observable(<?php echo (isset($guarantors))?json_encode($guarantors):''; ?>);

        self.share_guarantors = ko.observable(<?php echo (isset($share_guarantors))?json_encode($share_guarantors):''; ?>);

        self.pay_with = ko.observable(<?php echo (isset($pay_with))?json_encode($pay_with):''; ?>);

        self.relationships = ko.observable(<?php echo (isset($relationship_types))?json_encode($relationship_types):'';?>);
        self.relationship = ko.observable();

        self.payment_modes = ko.observable(<?php echo (isset($payment_modes))?json_encode($payment_modes):'';?>);
        self.payment_mode = ko.observable();

        //application parameteres
        self.complete_application = ko.observable(0);
        self.use_share_as_security = ko.observable(0);
        self.use_savings_as_security = ko.observable(0);
        self.top_up_application = ko.observable(0);
        self.add_guarantor = ko.observable(0);

    self.loan_details = ko.observable();
     self.group_loan_details = ko.observable(<?php if (isset($group_loan_details)) {
        echo json_encode($group_loan_details);
     } ?>);
    self.loan_type = ko.observable('1');
    self.pay_off_data = ko.observable();            
    self.payment_summation = ko.observable();
    self.selected_product = ko.observable();
    self.approval_data = ko.observable();
    self.payment_schedule = ko.observableArray();
    self.product_names = ko.observable(<?php echo json_encode($loanProducts); ?>);
    self.product_name = ko.observable();
    self.entered_amount = ko.observable();

    self.repayment_made_every_detail= ko.observable(<?php echo json_encode($repayment_made_every); ?>);
    self.repayment_made_every= ko.observable();
    self.interest_rate = ko.observable();
    self.amount = ko.observable();
    self.repayment_frequency = ko.observable();
    self.installments = ko.observable();
    self.accounts_list = ko.observableArray(<?php echo (isset($account_list))?json_encode($account_list):''; ?>);
    self.member_names = ko.observable(<?php echo (isset($members))?json_encode($members):''; ?>);
    self.member_name = ko.observable(<?php echo (isset($member))?json_encode($member):''; ?>);
    self.filtered_member_names = ko.computed(() => {
        if(self.member_name()) {
            return self.member_names().filter(m => parseInt(m.id) !== parseInt(self.member_name().id));

        }
        return self.member_names();
    }, self);

    self.all_collaterals = ko.observable(<?php echo (isset($all_collaterals))?json_encode($all_collaterals):''; ?>);
    self.existing_collateral = ko.observableArray([
        ]);

        self.member_collateral = ko.computed(() => {
        let arr = [];
        if(self.member_name() && self.all_collaterals()) {
            arr = self.all_collaterals().filter(col => parseInt(col.member_id) === parseInt(self.member_name().id));
        }
        self.existing_collateral(arr);
        return arr;
        
    }, self);

    self.loan_doc_types = ko.observable(<?php echo (isset($loan_doc_types))?json_encode($loan_doc_types):''; ?>);
     //for payment purposes
    self.active_loans = ko.observableArray(<?php echo (isset($active_loans))?json_encode($active_loans):''?>);
    self.active_loan = ko.observable();

    self.available_loan_saving_accounts = ko.observableArray(<?php echo (!empty($savings_accs) ? json_encode($savings_accs) : '') ?>);

    self.available_loan_share_accounts = ko.observableArray(<?php echo (!empty($share_accs) ? json_encode($share_accs) : '') ?>);

    self.available_loan_fees = ko.observableArray(<?php echo (!empty($available_loan_fees) ? json_encode($available_loan_fees) : '') ?>);
    //filtering the loan fees per product selection

        self.filtered_loan_fees = ko.computed(function () {
            let available_loan_product_loan_fees = [];
            if (self.product_name() && self.available_loan_fees()) {
                available_loan_product_loan_fees = self.available_loan_fees().filter(val => ((parseInt(val.loanproduct_id) == parseInt(self.product_name().id)) && (parseInt(val.chargetrigger_id) != 7)));
            }
            return available_loan_product_loan_fees;
        });

        self.filtered_loan_fees_total = ko.computed(function(){
            if(self.product_name()) {
                let available_loan_product_loan_fees = self.filtered_loan_fees();
                let total = 0;
                let self_app_amount = self.app_amount ? (self.app_amount() ? self.app_amount() : false) : false;
                let app_amount = self_app_amount || self.amount() || self.product_name().min_amount;

                    for(var p = 0; p < available_loan_product_loan_fees.length; ++p){
                        if (parseInt(available_loan_product_loan_fees[p].amountcalculatedas_id)==1) {
                            total += (parseFloat(available_loan_product_loan_fees[p].amount)*parseFloat(app_amount)/parseFloat(100));
                        }else if(parseInt(available_loan_product_loan_fees[p].amountcalculatedas_id)==3){
                            total += parseFloat(self.compute_fee_amount(available_loan_product_loan_fees[p].loanfee_id,app_amount));
                        }else {
                            total += parseFloat(available_loan_product_loan_fees[p].amount);
                        }
                    }
                return curr_format(parseFloat(total));
            }
            
        });
    self.member_savings_accounts =ko.observable();
    self.filter_savings_account = function(member_id){
         var available_savingsac;
            available_savingsac = ko.utils.arrayFilter(self.available_loan_saving_accounts(), function (data) {
               return parseInt(data.member_id) == parseInt(member_id);
            });
            self.member_savings_accounts(available_savingsac);
    }

    //filtering the savings A/C per client selection
        self.filtered_savingac = ko.computed(function () {
            var available_savingsac;
            if (self.member_name()|| self.active_loan()) {
                available_savingsac = ko.utils.arrayFilter(self.available_loan_saving_accounts(), function (data) {
                    if(typeof self.member_name() !='undefined'){
                    return parseInt(data.member_id) == parseInt(self.member_name().id);
                    } else if(typeof self.active_loan() !='undefined'){
                    return parseInt(data.member_id) == parseInt(self.active_loan().member_id);
                    }
                });
            }else if(typeof self.member_names() === 'undefined' || typeof self.member_names() === 'object' ){
                available_savingsac = ko.utils.arrayFilter(self.available_loan_saving_accounts(), function (data) {
                     return parseInt(data.member_id) == parseInt(<?php echo (isset($user))?$user['id']:((isset($_SESSION['member_id']) && !empty($_SESSION['member_id']))?$_SESSION['member_id']:'') ?>);
                });
            }
            return available_savingsac;
        });

         //filtering the share A/C per client selection
        self.filtered_shareac = ko.computed(function () {
            var available_shareac;
            if (self.member_name()|| self.active_loan()) {
                available_shareac = ko.utils.arrayFilter(self.available_loan_share_accounts(), function (data) {
                    if(typeof self.member_name() !='undefined'){
                    return parseInt(data.member_id) == parseInt(self.member_name().id);
                    } else if(typeof self.active_loan() !='undefined'){
                    return parseInt(data.member_id) == parseInt(self.active_loan().member_id);
                    }
                });
            }else if(typeof self.member_names() === 'undefined' || typeof self.member_names() === 'object' ){
                available_shareac = ko.utils.arrayFilter(self.available_loan_share_accounts(), function (data) {
                     return parseInt(data.member_id) == parseInt(<?php echo (isset($user))?$user['id']:((isset($_SESSION['member_id']) && !empty($_SESSION['member_id']))?$_SESSION['member_id']:'') ?>);
                });
            }
            return available_shareac;
        });

    //state totals
    self.state_totals = ko.observableArray(<?php echo (isset($state_totals))?json_encode($state_totals):'' ; ?>);

    //for generating the disbursement sheet at application stage
    <?php $this->load->view('client_loan/loan_steps_files/application_knockoutjs.php');?>
    //paying for the loan
    self.installment_payment_date = ko.observable();
    self.transaction_channel = ko.observableArray(<?php echo (isset($tchannel))?json_encode($tchannel):''; ?>);
    self.tchannels = ko.observable();
    self.payment_data=ko.observable();
    self.penalty_amount = ko.observable();            
    self.loan_ref_no = ko.observable("<?php echo $new_loan_acc_no;?>");
    self.installment_number = ko.observable(); 
    self.principal_amount = ko.observable(0);  
    self.interest_amount = ko.observable(0); 
    self.received_penalty_amount = ko.observable(0); 
    self.extra_principal_amount = ko.observable(); 

    self.loan_installments = ko.observableArray();
    self.loan_installment = ko.observable(); 
    self.selected_installment = ko.observable();
    self.installment_ids = ko.dependentObservable(function () {
        return ko.utils.arrayMap(self.selected_installment(), function (data) {
            if(typeof data !='undefined' ){
             return data.installment_number;
            }
        });
    }); 

    //getting the loan installments for a loan
    self.filtered_active_loan_installment = ko.computed(function () {
        if (self.active_loan()) {
            fetch_installments(self.active_loan().id);
        }
        self.payment_data(null);    
    });

    //filtering active loan per client selection
        self.filtered_active_loan = ko.computed(function () {
            var available_loans;
            if (self.member_name()) {
                available_loans = ko.utils.arrayFilter(self.active_loans(), function (data) {
                    return parseInt(data.member_id) == parseInt(self.member_name().id);
                });
            }else if(typeof self.member_names() === 'undefined' || typeof self.member_names() === 'object'){
                available_loans = ko.utils.arrayFilter(self.active_loans(), function (data) {
                     return parseInt(data.member_id) == parseInt(<?php echo (isset($user))?$user['id']:((isset($_SESSION['member_id']) && !empty($_SESSION['member_id']))?$_SESSION['member_id']:'') ?>);
                });
            }
            self.product_name(null);
            return available_loans;
        });
    self.selected_active_loan=ko.observable();

    self.filter_loan_product=ko.computed(function(){
        var loan_product;
        if(self.selected_active_loan()){
            loan_product= ko.utils.arrayFilter(self.product_names(), function(data){
                return parseInt(data.id) == parseInt(self.selected_active_loan().loan_product_id);
            }); 
        self.product_name(loan_product[0]);                  
        }
         return loan_product;
    });

    //range fees charge calculation
    self.available_loan_range_fees = ko.observableArray(<?php echo (!empty($available_loan_range_fees) ? json_encode($available_loan_range_fees):'') ?>);
    self.compute_fee_amount=function (loan_fee_id,loan_amount) {
       var available_ranges;
       var fee_amount=0;
        if (self.available_loan_range_fees()) {
            available_ranges = ko.utils.arrayFilter(self.available_loan_range_fees(), function (data) {
                return parseInt(data.loan_fee_id) == parseInt(loan_fee_id);
            });

            for (var i = 0; i <= available_ranges.length - 1; i++) {
                if(parseFloat(available_ranges[i].max_range) !='0.00'){

                    if (parseFloat(loan_amount) >=parseFloat(available_ranges[i].min_range) && parseFloat(loan_amount) <=parseFloat(available_ranges[i].max_range)) {
                        
                        fee_amount = parseInt(available_ranges[i].calculatedas_id)==1?(parseFloat(available_ranges[i].range_amount)*parseFloat(loan_amount)/parseFloat(100)):parseFloat(available_ranges[i].range_amount);
                        break;
                    }
                }else if(parseFloat(available_ranges[i].max_range) =='0.00' && parseFloat(available_ranges[i].min_range) !='0.00'){
                    if (parseFloat(loan_amount) >=parseFloat(available_ranges[i].min_range)) {
                        fee_amount = parseInt(available_ranges[i].calculatedas_id)==1?(parseFloat(available_ranges[i].range_amount)*parseFloat(loan_amount)/parseFloat(100)):parseFloat(available_ranges[i].range_amount);
                        break;
                    }
                }
            }
        }

       return fee_amount;
    }

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
            <!-- dataobj['installment_number'] = self.installment_ids(); -->
            get_payment_detail(dataobj);
        }
    });
    self.installment_payment_date.subscribe(function (data) {
        if (typeof data !== 'undefined') {
            get_new_penalty(data);
            get_total_pending_penalty(data);
        }
    });

    //payoff a loan
    self.pay_off_action_date=ko.observable();

    self.pay_off_action_date.subscribe(function(new_pay_off_date){
        var data={};
        data['id']=self.loan_details().id;
        data['state_id']=self.loan_details().state_id;
        data['payment_date']=new_pay_off_date;
        data['loan_end_date']=self.loan_details().last_pay_date;
        var controller = "Loan_installment_payment";
        var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/get_pay_off_data";
        $.ajax({
        url: url,
        data: data,
        type: 'POST',
        dataType:'json',
        success:function(response){
            self.pay_off_data(null);
            self.pay_off_data(response.pay_off_data);             
        },
        fail:function (jqXHR, textStatus, errorThrown) {
        console.log("Failure");
        }
        });
    });


    self.calculate = function () {
        var dataobj = {};
            dataobj['amount'] = typeof self.amount() === 'undefined' ? self.product_name().def_amount : self.amount;
            dataobj['loan_product_id'] = typeof self.product_name() === 'undefined' ? self.product_name().id : self.product_name().id;
            dataobj['interest_rate'] = typeof self.interest_rate() === 'undefined' ? self.product_name().def_interest : self.interest_rate;
            dataobj['repayment_made_every'] = typeof self.repayment_made_every() === 'undefined' ? self.product_name().repayment_made_every : self.repayment_made_every;
            dataobj['repayment_frequency'] = typeof self.repayment_frequency() === 'undefined' ? self.product_name().repayment_frequency : self.repayment_frequency;
            dataobj['installments'] = typeof self.installments() === 'undefined' ? self.product_name().def_repayment_installments : self.installments;
           // dataobj['action_date'] = self.loan_calc_payment_date() || ''; 

        get_new_schedule(dataobj, 2);
    };

    self.loan_product_length = ko.computed( function(){

    if(typeof self.product_name() != 'undefined' && self.product_name() != null){
        var loan_product_length=(self.product_name().max_repayment_installments)*(self.product_name().repayment_frequency);
        var loan_product_period= periods[self.product_name().repayment_made_every-1];

        return loan_product_length+' '+loan_product_period;
    }else if(typeof self.selected_product() != 'undefined' && self.selected_product() != null){
        if(typeof self.selected_product().max_repayment_installments != 'undefined'){
        
        var loan_product_length=(self.selected_product().max_repayment_installments)*(self.selected_product().repayment_frequency);
        var loan_product_period= periods[self.selected_product().repayment_made_every-1];

        return loan_product_length+' '+loan_product_period;

        }else{
            return false;
        }
    }else{
        return false;
    }
    }, this);

    self.product_date = ko.computed( function(){

    if(typeof self.product_name() != 'undefined' && self.product_name() != null){
        var loan_product_length=(self.product_name().max_repayment_installments)*(self.product_name().repayment_frequency);
        var loan_product_period= periods[self.product_name().repayment_made_every-1];

        return moment().add(loan_product_length,loan_product_period);
    }else if(typeof self.selected_product() != 'undefined' && self.selected_product() != null){
        var loan_product_length=(self.selected_product().max_repayment_installments)*(self.selected_product().repayment_frequency);
        var loan_product_period= periods[self.selected_product().repayment_made_every-1];

         return moment().add(loan_product_length,loan_product_period);
    }else{
        return false;
    }
    }, this);
    
    self.approval_date=  ko.observable('<?php //echo date('d-m-Y'); ?>');
    self.suggested_disbursement_date=  ko.observable('<?php //echo date('d-m-Y'); ?>');
    self.action_date=  ko.observable('<?php //echo date('d-m-Y'); ?>');
    self.payment_date=  ko.observable('<?php echo date('d-m-Y'); ?>');
    //self.loan_calc_payment_date=  ko.observable('<?php echo date('d-m-Y'); ?>');
    self.display_table = function (data, click_event) {
        if($(click_event.target).prop("hash")){
            displayed_tab = $(click_event.target).prop("hash").toString().replace("#", "");
        TableManageButtons.init(displayed_tab);
        }
        
    };
    self.action_date.subscribe(function (new_date) {
        var dataobj = {action_date: new_date};
        if (typeof new_date !== 'undefined' && new_date !='') {
            get_new_schedule(dataobj, 1);
        }
    });

    self.payment_date.subscribe(function (data) {
        var dataobj = {new_payment_date: data};
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

    self.amount.subscribe(function (data) {
        if(typeof self.loan_details() != 'undefined'){
            var dataobj = {amount: parseFloat(data) +(parseFloat(self.loan_details().disbursed_amount)-parseFloat(self.loan_details().parent_paid_principal))};
        }else{
        }
        var dataobj = {amount: data};
        if (typeof data !== 'undefined') {
            get_new_schedule(dataobj, 2);
        }


        if(($('#loan_calc_amount').valid())) {
            let member_id = $('#member_id_2').val() || (self.member_name() ? self.member_name().id : '');
            let loan_product_id = self.product_name() ? self.product_name().id : '';
                if(loan_product_id && member_id) {
                    self.get_requestable_amounts(member_id, loan_product_id, parseInt(data));
                }
            
        }

        // hide amortization schedule
        $('#loan_amortization_schedule_1').css('display', 'none');

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

    // #########
    self.extra_amount_available = ko.observable(0);
    self.next_payment_data=ko.observable(); // next installment data

    // Principal or Interest First
    self.interest_first = ko.observable('0');

    self.extra_principal_amount = ko.observable();
    self.extra_amount = ko.observable(0); 
    self.extra_amount_use = ko.observable(1);
    self.extra_amount.subscribe((data) => {
        setTimeout(function(){
                let validator = $('#formInstallment_payment').validate({
                //your validation rules
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "extra_principal") {
                        error.insertBefore($(".loan_curtailment_error"));
                    } else if (element.attr("name") == "extra_amount_use") {
                    // an example
                    error.insertAfter($(".after-p"));

                    } else {
                        error.insertAfter(element);
                    }
                }
            });
            if(parseInt(self.curtail_loan()) == 1) {
              validator.element("#td_extra_principal");  
            } else {
                validator.element("#extra_amount"); 
            }
            
            // $('#formInstallment_payment').valid();


        }, 500);
                
    });

    self.paid_total = ko.observable(); // for multiple installment payments
    self.loan_balance = ko.observable(<?php echo !empty($loan_balance) ? $loan_balance : 0; ?>); // for multiple installment payments

    self.curtail_loan = ko.observable('0');
    self.curtail_loan.subscribe((data) => {
        if(parseInt(data) == 1) {
           
            $('#totalAmount').rules("add", {
                required: true,
                max: parseFloat(self.multiple_installment_max()),
                min: 0,
                messages: {
                    required: 'This Field is required',
                    max: 'Amount is greater than the total loan payable amount ' + curr_format(parseFloat(self.multiple_installment_max())),
                    min: 'Amount is less than ' + curr_format(0),
                   
                },
            });

            
        } else {
            $('#totalAmount').rules("add", {
                required: true,
                max: self.payment_with_savings_max() ? round(self.payment_with_savings_max() , 2) : (self.next_payment_data() ? self.max_total_amount_single_installment() : false),
                min: 0,
                messages: {
                    required: 'This Field is required',
                    max: 'Amount is greater than ' + curr_format(self.payment_with_savings_max() ? round(self.payment_with_savings_max() , 2) : (self.next_payment_data() ? self.max_total_amount_single_installment() : false)),
                    min: 'Amount is less than ' + curr_format(0),
                   
                },
            });
            $('#td_extra_principal-error').css('display', 'none');

            
        }
        $('#formInstallment_payment').valid();

    });

    self.totalAmount = ko.observable(0);
            self.forgive_interest = ko.observable(0);
            self.forgive_penalty = ko.observable(0);
            self.forgiven_interest = ko.observable(0);
            self.forgiven_penalty = ko.observable(0);

            self.forgive_interest.subscribe(() => {
                if(self.payment_data()) {
                    if(self.forgive_interest()) {
                        self.forgiven_interest(parseFloat(self.payment_data().remaining_interest) - ( self.interest_amount() ? parseFloat(self.interest_amount()) : 0) );
                    }
                }
                
            });
            self.forgive_penalty.subscribe(() => {
                if(self.penalty_amount() && self.loan_installment()) {
                    if(self.forgive_penalty()) {
                        self.forgiven_penalty((parseFloat(self.penalty_amount().penalty_value) + parseFloat(self.loan_installment().demanded_penalty)) - ( self.received_penalty_amount() ? parseFloat(self.received_penalty_amount()) : 0) );
                    }
                }
            });

            self.interest_amount.subscribe((data) => {
                let totalAmount = self.totalAmount() ? parseFloat(self.totalAmount()) : 0;
                let interest_amount = self.interest_amount() ? parseFloat(self.interest_amount()) : 0;
                let principal_amount = self.principal_amount() ? parseFloat(self.principal_amount()) : 0;
                let penalty = self.received_penalty_amount() ? parseFloat(self.received_penalty_amount()) : 0;
                let extra_amount = totalAmount - (principal_amount + interest_amount + penalty);
                self.extra_amount(
                    round(parseFloat(extra_amount) , 2)
                );

                self.forgive_interest(0);
            });

            self.received_penalty_amount.subscribe((data) => {
                let totalAmount = self.totalAmount() ? parseFloat(self.totalAmount()) : 0;
                let interest_amount = self.interest_amount() ? parseFloat(self.interest_amount()) : 0;
                let principal_amount = self.principal_amount() ? parseFloat(self.principal_amount()) : 0;
                let penalty = self.received_penalty_amount() ? parseFloat(self.received_penalty_amount()) : 0;
                let extra_amount = totalAmount - (principal_amount + interest_amount + penalty);
                self.extra_amount(
                    round(parseFloat(extra_amount) , 2)
                );

                self.forgive_penalty(0);
            });

            self.principal_amount.subscribe((data) => {
                let totalAmount = self.totalAmount() ? parseFloat(self.totalAmount()) : 0;
                let interest_amount = self.interest_amount() ? parseFloat(self.interest_amount()) : 0;
                let principal_amount = self.principal_amount() ? parseFloat(self.principal_amount()) : 0;
                let penalty = self.received_penalty_amount() ? parseFloat(self.received_penalty_amount()) : 0;
                let extra_amount = totalAmount - (principal_amount + interest_amount + penalty);
                self.extra_amount(
                    round(parseFloat(extra_amount) , 2)
                );
            });

            self.calculate_principal_interest = () => {
                let totalAmount = self.totalAmount();

                if(self.payment_data()) {
                    if(parseFloat(totalAmount) >= parseFloat(self.payment_data().remaining_principal) ) {
                    self.principal_amount(
                        round(parseFloat(self.payment_data().remaining_principal) , 2)
                        );

                    // calculate interest
                    let interest = parseFloat(totalAmount) - parseFloat(self.payment_data().remaining_principal);

                    if(interest > 0) {
                        if(interest <= parseFloat(self.payment_data().remaining_interest)) {
                            self.interest_amount(round(interest, 2));
                            self.extra_amount(0);
                            self.received_penalty_amount(0);
                        }

                        if(interest > parseFloat(self.payment_data().remaining_interest)) {
                            self.interest_amount(round(self.payment_data().remaining_interest , 2));

                            let extra = interest - self.payment_data().remaining_interest;

                            if(extra > 0) {

                                if(self.penalty_amount() && self.loan_installment()) {
                                    if((parseFloat(self.penalty_amount().penalty_value) + parseFloat(self.loan_installment().demanded_penalty)) <= extra) {
                                        self.received_penalty_amount(
                                            round(parseFloat(self.penalty_amount().penalty_value) + parseFloat(self.loan_installment().demanded_penalty) , 2)
                                        );

                                        self.extra_amount(
                                            round(parseFloat(
                                                (extra - (parseFloat(self.penalty_amount().penalty_value) + parseFloat(self.loan_installment().demanded_penalty)))
                                            ), 2)
                                        );
                                    }
                                     if((parseFloat(self.penalty_amount().penalty_value) + parseFloat(self.loan_installment().demanded_penalty)) > extra) {
                                        self.received_penalty_amount(
                                            round(parseFloat(extra) , 2)
                                        );
                                        self.extra_amount(0);
                                     }
                                } else {
                                    self.extra_amount(
                                        round(parseFloat(extra) , 2)
                                        );
                                    self.received_penalty_amount(0);
                                }

                                
                            }else {
                                self.extra_amount(0);
                                self.received_penalty_amount(0);
                            }
                        }

                    } else {
                        self.interest_amount(0);
                        self.extra_amount(0);
                        self.received_penalty_amount(0);
                    }

                }

                    if(parseFloat(totalAmount) < parseFloat(self.payment_data().remaining_principal) ) {
                        self.principal_amount(
                            round(parseFloat(totalAmount) , 2)
                        );
                        self.interest_amount(0);
                        self.extra_amount(0);
                        self.received_penalty_amount(0);

                    }

                    if(parseFloat(totalAmount) < 0 || parseFloat(totalAmount) === parseFloat(0) || totalAmount === '')  {
                        self.principal_amount(0);
                        self.interest_amount(0);
                        self.extra_amount(0);
                        self.received_penalty_amount(0);

                    }
                }
            }


            self.calculate_interest_principal = () => {
                if(self.payment_data()) {

                    let totalAmount = parseFloat(self.totalAmount());
                    let remaining_principal = parseFloat(self.payment_data().remaining_principal);
                    let remaining_interest = parseFloat(self.payment_data().remaining_interest);

                    if(totalAmount <= remaining_interest) {
                        self.interest_amount(round(totalAmount, 2));
                        self.principal_amount(0);
                        self.extra_amount(0);
                        self.received_penalty_amount(0);
                    }

                    if(totalAmount > remaining_interest) {
                        self.interest_amount(round(remaining_interest , 2));

                        let bal1 = totalAmount - remaining_interest;

                        if(bal1 <= remaining_principal) {
                            self.principal_amount(
                                round(parseFloat(bal1))
                            );
                            self.extra_amount(0);
                            self.received_penalty_amount(0);
                        }

                        if(bal1 > remaining_principal) {
                            
                            self.principal_amount(
                                round(parseFloat(remaining_principal) , 2)
                            );

                            let bal2 = bal1 - remaining_principal;

                            if(self.penalty_amount() && self.loan_installment()) {
                                let penalty = parseFloat(self.penalty_amount().penalty_value) + parseFloat(self.loan_installment().demanded_penalty);
                                
                                if( bal2 <= penalty) {
                                    self.received_penalty_amount(
                                        round(parseFloat(bal2) , 2)
                                    );
                                    self.extra_amount(0);
                                }

                                if(bal2 > penalty) {
                                    self.received_penalty_amount(
                                        round(parseFloat(penalty) , 2)
                                    );
                                    self.extra_amount(
                                        round(parseFloat((bal2 - penalty)) , 2)
                                    );
                                }
                            } else {
                                self.received_penalty_amount(0);
                                self.extra_amount(
                                    round(parseFloat(bal2) , 2)
                                );
                            }

                        }
                    }

                }
            }

            // recalculate amounts on changes
            
            self.totalAmount.subscribe((data) => {
                if(parseInt(self.interest_first()) === 1) {
                    self.calculate_interest_principal();
                } else {
                    self.calculate_principal_interest();
                }
                
                self.forgive_penalty(0);
                self.forgive_interest(0);

                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);
            });

            self.interest_first.subscribe(() => {
                if(parseInt(self.interest_first()) === 1) {
                    self.calculate_interest_principal();
                } else {
                    self.calculate_principal_interest();
                }

                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);
            });

            self.penalty_amount.subscribe(() => {

                add_money_formatter();

                if(parseInt(self.interest_first()) === 1) {
                    self.calculate_interest_principal();
                } else {
                    self.calculate_principal_interest();
                }

                self.forgive_penalty(0);
                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);
            });
        
            self.active_loan.subscribe(() => {
                self.forgive_penalty(0);
                self.forgive_interest(0);

                self.edit_principal(false);
                self.edit_interest(false);
                self.edit_penalty(false);
                self.edit_loan_curtailment(false);

                if(self.active_loan()) {
                    get_total_pending_penalty();
                }
            });

            self.total_pending_penalty = ko.observable(0); // Holds Multiple Installment Total Penalty Calculted on the fly

            self.total_demanded_principal = ko.observable(0);
            self.total_demanded_interest = ko.observable(0);
            self.total_demanded_amount = ko.observable(0);
            self.total_demanded_penalty = ko.observable(0);

            self.overall_penalty = ko.computed(() => {
                
                if(self.total_pending_penalty() || self.total_demanded_penalty()) {
                   add_money_formatter(); 
                }

                if(self.total_pending_penalty()) {
                    //return parseFloat(self.total_demanded_penalty()) + parseFloat(self.total_pending_penalty());
                    return parseFloat(self.total_pending_penalty());

                }

                return parseFloat(self.total_demanded_penalty());
                
            }, self).extend({notify:'always'});

            self.loan_installments.subscribe((data) => {
                if(data) {
                    let demanded_principal = 0;
                    let demanded_interest = 0;
                    let demanded_penalty = 0;
                    let demanded_total_amount = 0;

                    let i = 0;

                    data.forEach(installment => {
                        i++;

                        if($('#multiple_installment_payment').val()) {
                            if(i === 1 && self.active_loan()) {
                                get_payment_detail({loan_ref_no: self.active_loan().loan_no, call_type : $('#call_type').val(), installment_number: installment.installment_number});
                            }
                            if(i === 2 && self.active_loan()) {
                                get_next_payment_detail({loan_ref_no: self.active_loan().loan_no, call_type : $('#call_type').val(), installment_number: installment.installment_number});
                            }
                        }

                        if(i === 2 && self.active_loan()) {
                            get_next_payment_detail({loan_ref_no: self.active_loan().loan_no, call_type : $('#call_type').val(), installment_number: installment.installment_number});
                        }

                       demanded_principal += (parseFloat(installment.principal_amount)-(installment.paid_principal_amount?parseFloat(installment.paid_principal_amount):parseFloat(0)));
                        demanded_interest += (parseFloat(installment.interest_amount)-(installment.paid_interest_amount?parseFloat(installment.paid_interest_amount):parseFloat(0)));
                        demanded_total_amount += parseFloat(installment.total_amount);
                        demanded_penalty += parseFloat(installment.demanded_penalty);
                    });

                    self.total_demanded_amount(demanded_total_amount);
                    self.total_demanded_principal(demanded_principal);
                    self.total_demanded_interest(demanded_interest);
                    self.total_demanded_penalty(demanded_penalty);
                }
            });
           
            self.pay_multiple_installments = ko.observable(1);
            self.with_interest = ko.observable(0);

            self.edit_principal = ko.observable(false);
            self.edit_interest = ko.observable(false);
            self.edit_penalty = ko.observable(false);
            self.edit_loan_curtailment = ko.observable(false);

            self.edit_click = (inputId) => {
                $(`#${inputId}`).off('blur');
                $(`#${inputId}`).on('blur', () => {
                    let validator = $('#formInstallment_payment').validate({
                        //your validation rules
                            errorPlacement: function(error, element) {
                            if (element.attr("name") == "extra_amount_use") {
                            // an example
                            error.insertAfter($(".after-p"));

                            } else {
                                error.insertAfter(element);
                            }
                        }
                    });
                    let isValid = validator.element(`#${inputId}`);

                    if(inputId === 'td_principal_amount' && isValid) self.edit_principal(false);

                    if(inputId === 'td_interest_amount' && isValid) self.edit_interest(false);

                    if(inputId === 'td_penalty' && isValid) self.edit_penalty(false);

                    if(inputId === 'td_extra_principal' && isValid) self.edit_loan_curtailment(false);
                    //$('#formInstallment_payment').valid();
                });
            }

            // Computing max and min Loan Curtailment
            self.loan_curtailment_max = ko.observable(0);
            self.loan_curtailment_min = ko.observable(0);

            self.principal_amount.subscribe((data) => {
                if(data != undefined) {
                    let paid_principal = parseFloat(data);
                    let remaining_principal = self.total_demanded_principal() - paid_principal;
                    let max = round((remaining_principal * 0.8), 2);
                    let min = round((1), 2);

                    self.loan_curtailment_max(max);
                    self.loan_curtailment_min(min); 
                }
            });

            self.extra_amount.subscribe((data) => {
                let paid_principal = self.principal_amount();
                let remaining_principal = self.total_demanded_principal() - paid_principal;
                let max = round((remaining_principal * 0.8), 2);
                let min = round((1), 2);

                self.loan_curtailment_max(max);
                self.loan_curtailment_min(min);                 
            });

            // Computing Max and Min
            self.max_total_amount_single_installment = ko.computed(() => {
                let remaining_principal = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_principal)
                ) : 0;

                let remaining_interest = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_interest)
                ) : 0;

                let next_installment_principal = self.next_payment_data() ? (
                    parseFloat(self.next_payment_data().remaining_principal)
                ) : 0;

                let next_installment_interest = self.next_payment_data() ? (
                    parseFloat(self.next_payment_data().remaining_interest)
                ) : 0;

                let penalty = self.penalty_amount() ? (
                    parseFloat(self.penalty_amount().penalty_value)
                ) : 0;

                let demanded_penalty = self.loan_installment() ? (
                    parseFloat(self.loan_installment().demanded_penalty)
                ) : 0;

                let total = (remaining_principal + remaining_interest + next_installment_principal + next_installment_interest + penalty + demanded_penalty);
                return round(parseFloat(total) , 2);

            },self);

            // Computing Penalty For Single Installment
            self.single_installment_total_penalty = ko.computed(() => {
                let on_the_fly_penalty = self.penalty_amount() ? (
                    parseFloat(self.penalty_amount().penalty_value)
                ) : 0;

                let old_penalty = self.loan_installment() ? (
                    parseFloat(self.loan_installment().demanded_penalty)
                ) : 0;

                let total = (on_the_fly_penalty + old_penalty);

                return round(parseFloat(total) , 2);
            }, self);

            // Computing Expected Total
            self.expected_total = ko.computed(() => {
                let penalty = self.single_installment_total_penalty();
                let principal = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_principal)
                ) : 0;
                let interest = self.payment_data() ? (
                    parseFloat(self.payment_data().remaining_interest)
                ) : 0;

                let total = (penalty + principal + interest);

                return total;

            }, self);
        
        // set maximum payable amount when using savings for payment

            self.payment_with_savings_max = ko.observable(false);

            self.payment_mode.subscribe((data) => {
                if(data && parseInt(data.id) === 5) {
                   <!--  self.payment_with_savings_max(round(self.expected_total(), 2)); -->
                    self.payment_with_savings_max(false); 
                }else {
                    self.payment_with_savings_max(false);   
                }
                setTimeout(() => add_money_formatter(), 1000);
            });
        
        self.forgive_payoff_penalty = ko.observable(0);
        self.forgive_payoff_penalty_max = ko.observable(0);
        self.pay_off_max = ko.observable(0);
        self.forgive_payoff_penalty_min = ko.observable(0);
        self.pay_off_min = ko.observable(0);
        self.pay_off_data.subscribe(() => {
            let penalty = self.pay_off_data() ? self.pay_off_data().penalty_value : 0;
            let to_date_interest_sum = self.pay_off_data() ? self.pay_off_data().to_date_interest_sum : 0;
            let principal_sum = self.pay_off_data() ? self.pay_off_data().principal_sum : 0;
            let already_paid_sum = self.pay_off_data() ? self.pay_off_data().already_paid_sum : 0;

            let total =  (((parseFloat(penalty)+parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1)) ;

            let total_max = Math.ceil( (((parseFloat(penalty)+parseFloat(to_date_interest_sum)+parseFloat(principal_sum))*1)-(parseFloat(already_paid_sum)*1))/100 )*100;
            
            self.pay_off_max(total_max);
            self.pay_off_min(round(total, 2));

            self.forgive_payoff_penalty_max( Math.ceil((total - penalty) / 100) *100 );
            self.forgive_payoff_penalty_min(round(( total - penalty ) , 2));
        });


        // change max and min rules 
        self.forgive_payoff_penalty.subscribe((data) => {
            if(data) {
                $( "#payoff_paid_total" ).rules( "add", {
                    required: true,
                    max: self.forgive_payoff_penalty_max(),
                    min: self.forgive_payoff_penalty_min(),
                    messages: {
                        required: "This field is required.",
                        max: jQuery.validator.format('Amount is greater than ' +  curr_format(self.forgive_payoff_penalty_max())),
                        min: jQuery.validator.format('Amount is less than ' +  curr_format(self.forgive_payoff_penalty_min())),
                    }
                });
            } else {

                $( "#payoff_paid_total" ).rules( "add", {
                    required: true,
                    max: self.pay_off_max(),
                    min: self.pay_off_min(),
                    messages: {
                        required: "This field is required.",
                        max: jQuery.validator.format('Amount is greater than ' +  curr_format(self.pay_off_max())),
                        min: jQuery.validator.format('Amount is less than ' +  curr_format(self.pay_off_min())),
                    }
                });

            }

            //$('#formPay_off').valid();
            if($('#payoff_paid_total').val()) {
                $("#formPay_off").data('validator').element('#payoff_paid_total');
            }
            
        });

        // Multiple Installment Max & Min Amount 
            self.multiple_installment_max = ko.observable(0);
            self.multiple_installment_min = ko.observable(0);

            self.paid_total_change = ko.computed(() => {
                let data = self.overall_penalty();
                let total_demanded = self.total_demanded_amount();
                let overall_total = round((parseFloat(total_demanded) + parseFloat(data)) ,2);

                let remaining_principal = self.payment_data() ? self.payment_data().remaining_principal : 0;
                let remaining_interest = self.payment_data() ? self.payment_data().remaining_interest : 0;
                let next_principal = self.next_payment_data() ? self.next_payment_data().remaining_principal : 0;
                let next_interest = self.next_payment_data() ? self.next_payment_data().remaining_interest : 0;

                let min_total = parseFloat(remaining_principal) + parseFloat(remaining_interest) + parseFloat(next_principal)+parseFloat(next_interest);

                //let min = Math.ceil(min_total / 100) * 100;
                self.multiple_installment_min(round(min_total,2));

                self.multiple_installment_max( round((overall_total + (0.2 * overall_total)), 2) );

                return true;
                
            }, self).extend({
            notify: 'always'
        });
       
    // Loan Qualification Check
    self.requestable_amounts = ko.observable();
            self.loan_type2 = ko.observable("<?php echo (isset($case2) && !empty($case2)) ? $case2 : 'client_loan'; ?>");

            self.qualification_check = ko.computed(() => {
                if(self.requestable_amounts()) {
                    if(self.requestable_amounts().hasOwnProperty('needed_col')) {
                        return (self.requestable_amounts().needed_col === 0);
                    }
                    return parseFloat(self.requestable_amounts().max) > parseFloat(self.requestable_amounts().min);
                }
                return false;
            }, self).extend({notify:'always'});

            self.qualification_check.subscribe((data) => {
                if(!data) {
                    $('a[href="#next"]').parent().attr({'aria-disabled' : 'true', 'class' : 'disabled'});
                    $('a[href="#next"]').attr({'href' : '#xxx',});
                } else {
                    $('a[href="#xxx"]').attr({'href' : '#next',});
                    $('a[href="#next"]').parent().attr({'aria-disabled' : 'false', 'class' : ''});
                }
            });

            self.member_name.subscribe((data) => {
                if(data && data.id && self.product_name() && self.product_name().id) {
                    self.get_requestable_amounts(data.id, self.product_name().id);
                }
            });

            self.product_name.subscribe((data) => {
                let member_id =  $('#member_id_2').val() || (self.member_name() ? self.member_name().id : '') || "<?php echo (isset($user['id']) ? $user['id'] : ''); ?>";
                if(data && data.id && member_id) {
                    self.get_requestable_amounts(member_id, data.id);
                }

                if(data && $('#loan_calc_amount').val()) {
                    self.amount(parseFloat(data.min_amount));
                }

                if(data) {
                // Fetch new_account_no
                get_new_client_loan_account_no(data.id);
                }
                
            });

            self.get_requestable_amounts = (member_id, loan_product_id, loan_amount) => {
                self.requestable_amounts(null);
                $.ajax({
                    url: '<?php echo isset($case2) && $case2 == 'My Loans' ? site_url("u/loans/get_requestable_loan_amounts") : site_url("client_loan/get_requestable_loan_amounts"); ?>',
                    data: {
                        member_id: member_id,
                        loan_product_id: loan_product_id,
                        amount: loan_amount // Used with Loan Calculator to check needed collateral
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        self.requestable_amounts(response);
                    },
                    fail: function (jqXHR, textStatus, errorThrown) {
                        self.requestable_amounts(null);
                        console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                    }
                });

            }



    // maximum loans a member can guarantee on
    self.max_loans_to_guarantee = ko.observable(<?php echo !empty($org['max_loans_to_guarantee']) ? $org['max_loans_to_guarantee'] : null ?>);
};

client_loanModel = new Client_loanModel();
ko.applyBindings(client_loanModel, $("#tab-loans")[0]);
$.validator.addMethod("mustbelessthanProductMaxLoanPeriod", function(value, element) {
        $(element).attr('data-rule-mustbelessthanProductMaxLoanPeriod');
        var account_length=(parseInt($('#installment').val())*parseInt($('#paid_every').val()));
        var account_period=periods[parseInt($('#period_id').val())-1];
        var account_date= moment().add(account_length,account_period);
        if(typeof client_loanModel.product_date() != 'undefined'){
            var period_difference=client_loanModel.product_date().diff(account_date,'days');
            if(period_difference >= 0){
                return true;
            }else{
                return false;
            }
        } else {
            return false;
        }
    },"This period exceedes the above stated period");

$.validator.addMethod("mustbelessthantheProductMaxLoanPeriod", function(value, element) {
        $(element).attr('data-rule-mustbelessthantheProductMaxLoanPeriod');
        var account_length=(parseInt($('#approved_installments').val())*parseInt($('#approved_repayment_frequency').val()));
        var account_period=periods[parseInt($('#approved_repayment_made_every').val())-1];

        var account_date= moment().add(account_length,account_period);

        if(typeof client_loanModel.product_date() != 'undefined'){
            var period_difference=client_loanModel.product_date().diff(account_date,'days');

            if(period_difference >= 0){
                return true;
            }else{
                return false;
            }

        }else{
            return false;
        }
    },"This period exceedes the above stated period");

    $('#existing-collateral').on('select2:select', function (e) {
        let selected_col = JSON.parse(e.params.data.element.value);
        client_loanModel.add_existing_collateral(JSON.parse(e.params.data.element.value));
        let remaing_collateral = client_loanModel.existing_collateral().filter(val => parseInt(selected_col.id) !== parseInt(val.id));
        client_loanModel.existing_collateral(remaing_collateral);
        
        $('#existing-collateral').val([0]).trigger('change');

    });

    let showSingleInstallmentForm = () => {
        $('#multiple_installment_payment-modal').modal('hide');
        $('#installment_payment-modal').modal('show');
    }

    //Get total Penalty for multiple installments
    let get_total_pending_penalty = (new_date) => {
        var data = {};
        data['payment_date']=new_date ? new_date : (client_loanModel.installment_payment_date() ? client_loanModel.installment_payment_date() : (client_loanModel.payment_date() ? client_loanModel.payment_date() : "<?php echo date('d-m-Y') ?>"));
        
        data['client_loan_id'] = client_loanModel.active_loan().id;
        data['state_id'] = client_loanModel.active_loan().state_id;

        $.ajax({
            url: '<?php echo site_url("loan_installment_payment/get_total_penalty_data"); ?>',
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                client_loanModel.total_pending_penalty(response.total_penalty);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    let add_money_formatter = () => {
    // Money Format options
    let options = {
        digitGroupSeparator: ',',
        decimalCharacter: '.',
        //decimalCharacterAlternative: '.',
        allowDecimalPadding: 'floats'
    }
    if (!AutoNumeric.isManagedByAutoNumeric('.money-format')) {
        allMoneyInputs = new AutoNumeric.multiple('.money-format', options);
    } else {
        allMoneyInputs.forEach(el => el.remove()); // Remove AutoNumeric listeners and reset values to ''
        allMoneyInputs = new AutoNumeric.multiple('.money-format', options); // Re-initialise AutoNumeric
    }

    let moneyInputs = document.querySelectorAll('.money-format');
    // console.log('\n\n', moneyInputs, '\n\n');

    moneyInputs.forEach(input => {
        input.addEventListener('change', (e) => {
            let inputId = input.id;
            // console.log('\n\n', inputId, '\n\n');
            // console.log(e.target.value, '\n');
            let str_val = e.target.value;
            let num_val = parseFloat(str_val.split(',').join(''));

            // console.log('Number Value : ', num_val);

            let numeric_input_id = `numeric-${inputId.split('-')[1]}`;

            // console.log('\n\n', 'Num Input ID', numeric_input_id, '\n\n');

            $(`#${numeric_input_id}`).val(num_val).change();
            $('#formInstallment_payment_multiple').valid();
            $('#formInstallment_payment').valid();

        })
    })
}

    //getting payment data for next installment
    function get_next_payment_detail(new_data) {
        var url = "<?php echo site_url("loan_installment_payment/payment_data"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType:'json',
            success:function (response) {
                //populate the observables
                client_loanModel.next_payment_data(response.payment_data);
                //client_loanModel.penalty_amount(response.penalty_data);
                },
            fail:function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
        });
    }

    let maximum_guarantor_check = (member_id) => {
        return new Promise((resolve, reject) => {
            let url = "<?php echo site_url("client_loan/get_member_guaranteed_active_loans") ?>";
            $.ajax({
                url: url,
                data: {
                    member_id : member_id
                },
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    let loans = [...response.guarantors, ...response.savings, ...response.shares];

                    function onlyUnique(value, index, self) {
                        return self.indexOf(value) === index;
                    }

                    let unique_loans = loans.filter(onlyUnique);

                    let loans_count = unique_loans.length;

                    // console.log(response);
                    if(client_loanModel.max_loans_to_guarantee() != null) {
                        resolve(loans_count < parseInt(client_loanModel.max_loans_to_guarantee()));
                    } else {
                        resolve(true);
                    }
                    
                },
                fail: function(jqXHR, textStatus, errorThrown) {
                    console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                    reject(false);
                }
            });
        });
    }

    let loan_calculator_qualification_check = (amount, member_id, loan_product_id) => {
        return new Promise((resolve, reject) => {

            let url = "<?php echo site_url("client_loan/loan_calculator_qualification_check") ?>";
            $.ajax({
                url: url,
                data: {
                    member_id: member_id,
                    loan_product_id: loan_product_id,
                    amount: amount
                },
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    let loans = [...response.guarantors, ...response.savings, ...response.shares];

                    function onlyUnique(value, index, self) {
                        return self.indexOf(value) === index;
                    }

                    let unique_loans = loans.filter(onlyUnique);

                    let loans_count = unique_loans.length;

                    // console.log(response);
                    if(client_loanModel.max_loans_to_guarantee() != null) {
                        resolve(loans_count < parseInt(client_loanModel.max_loans_to_guarantee()));
                    } else {
                        resolve(true);
                    }
                    
                },
                fail: function(jqXHR, textStatus, errorThrown) {
                    console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                    reject(false);
                }
            });

        })
    }

     const get_new_client_loan_account_no = (loan_product_id) => {
                        
                        let url = "<?php  echo site_url('client_loan/get_loan_account_no')?>";
                        $.ajax({
                            url: url,
                            data: {
                                loan_product_id: loan_product_id
                            },
                            type: 'POST',
                            dataType: 'json',
                            success: function(response) {
                                let loan_ref_no = response.data.new_account_no;
                                client_loanModel.loan_ref_no(loan_ref_no);
                            },
                            error: function() {
                                console.log('Getting new Account no error');
                            }
                        });
                }
    
    $('#multiple_installment_payment-modal').on('hide.bs.modal', function(e) {

    // Resetting Form
    client_loanModel.installment_payment_date('');      
    client_loanModel.forgive_penalty('');      
    client_loanModel.with_interest('');      
    client_loanModel.payment_mode('');      
    client_loanModel.trans_channel('');      
    client_loanModel.selected_ac(''); 
    $('#formInstallment_payment_multiple').trigger("reset");     

    });

    $('#installment_payment-modal').on('hide.bs.modal', function(e) {

    // Resetting Form
    client_loanModel.installment_payment_date('');      
    client_loanModel.curtail_loan('0');     
    client_loanModel.payment_mode('');  
    client_loanModel.interest_first('0');  
    client_loanModel.extra_amount_use('');  
    client_loanModel.trans_channel('');      
    client_loanModel.selected_ac(''); 
    $('#formInstallment_payment').trigger("reset"); 

    });






