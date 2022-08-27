$('form#formBuy_shares').validate({submitHandler: saveData2});
        $('form#formConvert_shares').validate({submitHandler: saveData2});
        $('form#formShares_state').validate({submitHandler: saveData2});
        $('form#formRefund').validate({submitHandler: saveData2});
        $('form#formTransfer_share').validate({submitHandler: saveData2});
        $('form#formBulk_deposit').validate({submitHandler: saveData3});
        $('form#formAlert_setting').validate({submitHandler: saveData3});
          $('form#formCustom_email').validate({submitHandler: saveData3});
        $('form#formReverseShare_transaction').validate({submitHandler: saveData2});
        $('.sharem_accounts').select2({dropdownParent: $("#transfer")});
        $('.select2able').select2({dropdownParent: $("#add_share_account-modal")});
        $('.select2able2').select2({dropdownParent: $("#bulk_deposit_template-modal")});
  
        $('form#formShares').validate({submitHandler: saveData2});
        $( "#end_date1" ).datepicker({  maxDate: 0 });

var SharesModel = function() {
    var self = this;
   
        self.period_types = ko.observable([{"id": 1, "period_name": "As At"}, {
                "id": 2,
                "period_name": "Date Range"
            }]);
      self.gender_label = ko.observable([{"id": 0, "title": "Female"}, {
                "id": 1,
                "title": "Male"
            }]);
      

    // self.share_issuances = ko.observable(<?php //echo json_encode($share_issuances); ?>);
    self.transaction_status = ko.observable("1");
    self.end_date3 = ko.observable("<?php echo date('d-m-Y'); ?>");
    self.share_issuance = ko.observableArray(<?php echo isset($share_issuances) ? json_encode($share_issuances) : []; ?>);
    self.issuance = ko.observable();
    
    self.category_id = ko.observable();
    
      //subscriptions
    self.month = ko.observable(0);
    self.year =ko.observable(1);
 

    self.month.subscribe((data) => {
        if(data) {
            self.year(false);
        }
    });
    self.year.subscribe((data) => {
        if(data) {
            self.month(false);
        }
    });
    self.members = ko.observableArray(<?php echo json_encode($members); ?>);
    self.member = ko.observable();
    self.savings_accounts=ko.observableArray(null);
    self.savings_account = ko.observable(); 
    self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
    self.payment_modes = ko.observable(<?php echo (isset($payment_modes))?json_encode($payment_modes):'';?>);
    self.payment_modes_bulk_trans = ko.observable(<?php echo (isset($payment_modes_bulk_trans))?json_encode($payment_modes_bulk_trans):'';?>);
    self.payment_mode = ko.observable();
    self.payment_mode_filtered = ko.computed(function(){
        return ko.utils.arrayFilter(self.payment_modes(), function(pay) {
            return pay.id !=5;
        });
    });
    self.transaction_date_wi = ko.observable();
    self.share_call = ko.observableArray(null);
    self.firstcall = ko.observableArray(<?php echo json_encode($firstcall); ?>);
    self.selected_account = ko.observable();
    self.call_amount = ko.observable(0);
    self.application_id=ko.observable();
    self.tchannels = ko.observable();
    self.get_share_calls = ko.observable();
    self.sharecalls = ko.observable();
    self.shares_puchaced = ko.observable(0);
    self.approved_shares = ko.observable(0);
    self.shares_requested = ko.observable(0);
    self.application_details = ko.observable();
    self.call_payment =ko.observable();
    self.share_account_no_id=ko.observable();
    self.account_trans = ko.observable();
    self.account_tr = ko.observable();
    self.share_accounts=ko.observableArray();
    self.share_account = ko.observable();
    self.name_error =ko.observable(0);

    self.share_account2=ko.observable();
    // self.saving_accounts_transfer_to = ko.computed(() => {
    //     get_accounts_details(self.share_account);
    // }, self)  

    
    self.accountd = ko.observable();
    self.transfer_fees = ko.observableArray(null);
    self.deposit_fees = ko.observableArray(null);
    self.deposit_amount = ko.observable(0);
    self.totaldepositCharges = ko.observable(null);
    self.totaltransferCharges = ko.observable(null);
    self.transfer_amount = ko.observable(0);
    self.period = ko.observable();
    self.category = ko.observable();
    //subscriptions
    self.month = ko.observable(0);
    self.year =ko.observable(1);
 

    self.month.subscribe((data) => {
        if(data) {
            self.year(false);
        }
    });
    self.year.subscribe((data) => {
        if(data) {
            self.month(false);
        }
    });
    self.membership = ko.observable();
    self.period_savings = ko.observable();
    
    self.shares = ko.observable();
    self.selected_period = ko.observable(0);
        // from query report
    self.share_report = ko.observable();
    self.monthly_report = ko.observable();
    self.no_of_shareholders = ko.observable();
    self.total_shares = ko.observable();
    self.price_per_share = ko.observable();
    self.savings = ko.observable();
    
    self.male_members = ko.observable();
    self.female_members = ko.observable();
    self.issuance_name2=ko.observable();
    self.new_accounts_created=ko.observable();
    self.previous_month=ko.observable();
    //self.previous_month=ko.observable(<?php //echo //json_encode($previous_month);?>);
    //self.num_account_created=ko.observable();
    //self.num_account_created2=ko.observable();
    self.shares_bought=ko.observable();
    self.share_price =ko.observable();
    self.amount=ko.observable();
    self.amount2=ko.observable();
    
    self.share_report1 = ko.observable();
    self.no_of_shareholders1 = ko.observable();
    self.total_shares1 = ko.observable();
    self.price_per_share1 = ko.observable();
    self.savings1 = ko.observable();
    self.savings_count1 = ko.observable();
    self.male_members1 = ko.observable();
    self.female_members1 = ko.observable();
    self.month_to_filter = ko.observable();
    self.rowSpan_value=ko.observable();
    // enhanced  general report data variables
        self.male_members = ko.observable(0);
        self.female_members = ko.observable(0);
        self.no_of_shareholders1= ko.observable(0);
        self.total_credit_sum1= ko.observable(0);
    
    self.inactive_accounts =ko.observable();
    self.deactivated_accounts=ko.observable();
    self.active_shares_accounts=ko.observable();
    self.total_credit_sum=ko.observable();
    self.total_debit_sum=ko.observable();
    self.no_trans_reversed=ko.observable();
    self.no_of_shares_transfered=ko.observable();
    self.gender_summary_data=ko.observable();
    self.no_of_shares_bought=ko.observable();
    self.fiscal_2 = ko.observable();
    self.fiscal_3 = ko.observable();
    self.fiscal_1 = ko.observable();
    self.overall_total_t1=ko.observable();
    self.overall_total_t2=ko.observable();
    self.total_shares_t2=ko.observable();
    self.summary_data=ko.observable();
    self.total_credit=ko.observable();
    self.total_share_credit=ko.observable();
    self.total_debit=ko.observable();
    self.total_shares=ko.observable();
    self.start_date=ko.observable();
    self.start_date3=ko.observable('');
    self.end_date=ko.observable();
    self.start_date1=ko.observable();
    self.end_date1=ko.observable();
    self.alert_type=ko.observable();
    self.total_shares_amount=ko.observable();
    self.no_of_shares_sold=ko.observable();

     self.issuance = ko.observable();

    self.issuance.subscribe((data) => {
        if(data) {
            // Fetch new_account_no
            get_new_shares_account_no(data.id);
        }
    })

     self.new_account_no= ko.observable();

    self.share_account2.subscribe(function(new_id) {
        if (typeof new_id !== 'undefined' && new_id !== null) {
            get_member_savings_account(new_id.member_id, parseInt(new_id.client_type));
            //get_product_details();
        }
    });

    

    // end
            self.account_trans.subscribe(function (data) {
                    get_share_accounts(data);
            });

            self.accountd.subscribe(function (data) {
            var check = "deposit";
            if (typeof data.share_issuance_id !== 'undefined') {
                get_new_charge(check, data.share_issuance_id, "<?php echo site_url("shares/deposit_fees"); ?>");
                //this.account_balance = ko.observable(data.cash_bal);
            }

            });

             self.account_tr.subscribe(function (data) {
            var check = "transfer";
            if (typeof data.share_issuance_id !== 'undefined') {
                get_new_charge(check, data.share_issuance_id, "<?php echo site_url("shares/transfer_fees"); ?>");
                //this.account_balance = ko.observable(data.cash_bal);
            }

            });

             //After charges on deposit 
        self.totaldepositCharges = ko.computed(function () {
            total = 0;
            ko.utils.arrayForEach(self.deposit_fees(), function (depositfee) {
                if (depositfee.amountcalculatedas_id == 1) {
                    total += (parseFloat(depositfee.amount) * (self.deposit_amount())) / 100;
                }else {
                    total += parseFloat(depositfee.amount);
                }
            });
            return total;   //to be reviewed later...
        }, this);

          //After transfer charges
        self.totaltransferCharges = ko.computed(function () {
            total = 0;
            ko.utils.arrayForEach(self.transfer_fees(), function (transferfee) {
                if (transferfee.amountcalculatedas_id == 1) {  //1-percentage 2-fixed
                    total += (parseFloat(transferfee.amount) * (self.transfer_amount())) / 100;
                }else {
                    total += parseFloat(transferfee.amount);
                }
            });
            return total;   
        }, this);


    self.member_accounts = ko.observable();
    self.member.subscribe(function(new_id) {
        if (typeof new_id !== 'undefined' && new_id !== null) {
            get_member_savings_account(new_id.id, parseInt(new_id.client_type));
            //get_product_details();
        }
    });

    self.display_table = function (data, click_event) {
    TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
     self.member.subscribe(function(new_id) {
        if (typeof new_id !== 'undefined' && new_id !== null) {
            get_member_savings_account(new_id.id, parseInt(new_id.client_type));
            //get_product_details();
        }
    });



    self.share_call.subscribe(function(data) {
        var check = "buy_shares";
        if (typeof data.id !== 'undefined') {
            update_share_calls(data.id, data.share_issuance_id,
                "<?php echo site_url("share_call/active_share_calls"); ?>");
            //this.account_balance = ko.observable(data.cash_bal);
        }
    });
};
}

