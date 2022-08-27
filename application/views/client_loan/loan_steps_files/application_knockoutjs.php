            self.application_date=  ko.observable('<?php //echo date('d-m-Y'); ?>'); 
            self.app_action_date=  ko.observable('<?php //echo date('d-m-Y'); ?>');
            self.app_interest = ko.observable();
            self.app_amount=ko.observable();
            self.app_offset_period=ko.observable();
            self.app_offset_every=ko.observable();
            self.app_installments=ko.observable();
            self.fund_source_account_id=ko.observable();
            self.fund_source_account=ko.observable();
            self.app_repayment_frequency=ko.observable();
            self.app_repayment_made_every=ko.observable();
            self.app_penalty_rate=ko.observable();

            self.oncheck= function() {
                    return true;
            };
            
            self.filtered_detials = ko.computed(function(){
                if (self.product_name()) {
                    self.app_interest(self.product_name().def_interest);

                    if(typeof self.group_loan_details() !== 'undefined'){
                        self.app_amount( (parseFloat(self.group_loan_details().borrowed_amount)>0) ? (parseFloat(self.group_loan_details().requested_amount)-parseFloat(self.group_loan_details().borrowed_amount)) : parseFloat(self.group_loan_details().requested_amount));
                    }else{
                        self.app_amount();
                    }
                    self.app_offset_period(self.product_name().def_offset);
                    self.fund_source_account_id(self.product_name().fund_source_account_id);
                    self.fund_source_account(self.product_name().fund_source_account);
                    self.app_offset_every(self.product_name().offset_made_every);
                    self.app_installments(self.product_name().def_repayment_installments);
                    self.app_repayment_frequency(self.product_name().repayment_frequency);
                    self.app_repayment_made_every(self.product_name().repayment_made_every);
                    self.app_penalty_rate(self.product_name().def_penalty_rate);
                }
            });

            //Payment schedule at application stage
            self.app_interest.subscribe(function(data){
               var dataobj = {interest: data};
                if (typeof data !== 'undefined' && data !='') {
                    get_payment_schedule(dataobj);
                }
            });
            self.app_action_date.subscribe(function(data){
               var dataobj = {action_date: data};
                if (typeof data !== 'undefined' && data !='') {
                    get_payment_schedule(dataobj);
                }
            });
            self.app_amount.subscribe(function (data) {
                var new_amount;
                if(typeof self.selected_active_loan() != 'undefined'){
                    new_amount=(parseFloat(self.selected_active_loan().expected_principal) - parseFloat(self.selected_active_loan().paid_principal))+parseFloat(data);
                }else{
                  new_amount=data;
                }
                var dataobj = {amount: new_amount};              
                if (typeof data !== 'undefined' && data !='') {
                    get_payment_schedule(dataobj);
                }
            });
            self.app_offset_period.subscribe(function (data) {
                var dataobj = {offset_period: data};
                if (typeof data !== 'undefined' && data !='') {
                    get_payment_schedule(dataobj);
                }
            });
            self.app_offset_every.subscribe(function (data) {
                var dataobj = {offset_made_every: data};
                if (typeof data !== 'undefined') {
                    get_payment_schedule(dataobj);
                }
            });
            self.app_installments.subscribe(function (data) {
                var dataobj = {installments: data};
                if (typeof data !== 'undefined') {
                    get_payment_schedule(dataobj);
                }
            });
            self.app_repayment_frequency.subscribe(function (data) {
                var dataobj = {repayment_frequency: data};
                if (typeof data !== 'undefined') {
                    get_payment_schedule(dataobj);
                }
            });
            self.app_repayment_made_every.subscribe(function (data) {
                var dataobj = {repayment_made_every: data};
                if (typeof data !== 'undefined') {
                    get_payment_schedule(dataobj);
                }
            });

            
