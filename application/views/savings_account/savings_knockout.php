    $('form#formSavings_account').validate({submitHandler: saveData2});
    $('form#formDeposit').validate({submitHandler: saveData2});
    $('form#formTransfer').validate({submitHandler: saveData2});
    $('form#formChange_state').validate({submitHandler: saveData2});
    $('form#formTransaction').validate({submitHandler: saveData2});
    $('form#formBulk_deposit').validate({submitHandler: saveData3});
    $('form#formReverseTransaction').validate({submitHandler: saveData2});
    $('form#formWithdraw_requests').validate({submitHandler: saveData2});
    $('form#formDeclinedWithdraw_requests').validate({submitHandler: saveData10});
    

       $("form#formWithdraw").validate({
        rules: {
                transaction_channel_id:{
                    remote: {
                    url: "<?php echo site_url('savings_account/check_channel_balance'); ?>",
                    type: "post",
                    data: {
                        transaction_channel_id: function () {
                            return $("form#formWithdraw select[name='transaction_channel_id']").val();
                        },
                        amount: function () {
                            return $("form#formWithdraw input[name='amount']").val();
                        }
                    }
                }
               }      
        },submitHandler: saveData2});


    var SavingsModel = function () {
        var self = this;
        self.isPrinting_active = ko.observable(false);
        self.isPrinting_pending = ko.observable(false);
        self.display_table = function (data, click_event) {
            if($(click_event.target).prop("hash")){
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            }
        };
        self.ProductOptions = ko.observableArray(<?php echo (isset($products))?json_encode($products):''; ?>);
        self.children = ko.observableArray(<?php echo (isset($children))?json_encode($children):''; ?>);
        self.child_id = ko.observable();
        
        self.new_account_no = ko.observable(<?php echo (isset($new_account_no))?json_encode($new_account_no):''; ?>);
        self.savings_accounts=ko.observableArray(null);
        self.savings_account = ko.observable();
        self.saving_ahead = ko.observable();
        self.repayment_made_every = function(repayment_key) {
            return periods[parseInt(repayment_key)-parseInt(1)];
        }

        self.payment_modes = ko.observable(<?php echo (isset($payment_modes))?json_encode($payment_modes):'';?>);
        self.payment_mode = ko.observable();

        self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
        self.withdraw_fees = ko.observableArray(null);
        self.transfer_fees = ko.observableArray(null);
        self.deposit_fees = ko.observableArray(null);
        self.group_members = ko.observableArray();
        self.member_nm = ko.observable();
        self.fees_upon_approval = ko.observableArray();
        self.selected_account = ko.observable();
        self.selected_mode_w = ko.observable();
        self.selected_mode_d = ko.observable();
        self.accountw = ko.observable();
        self.account_trans = ko.observable();
        self.payment_mode1 = ko.observable();
        self.fees = ko.observable();
        self.account_no_id=ko.observable();
        self.tchannels = ko.observable();
        self.Product = ko.observable();
        self.User = ko.observable();
        self.deposit_amount = ko.observable(0);
        self.totaldepositCharges = ko.observable(null);
        self.withdraw_amount = ko.observable(0);
        self.term_lenght = ko.observable(0);
        self.transfer_amount = ko.observable(0);
        self.account_balance = ko.observable();
        self.getAccounts =ko.observable();
        self.name_error =ko.observable(0);

        self.deposit_Product_id =ko.observable();

         self.Product = ko.observable();

    self.Product.subscribe((data) => {
        if(data) {
            // Fetch new_account_no
            get_new_savings_account_no(data.id);
        }
    })

        //for dashboard deposit
        self.saving_accounts =ko.observableArray();
        self.saving_accounts =ko.observableArray();
        self.update_accounts= function(){
            $.ajax({
                url: '<?php echo site_url('savings_account/account_list') ?>',
                data: {},
                dataType:'json',
                type:'Post',
                success: function(response){
                    self.saving_accounts(response.data);
                },
                fail: function(){
                    console.log("error happened")
                }

            })
        };

        //for activation purposes
        self.action_msg = ko.observable();
        self.account_state = ko.observable();
        self.transaction_date_de = ko.observable();
        self.transaction_date_bulk = ko.observable();
        self.transaction_date_wi = ko.observable();
        self.transaction_date_open = ko.observable();
        self.transaction_date_tr = ko.observable();
        var savings_data =ko.observableArray(<?php echo (isset($savings_data))?json_encode($savings_data):'' ; ?>);
        self.total_savings=ko.observable(<?php echo (isset($total_savings))?json_encode($total_savings):'' ; ?>);
        var clients=[];
        var s_amount=[];
        var s_percent=[];
        savings_data().forEach(function(v,i){
          clients.push(v.name);
          s_amount.push(parseFloat(v.y));
          s_percent.push((parseFloat(v.y)/parseFloat(<?php echo (isset($total_savings))?json_encode($total_savings):'' ; ?>))*100);
        });
        if (clients.length !== 0) {
            draw_basic_bar_graph("bar_graph","Savings per Individual","Account Balance: <b>{point.y:,.2f}</b>",clients,s_amount);
            draw_basic_bar_graph("bar_graph1","Shares per Individual","Share Percentage: <b>{point.y:,.2f}</b>",clients,s_percent);
        }

        //state totals
        self.ac_state_totals = ko.observableArray(<?php echo (isset($ac_state_totals))?json_encode($ac_state_totals):'' ; ?>);

        // Withraw requests state totals
        self.accepted_requests_totals = ko.observable(<?php echo isset($withdraw_requests['accepted'])? $withdraw_requests['accepted']: 0?>);
        self.declined_requests_totals = ko.observable(<?php echo isset($withdraw_requests['declined'])? $withdraw_requests['declined'] : 0 ?>);
        self.pending_requests_totals = ko.observable(<?php echo isset($withdraw_requests['pending'])? $withdraw_requests['pending'] :0 ?>);

         //range fees charge calculation
            self.available_savings_range_fees = ko.observableArray(<?php echo (!empty($available_savings_range_fees) ? json_encode($available_savings_range_fees):'') ?>);
            self.compute_fee_amount=function (savings_fee_id,amount) {
               var available_ranges;
               var fee_amount=0;
                if (self.available_savings_range_fees()) {
                    available_ranges = ko.utils.arrayFilter(self.available_savings_range_fees(), function (data) {
                        return parseInt(data.saving_fee_id) == parseInt(savings_fee_id);
                    });

                    for (var i = 0; i <= available_ranges.length - 1; i++) {
                        if(parseFloat(available_ranges[i].max_range) !='0.00'){

                            if (parseFloat(amount) >=parseFloat(available_ranges[i].min_range) && parseFloat(amount) <=parseFloat(available_ranges[i].max_range)) {
                                
                                fee_amount = parseInt(available_ranges[i].calculatedas_id)==1?(parseFloat(available_ranges[i].range_amount)*parseFloat(amount)/parseFloat(100)):parseFloat(available_ranges[i].range_amount);
                                break;
                            }
                        }else if(parseFloat(available_ranges[i].max_range) =='0.00' && parseFloat(available_ranges[i].min_range) !='0.00'){
                            if (parseFloat(amount) >=parseFloat(available_ranges[i].min_range)) {
                                fee_amount = parseInt(available_ranges[i].calculatedas_id)==1?(parseFloat(available_ranges[i].range_amount)*parseFloat(amount)/parseFloat(100)):parseFloat(available_ranges[i].range_amount);
                                break;
                            }
                        }
                    }
                }
               return fee_amount;
            }


            //interest range rate calculation
            self.available_interest_range_rates = ko.observableArray(<?php echo (!empty($available_interest_range_rates) ? json_encode($available_interest_range_rates):'') ?>);
            self.compute_rate_amount=function (product_id,term_length) {
               var available_rates;
               var range_rate=0;
                if (self.available_interest_range_rates()) {
                    available_rates = ko.utils.arrayFilter(self.available_interest_range_rates(), function (data) {
                        return parseInt(data.product_id) == parseInt(product_id);
                    });

                    for (var i = 0; i <= available_rates.length - 1; i++) {
                        if(parseFloat(available_rates[i].max_range) !='0.00'){
                            if (parseFloat(term_length)>=parseFloat(available_rates[i].min_range) && parseFloat(term_length) <=parseFloat(available_rates[i].max_range)) {
                                range_rate = available_rates[i].range_amount;
                                break;
                            }
                        }else if(parseFloat(available_rates[i].max_range) =='0.00' && parseFloat(available_rates[i].min_range) !='0.00'){
                            if (parseFloat(term_length) >=parseFloat(available_rates[i].min_range)) {
                                range_rate = available_rates[i].range_amount;
                                break;
                            }
                        }
                    }
                }
               return range_rate;
            }



        self.selected_account.subscribe(function (data) {
            var check = "deposit";
            if (typeof data !== 'undefined' && typeof data.deposit_Product_id !== 'undefined') {
                get_new_charge(check, data.deposit_Product_id, "<?php echo site_url("savings_account/deposit_fees"); ?>");
                self.deposit_Product_id(data.deposit_Product_id);
            }
        });


          self.selected_mode_w.subscribe(function (data) {
            var check = "withdraw";
            if (typeof data !== 'undefined') {
            var pay_mtd=parseInt(data.id)===1?8:parseInt(data.id)===2?9:parseInt(data.id)===4?6:parseInt(data.id)===6?12:parseInt(data.id)===7?13:parseInt(data.id)===8?14:0;
                get_withdraw_charge(check,pay_mtd, self.deposit_Product_id, "<?php echo site_url("savings_account/withdraw_fees2"); ?>");
            }
        });

         self.selected_mode_d.subscribe(function (data) {
            var check = "deposit";
            if (typeof data !== 'undefined') {
            var pay_mtd=parseInt(data.id)===1?8:parseInt(data.id)===2?9:parseInt(data.id)===4?6:parseInt(data.id)===6?11:parseInt(data.id)===7?10:parseInt(data.id)===8?15:0;
                get_withdraw_charge(check,pay_mtd, self.deposit_Product_id, "<?php echo site_url("savings_account/deposit_fees2"); ?>");
            }
        });


       

        self.accountw.subscribe(function (data) {
            var check = "withdraw";
            console.clear();
            console.log("Hello", data.deposit_Product_id);
            if (typeof data.deposit_Product_id !== 'undefined') {
                get_new_charge(check, data.deposit_Product_id, "<?php echo site_url("savings_account/withdraw_fees"); ?>");
                this.account_balance = ko.observable(data.cash_bal);
                self.deposit_Product_id(data.deposit_Product_id);
            }

        });
            if (typeof self.payment_mode1 !== 'undefined') {
                self.payment_mode1.subscribe(function(data) {
                var check = "withdraw";
                var pay_mtd=parseInt(data.id)===1?8:parseInt(data.id)===2?9:parseInt(data.id)===4?6:0;
                    get_withdraw_charge(check,pay_mtd, self.deposit_Product_id, "<?php echo site_url("savings_account/withdraw_fees2"); ?>");
                });
            }

        self.account_trans.subscribe(function (data) {
            var check = "transfer";
            if (typeof data.deposit_Product_id !== 'undefined') {
                get_new_charge(check, data.deposit_Product_id, "<?php echo site_url("savings_account/transfer_fees"); ?>");
                this.account_balance = ko.observable(data.cash_bal);
                self.deposit_Product_id(data.deposit_Product_id);

            }
            $.ajax({
            url: '<?php echo site_url("savings_account/get_savings_accounts"); ?>',
            data: {account_no_id: data.id},
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                    self.savings_accounts(response.savings_accounts);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
          });  

        });
    <?php if (!isset($user)): ?>
            self.clients = ko.observableArray(<?php echo (isset($sorted_clients))?json_encode($sorted_clients):'';  ?>);
            //filter the clients based on the type of clients the product is available to
            self.filteredClients = ko.computed(function () {
                if (typeof self.Product() !== 'undefined') {
                    return ko.utils.arrayFilter(self.clients(), function (client) {
                        return (parseInt(client.client_type) == parseInt(self.Product().availableto) || parseInt(self.Product().availableto) === 3);
                    });
                }
            });
    <?php endif; ?>
        //After charges on deposit	
        self.totaldepositCharges = ko.computed(function () {
            total = 0;
            ko.utils.arrayForEach(self.deposit_fees(), function (depositfee) {
                if (depositfee.cal_method_id == 1) {
                    total += (parseFloat(depositfee.amount) * (self.deposit_amount())) / 100;
                }else if(depositfee.cal_method_id == 3){
                    total += parseFloat(self.compute_fee_amount(depositfee.savings_fees_id,self.deposit_amount()));
                }else {
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
                }else if(withdrawfee.cal_method_id == 3){
                    total += parseFloat(self.compute_fee_amount(withdrawfee.savings_fees_id,self.withdraw_amount()));
                } else {
                    total += parseFloat(withdrawfee.amount);
                }
            });
            return total;   //to be reviewed later...
        }, this);

        //After transfer charges
        self.totaltransferCharges = ko.computed(function () {
            total = 0;
            ko.utils.arrayForEach(self.transfer_fees(), function (transferfee) {
                if (transferfee.cal_method_id == 1) {  //1-percentage 2-fixed
                    total += (parseFloat(transferfee.amount) * (self.transfer_amount())) / 100;
                }else if(transferfee.cal_method_id == 3){
                    total += parseFloat(self.compute_fee_amount(transferfee.savings_fees_id,self.transfer_amount()));
                } else {
                    total += parseFloat(transferfee.amount);
                }
            });
            return total;   
        }, this);

    };
             
    savingsModel = new SavingsModel();
    ko.applyBindings(savingsModel, $("#tab-savings")[0]);
    savingsModel.update_accounts();

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
                    savingsModel.deposit_fees(response.deposit_fees);
                } else if (check == "withdraw") {
                    savingsModel.withdraw_fees(response.withdraw_fees);

                } else if (check == "transfer") {
                    savingsModel.transfer_fees(response.transfer_fees);

                }
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

        //getting withdraw charges
    function get_withdraw_charge(check, payment_id, new_product_id, url) {
        $.ajax({
            url: url,
            data: {new_product_id: new_product_id,payment_id: payment_id},
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                    savingsModel.withdraw_fees(response?.withdraw_fees);
                    savingsModel.deposit_fees(response?.deposit_fees);
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    $('#select_child').select2({
        dropdownParent: $('#add_savings_account')
    });

    $('#select_savings_product').on('change', function(e) {
        $('#select_child').select2({
            dropdownParent: $('#add_savings_account')
        });
    });

    $('#select_savings_product').select2({
            dropdownParent: $('#add_savings_account')
    });

    $('#select_savings_account_client').on('change', function (e) {
        let member_id = e.target.value;

        if(savingsModel.Product() && parseInt(savingsModel.Product().producttype) === 4 && member_id) {
            $.ajax({
                url: "<?php echo site_url('children/jsonList') ?>",
                data: {
                    member_id: member_id
                },
                type: 'POST',
                dataType: 'json',
                success: function (response) {
                    console.log('\n\n\n\n', response);
                    if(e.target.value) {
                        savingsModel.children(response.data);
                    }
                },
                fail: function (jqXHR, textStatus, errorThrown) {
                    console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });

            $('#select_child').select2({
                dropdownParent: $('#add_savings_account')
            });
        }        
    });

        const get_new_savings_account_no = (deposit_Product_id) => {
                        
                            let url = "<?php echo site_url('savings_account/get_new_account_no')?>";
                            $.ajax({
                                url: url,
                                data: {
                                    deposit_Product_id: deposit_Product_id
                                },
                                type: 'POST',
                                dataType: 'json',
                                success: function(response) {
                                    let new_account_no = response.data.new_account_no;
                                    savingsModel.new_account_no(new_account_no);
                                },
                                error: function() {
                                    console.log('Getting new Account no error');
                                }
                            });
                    }