sharesModel = new SharesModel();


//getting member's savings account
    function get_member_savings_account(data_id, client_type) {
        var url = "<?php echo site_url("savings_account/jsonList2"); ?>";
        $.ajax({
            url: url,
            data: {
                client_id:data_id,
                state_id:7,
                client_type: client_type ? client_type : '<?php echo isset($client_type) ? $client_type : 1; ?>'
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //clear the the other fields because we are starting the selection afresh
                sharesModel.member_accounts(null);

                sharesModel.member_accounts(response.accounts_data);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    function get_share_accounts(data) {
        var url = '<?php echo site_url("shares/get_share_accounts"); ?>';
        $.ajax({
            url: url,
            data: {
                share_account_no_id: data.id,
                status_id:1,
                state_id:7
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //clear the the other fields because we are starting the selection afresh
                sharesModel.share_accounts(null);
                //populate the observables
                sharesModel.share_accounts(response.share_accounts);

            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    function get_accounts_details(data) {
        var url = '<?php echo site_url("shares/get_account_details"); ?>';
        $.ajax({
            url: url,
            data: {id: data.id,status_id:1,state_id:7},
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //clear the the other fields because we are starting the selection afresh
                //sharesModel.call_payment(null);
                //populate the observables
                sharesModel.call_payment(response.data);

            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    
    //getting charges
    function get_new_charge(check, new_product_id, url) {
        $.ajax({
            url: url,
            data: {new_product_id: new_product_id},
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //populate the observables
                if (check == "deposit") {
                    sharesModel.deposit_fees(response.deposit_fees);
                } else if (check == "withdraw") {
                    sharesModel.withdraw_fees(response.withdraw_fees);

                } else if (check == "transfer") {
                    sharesModel.transfer_fees(response.transfer_fees);

                }
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

        function update_share_calls(new_application_id,share_issuance_id, url) {
        $.ajax({
            url: url,
            data: {new_application_id: new_application_id,share_issuance_id:share_issuance_id},
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                    sharesModel.share_call(response.sharecall);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    <?php $this->load->view('savings_account/deposits/function_js'); ?>

    $(document).ready(() => {
        if(window.location.pathname === '/efinanciv2/shares'){
          $('#enable_print').hide();
        }else {
            $('#enable_print').show();
        }

        $('#btn_printing_active_shares').css('display', 'none');
        $('#btn_printing_pending_shares').css('display', 'none');
        $('#btn_printing_inactive_shares').css('display', 'none');
        
    });
    // generated the new account numbers  .
     const get_new_shares_account_no = (share_issuance_id) => {
                        
                            let url = "<?php  echo site_url('shares/get_new_account_no')?>";
                            $.ajax({
                                url: url,
                                data: {
                                    share_issuance_id: share_issuance_id
                                },
                                type: 'POST',
                                dataType: 'json',
                                success: function(response) {
                                    let new_account_no = response.data.new_account_no;
                                    sharesModel.new_account_no(new_account_no);
                                },
                                error: function() {
                                      console.log("Could not generate new share account");
                                }
                            });
                    }
     

