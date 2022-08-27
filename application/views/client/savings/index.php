  <div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2> Accounts</h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?php echo base_url('u/home')?>">Home</a>
            </li>
            <li class="breadcrumb-item active">
                <strong>Accounts</strong>
            </li>
        </ol>
    </div>
    <div class="col-sm-8">
        
    </div>
</div>
<div class="row white-bg">
 <div class="col-lg-12">
    <br>
    <div class="table-responsive">
            <table class="table  table-bordered table-hover" id="tblSavings" width="100%" >
            <thead>
                <tr>
                    <th>Account No</th>
                    <th>Account Type</th>
                    <th>Category</th>
                    <th>Available Balance</th>
                    <th>Action</th> 
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
       <?php $this->load->view('client/savings/deposits/add_modal'); ?>
       <?php $this->load->view('client/savings/withdraws/add_modal'); ?>
       <?php $this->load->view('client/savings/withdraws/request_withdraw'); ?>
       <?php $this->load->view('client/savings/withdraws/transfer'); ?>
</div>
</div>
<script>
    var dTable = {};
    var savingsModel = {};
    var TableManageButtons = {};

    $(document).ready(function () {

    <?php $this->load->view('client/savings/client_savingknockout.php'); ?>

        <?php if(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==1 ){?>
        $('form#formSentePayDeposit').validate({submitHandler: saveData});

        <?php }elseif(isset($payment_engine['payment_id']) && $payment_engine['payment_id'] ==3){?>
            $('form#formBeyonicDeposit').validate({submitHandler: saveData2});
         <?php  } ?>


        $(".select2able").select2({
            allowClear: true
        });

        var handleDataTableButtons = function (tabClicked) {
        <?php $this->load->view('client/savings/savings_account_js.php'); ?>
        <?php $this->load->view('savings_account/deposit_withdraw_js.php'); ?>
        
        };

        TableManageButtons = function () {
            "use strict";
            return {
                init: function (tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();

        TableManageButtons.init("tab-active_accounts");

    });  //end document ready
    function consumeDtableData(dTableData) {
        var theData = dTableData.data;
        //fill the client accounts array with new data, whenever there is a table reload.
        if (theData.length > 0) {
            if (theData[0]['item_value']) {//collateral data array
                loanDetailModel.collateral_amount(sumUp(theData, 'item_value'));
            }
            if (theData[0]['amount_locked']) {//guarantor data array
                loanDetailModel.guarantor_amount(sumUp(theData, 'amount_locked'));
            }
        }
    }
    function reload_data(form_id, response) {
        switch (form_id) {
            case "formSavings":
                
                TableManageButtons.init("tab-savings_account_pending");
                if(typeof response.accounts !== 'undefined' && response.accounts != ''){
                   savingsModel.clients(response.accounts);
                }
                if(typeof response.organisation_format !== 'undefined'){
                    savingsModel.organisationFormats(response.organisation_format);
                }
                break;
            case "formChange_state":
            case "formWithdraw":
            case "formTransfer":
            case "formDeposit":
               if (typeof response.insert_id !== 'undefined') {
                    window.location = "<?php echo site_url('u/transaction/print_receipt/'); ?>" + response.insert_id;
                }
                dTable['tblSavings'].ajax.reload(null, true);
                savingsModel.clients(response.accounts);
                break;
            default:
                //nothing really to do here
                break;
        }
    }



//function to determine the due date for the check out process
    function due_date() {

        var today = new Date();
        today.setMinutes( today.getMinutes() + 20 );
        var year = today.getFullYear();
        var actual_date = today.getDate();
        var month = (today.getMonth() + 1);

        if (actual_date < 10)
            actual_date = "0" + actual_date;

        if (month < 10)
            month = "0" + month;

        var cur_day = year + "-" + month + "-" + actual_date;

        var hours = today.getHours()
        var minutes = today.getMinutes()
        var seconds = today.getSeconds();

        if (hours < 10)
            hours = "0" + hours;

        if (minutes < 10)
            minutes = "0" + minutes;

        if (seconds < 10)
            seconds = "0" + seconds;

        return cur_day + " " + hours + ":" + minutes + ":" + seconds;

    }

    //the mula check out functionasync
     function mula_check_out() {
            var dueDate,firstname,lastname,contact,requested_amount,account_number,client_name,names,narrative,RedirectUrl,WebhookUrl,group_member_id;
            
            //var  date_response = await due_date();
            dueDate = due_date();
            client_name=$("#client_name").val();
            names= client_name.split(" ");
            firstname=names[1];
            lastname=names[2];
            contact=$("#client_contact").val();
            requested_amount=$("#amount").val();
            account_no_id=$("#account_no_id").val();
            account_number=$("#account_no").val();
            narrative=$("#narrative").val();
            RedirectUrl="<?php echo base_url('u/savings');?>";
            WebhookUrl="<?php echo base_url('u/mula_payment/acknowledge_payment');?>";

            group_member_id=$("#group_member_id").val();

            const url ="<?php echo base_url('u/mula_payment/get_transactionID');?>";
            const encryptionURL = "<?php echo base_url('u/mula_payment/encryption');?>";
            var member_id =$("#member_id").val();
            var data={'member_id':member_id};
            
            // function generate_trnx_id() {
            //     return fetch(
            //         url,
            //         {
            //             method:'POST',
            //             body:JSON.stringify(data),
            //             mode:'cors'
            //         }
            //         ).then(response => response.json()).catch(error => console.log(error));
            // }
            // let response= await generate_trnx_id();
            // var merchant_transaction_id=response.merchant_transaction_id;
            // const merchantProperties = 
            //         {
            //             "customerFirstName":(group_member_id)?client_name:firstname,
            //             "customerLastName":(group_member_id)?client_name:lastname,
            //             "MSISDN":contact,
            //             "customerEmail":'ict@gmtconsults.com',
            //             "requestAmount":requested_amount,
            //             "currencyCode":'UGX',
            //             "serviceCode":"<?php //echo $payment_engine_requirements['service_code'] ?>",//'SAVDEV9343'
            //             "dueDate":dueDate,
            //             "countryCode":'UG',
            //             "languageCode":'en',
            //             "accountNumber":account_no_id,
            //             "requestDescription":narrative,
            //             "successRedirectUrl":RedirectUrl,
            //             "failRedirectUrl":RedirectUrl,
            //             "paymentWebhookUrl":WebhookUrl,
            //             "merchantTransactionID":merchant_transaction_id,
            //             "accountNoID":account_number,
            //             "accessKey": "<?php //echo $payment_engine_requirements['access_key'] ?>",//'$2a$08$oDgKu9jLJ5LE/J1IwkCiC.ueDOs2uT9BI7GhHrIjKaw1PpSZi96Ca'
            //             "countryCode" : 'UG'
            //         };
            // function encrypt() {
            //     return fetch(
            //         encryptionURL, 
            //         {
            //             method:'POST', 
            //             body:JSON.stringify(merchantProperties),
            //             mode:'cors'
            //         }).then(response => response.json()).catch(error => console.log(error));
            // }
            // encrypt().then(
            //     response => {
            //                     MulaCheckout.renderMulaCheckout({
            //                         checkoutType: "express",
            //                         merchantProperties: response,
            //                     });
            //                 }
            //             ).catch(error => console.log(error));
            $.ajax({
                url:url,
                data: {'member_id':member_id},
                type: 'POST',
                dataType:'json',
                success:function (response) {
                    var merchant_transaction_id=response.merchant_transaction_id;
                    const merchantProperties = 
                    {
                        "customerFirstName":(group_member_id)?client_name:firstname,
                        "customerLastName":(group_member_id)?client_name:lastname,
                        "MSISDN":contact,
                        "customerEmail":'ict@gmtconsults.com',
                        "requestAmount":requested_amount,
                        "currencyCode":'UGX',
                        "serviceCode":"<?php echo $payment_engine_requirements['service_code'] ?>",//'SAVDEV9343'
                        "dueDate":dueDate,
                        "countryCode":'UG',
                        "languageCode":'en',
                        "accountNumber":account_no_id,
                        "requestDescription":narrative,
                        "successRedirectUrl":RedirectUrl,
                        "failRedirectUrl":RedirectUrl,
                        "paymentWebhookUrl":WebhookUrl,
                        "merchantTransactionID":merchant_transaction_id,
                        "accountNoID":account_number,
                        "accessKey": "<?php echo $payment_engine_requirements['access_key'] ?>",//'$2a$08$oDgKu9jLJ5LE/J1IwkCiC.ueDOs2uT9BI7GhHrIjKaw1PpSZi96Ca'
                        "countryCode" : 'UG'
                    };

                    function encrypt() {
                        return fetch(
                            encryptionURL, 
                            {
                                method:'POST', 
                                body:JSON.stringify(merchantProperties),
                                mode:'cors'
                            }).then(response => response.json())
                    }
                    encrypt().then(
                        response => {
                                        MulaCheckout.renderMulaCheckout({
                                            checkoutType: "express",
                                            merchantProperties: response,
                                        });
                                    }
                                ).catch(error => console.log(error));
                }
            });
    }//end of mula_check_out function
</script>
