//loan approval handling
    $('table tbody').on('click', 'tr .approve_loan', function (e) {
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
            client_loanModel.loan_details(data);
            dt.search("").draw();
            var controller = "Client_loan";
            var url = "<?php echo site_url(); ?>/" + controller.toLowerCase() + "/get_approval_data";
            $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType:'json',
            success:function(response){
                client_loanModel.approval_data(null);
                client_loanModel.selected_product(null);
                client_loanModel.product_name(null);
                client_loanModel.selected_product(response.selected_product);
                client_loanModel.approval_data(response.approval_data);
            }
            });
        });
    //loan disbursement handling
    $('table tbody').on('click', 'tr .disburse', function (e) {
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
            client_loanModel.loan_details(data);
            dt.search("").draw();
            var data_set={};
            var data1={};
            var controller = "Client_loan";
            <?php if(($org['loan_app_stage']==0)||($org['loan_app_stage']==1)){ ?>
                data_set=data;
                var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement";
            <?php }elseif($org['loan_app_stage']==2){ ?>
                var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/disbursement1";
                data1['offset_period1']=data.offset_period;
                data1['offset_made_every1']=data.offset_made_every;
                data1['amount1']=parseFloat(data.requested_amount)+(parseFloat(data.disbursed_amount)-parseFloat(data.parent_paid_principal));
                data1['product_type_id1']=data.product_type_id;
                data1['interest_rate1']=data.interest_rate;
                data1['installments1']=data.installments;
                data1['repayment_made_every1']=data.repayment_made_every;
                data1['repayment_frequency1']=data.repayment_frequency;
                data1['loan_product_id1']=data.loan_product_id;
                data_set=data1;
            <?php } ?>
            $.ajax({
            url: url,
            data: data_set,
            type: 'POST',
            dataType:'json',
            success:function(response){
                client_loanModel.action_date(null);
                client_loanModel.action_date('<?php //echo date('d-m-Y'); ?>');
                client_loanModel.payment_schedule(null); 
                client_loanModel.available_loan_fees(null);
                client_loanModel.available_loan_fees(response.available_loan_fees);
                client_loanModel.payment_schedule(response.payment_schedule);
                client_loanModel.payment_summation(response.payment_summation); 
            }
            });
        });
    //loan pay_off handling and writting off
    $('table tbody').on('click', 'tr .money_action', function (e) {
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
            client_loanModel.loan_details(data);
            client_loanModel.filter_savings_account(data.member_id);
            dt.search("").draw();
            var controller = "Loan_installment_payment";
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/get_pay_off_data";
            $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType:'json',
            success:function(response){
                client_loanModel.pay_off_data(null);
                client_loanModel.pay_off_data(response.pay_off_data); 
                client_loanModel.available_loan_fees(null);
                client_loanModel.available_loan_fees(response.available_loan_fees)              
            }
            });
        });
    //Rejecting a loan
     $('table tbody').on('click', 'tr .action_on_loan', function (e) {
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
            client_loanModel.loan_details(data);
            dt.search("").draw();
        });