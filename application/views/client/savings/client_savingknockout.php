    $('form#formSavings_account').validate({submitHandler: saveData2});
    $('form#formDeposit').validate({submitHandler: saveData2});
    $('form#formTransfer').validate({submitHandler: saveData2});
    $('form#formChange_state').validate({submitHandler: saveData2});
    //$('form#formWithdraw').validate({submitHandler: saveData2});

       $("form#formWithdraw").validate({
                rules: {
                        transaction_channel_id:{
                            remote: {
                            url: "<?php echo site_url('u/savings/check_channel_balance'); ?>",
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
        self.display_table = function (data, click_event) {
            TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
        };
        self.ProductOptions = ko.observableArray(<?php echo json_encode($products); ?>);
        self.organisationFormats = ko.observable(<?php echo json_encode($organisation_format); ?>);
        self.savings_accounts=ko.observableArray(null);
        self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
        self.withdraw_fees = ko.observableArray(null);
        self.transfer_fees = ko.observableArray(null);
        self.deposit_fees = ko.observableArray(null);
        self.group_members = ko.observableArray();
        self.member_nm = ko.observable();
        self.selected_account = ko.observable();
        self.accountw = ko.observable();
        self.account_trans = ko.observable();
        self.fees = ko.observable();
        self.account_no_id=ko.observable();
        self.tchannels = ko.observable();
        self.Product = ko.observable();
        self.User = ko.observable();
        self.deposit_amount = ko.observable(0);
        self.totaldepositCharges = ko.observable(null);
        self.withdraw_amount = ko.observable(0);
        self.transfer_amount = ko.observable(0);
        self.account_balance = ko.observable();
        self.getAccounts =ko.observable();
        //for activation purposes
        self.action_msg = ko.observable();
        self.account_state = ko.observable();

            //activation purpose

        // var org_initial = '<?php //echo $organisation['org_initial']; ?>';
        // self.account_no = ko.computed(function () {
        //  var count = typeof self.organisationFormats().account_counter !== 'undefined'?zeroFill(parseInt(self.organisationFormats().account_counter)+1, 6):'';
        //    var sec = moment().format('ss'), min = moment().format('MM'), yr = moment().format('YY'),
        //            account_format = org_initial + "-" + yr + count + sec + min;
        //  if (typeof self.organisationFormats().account_format !== 'undefined' && self.organisationFormats().account_format == '2') {
        //      account_format = yr + count + sec + min + "-" + org_initial;
        //  } 
        //    if (typeof self.organisationFormats().account_format !== 'undefined' && self.organisationFormats().account_format == '3') {
        //      account_format = yr + count + sec + min;
        //  }
        //  return account_format;
        //});

        
        self.selected_account.subscribe(function (data) {
            var check = "deposit";
            if (typeof data.deposit_Product_id !== 'undefined') {
                get_new_charge(check, data.deposit_Product_id, "<?php echo site_url("u/savings/deposit_fees"); ?>");
            }
        });
        self.accountw.subscribe(function (data) {
            var check = "withdraw";
            if (typeof data.deposit_Product_id !== 'undefined') {
                get_new_charge(check, data.deposit_Product_id, "<?php echo site_url("u/savings/withdraw_fees"); ?>");
                this.account_balance = ko.observable(data.cash_bal);
            }

        });
        self.account_trans.subscribe(function (data) {
            var check = "transfer";
            if (typeof data.deposit_Product_id !== 'undefined') {
                get_new_charge(check, data.deposit_Product_id, "<?php echo site_url("u/savings/transfer_fees"); ?>");
                this.account_balance = ko.observable(data.cash_bal);

            }
            $.ajax({
            url: '<?php echo site_url("u/savings/get_savings_accounts"); ?>',
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
            self.clients = ko.observableArray(<?php echo json_encode($sorted_clients); ?>);
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

        //After transfer charges
        self.totaltransferCharges = ko.computed(function () {
            total = 0;
            ko.utils.arrayForEach(self.transfer_fees(), function (transferfee) {
                if (transferfee.cal_method_id == 1) {  //1-percentage 2-fixed
                    total += (parseFloat(transferfee.amount) * (self.transfer_amount())) / 100;
                } else {
                    total += parseFloat(transferfee.amount);
                }
            });
            return total;   
        }, this);

    };
             
    savingsModel = new SavingsModel();
    ko.applyBindings(savingsModel, $("#tab-savings")[0]);

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
