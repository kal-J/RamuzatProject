        //select2 elements
        $("#loan_product_id").select2({dropdownParent:$("#add_pending_approval-modal")});
        <?php if (!isset($group)):?>
        $("#group_id").select2({dropdownParent:$("#add_pending_approval-modal")});
        <?php endif; ?>
        $("#credit_officer_id").select2({dropdownParent:$("#add_pending_approval-modal")});
        var Group_loanModel = function () {
            var self = this;
            self.product_names = ko.observable(<?php echo json_encode($loanProducts); ?>);
            self.product_name = ko.observable();
            self.loan_type = ko.observable();
            self.loan_ref_no = ko.observable(<?php echo json_encode($new_loan_no); ?>);
            self.application_date=  ko.observable('<?php echo date('d-m-Y'); ?>'); 
            self.repayment_made_every_detail= ko.observable(<?php echo json_encode($repayment_made_every); ?>);
            self.payment_modes = ko.observable(<?php echo (isset($payment_modes))?json_encode($payment_modes):'';?>);
            self.payment_mode = ko.observable();
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
                     };
            
            self.loan_product_length = ko.computed( function(){

            if(typeof self.product_name() != 'undefined'){
                var loan_product_length=(self.product_name().max_repayment_installments)*(self.product_name().repayment_frequency);
                var loan_product_period= periods[self.product_name().repayment_made_every-1];

                return loan_product_length+' '+loan_product_period;
            }else{
                return false;
            }
            }, this);

            self.product_date = ko.computed( function(){

            if(typeof self.product_name() != 'undefined'){
                var loan_product_length=(self.product_name().max_repayment_installments)*(self.product_name().repayment_frequency);
                var loan_product_period= periods[self.product_name().repayment_made_every-1];

                return moment().add(loan_product_length,loan_product_period);
            }else{
                return false;
            }
            }, this);

            self.group_leader_present = ko.observable(false);
          };

        group_loanModel = new Group_loanModel();
        ko.applyBindings(group_loanModel, $("#tab-loans")[0]);
       
        //loan period validation
        $.validator.addMethod("mustbelessthanProductMaxLoanPeriod", function(value, element) {
                $(element).attr('data-rule-mustbelessthanProductMaxLoanPeriod');
                var account_length=(parseInt($('#installment').val())*parseInt($('#paid_every').val()));
                var account_period=periods[parseInt($('#period_id').val())-1];

                var account_date= moment().add(account_length,account_period);

                if(typeof group_loanModel.product_date() != 'undefined'){
                    var period_difference=group_loanModel.product_date().diff(account_date,'days');

                    if(period_difference >= 0){
                        return true;
                    }else{
                        return false;
                    }

                }else{
                    return false;
                }
            },"This period exceedes the above stated period");

 
            $("form#formGroup_loan").validate({
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