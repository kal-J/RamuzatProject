<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = $fiscal_year['end_date'] <= date('Y-m-d') ? date('d-m-Y', strtotime($fiscal_year['end_date'])) : date('d-m-Y');
?>
<style>
    @keyframes spinner-border {
      to { transform: rotate(360deg); }
    } 
    .spinner-border{
        display: inline-block;
        width: 1rem;
        height: 1rem;
        vertical-align: text-bottom;
        border: .25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        -webkit-animation: spinner-border .75s linear infinite;
        animation: spinner-border .75s linear infinite;
    }
    .spinner-border-sm{
        height: 1rem;
        border-width: .2em;
    }
</style>

<div id="div_active_shares_print_out" style="display: none;"></div>
<div id="div_pending_shares_print_out" style="display: none;"></div>
<div id="div_in_active_shares_print_out" style="display: none;"></div>

<script>
    var dTable = {};
    var sharesModel = {};
    var TableManageButtons = {};
    var gender,start_date,end_date,issuance_name,less_more_equal,num_limit,check_filter,end_date1,start_date1;
    $(document).ready(function () {

     start_date = moment('<?php echo $start_date; ?>', 'DD-MM-YYYY').format('DD-MM-YYYY') ;
     end_date = moment('<?php echo $end_date; ?>','DD-MM-YYYY').format('DD-MM-YYYY') ;
    
     $(".select2able").select2({
        allowClear: false
     });
      self.period_types = ko.observable([{"id": 1, "period_name": "As At"}, {
                "id": 2,
                "period_name": "Date Range"
            }]);
      self.gender_label = ko.observable([{"id": 0, "title": "Female"}, {
                "id": 1,
                "title": "Male"
            }]);

     
       // $('form#formMake_acall').validate({submitHandler: saveData2});
        $('form#formBuy_shares').validate({submitHandler: saveData2});
        $('form#formConvert_shares').validate({submitHandler: saveData2});
        $('form#formShares_state').validate({submitHandler: saveData2});
        $('form#formRefund').validate({submitHandler: saveData2});
        $('form#formTransfer').validate({submitHandler: saveData2});
        $('form#formBulk_deposit').validate({submitHandler: saveData3});
        $('form#formReverseShare_transaction').validate({submitHandler: saveData2});
        $('.sharem_accounts').select2({dropdownParent: $("#transfer")});
        $('.select2able').select2({dropdownParent: $("#add_share_account-modal")});
        $('.select2able2').select2({dropdownParent: $("#bulk_deposit_template-modal")});
  
        $('form#formShares').validate({submitHandler: saveData2});
       // $('form#formShares_application').validate({submitHandler: saveData2});
        var SharesModel = function () {
            var self = this;
            // self.share_issuances = ko.observable(<?php //echo json_encode($share_issuances); ?>);
            self.share_issuance = ko.observableArray(<?php echo json_encode($share_issuances); ?>);
            self.issuance = ko.observable();
            self.share_categories= ko.observable(<?php echo json_encode($share_categories);?>);
        
            self.category_id = ko.observable();
            self.members = ko.observableArray(<?php echo json_encode($members); ?>);
            self.member = ko.observable();

            self.savings_accounts=ko.observableArray(null);
            self.savings_account = ko.observable(); 
            self.transaction_channel = ko.observableArray(<?php echo json_encode($tchannel); ?>);
            self.payment_modes = ko.observable(<?php echo (isset($payment_modes))?json_encode($payment_modes):'';?>);
            self.payment_mode = ko.observable();

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
            self.share_accounts=ko.observable();
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

            self.start_date = ko.observable(start_date);
            self.end_date = ko.observable(end_date);
           //share report summary data
            self.end_date1 = ko.observable("<?php echo date('d-m-Y')?>");
            self.start_date1 = ko.observable();
            self.period = ko.observable();
            self.category = ko.observable();
            self.month = ko.observable();
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
            self.num_account_created=ko.observable();
            self.num_account_created2=ko.observable();
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
             self.male_members = ko.observable(<?php echo json_encode($male_members);?>);
             self.female_members = ko.observable(<?php echo json_encode($female_members);?>);
             self.no_of_shareholders1= ko.observable(<?php  echo json_encode($share_accounts);?>);
             //self.total_credit_sum1= ko.observable(<?php  //echo json_encode($share_report);?>);
           
            self.inactive_accounts =ko.observable();
            self.deactivated_accounts=ko.observable();
            self.active_shares_accounts=ko.observable();
            self.total_credit_sum=ko.observable();
            self.total_debit_sum=ko.observable();
            self.no_trans_reversed=ko.observable();
            self.no_of_shares_transfered=ko.observable();
            self.gender_summary_data=ko.observable();
            self.no_of_shares_bought=ko.observable();
             self.start_date1 = ko.observable();
            self.end_date1 = ko.observable();
             self.fiscal_2 = ko.observable();
            self.fiscal_3 = ko.observable();
            self.fiscal_1 = ko.observable();
            self.start_date2 = ko.observable();
            self.end_date2 = ko.observable();
            self.overall_total_t1=ko.observable();
            self.overall_total_t2=ko.observable();
            self.total_shares_t2=ko.observable();
            self.year =ko.observable();
            //self.yearly_report =ko.observable();
          
           
             
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
            self.display_table = function (data, click_event) {
                TableManageButtons.init($(click_event.target).prop("hash").toString().replace("#", ""));
            };
            self.member.subscribe(function (new_id) {
                if (typeof new_id !== 'undefined' && new_id !== null) {
                    get_member_savings_account(new_id.id);
                    //get_product_details();
                }
            });
            self.share_account2.subscribe(function (new_id) {
                if (typeof new_id !== 'undefined' && new_id !== null) {
                    get_member_savings_account(new_id.member_id);
                    //get_product_details();
                }
            });

            
            
            self.share_call.subscribe(function (data) {
                var check = "buy_shares";
                if (typeof data.id !== 'undefined') {
                    update_share_calls(data.id,data.share_issuance_id, "<?php echo site_url("share_call/active_share_calls"); ?>");
                    //this.account_balance = ko.observable(data.cash_bal);
                }
            });
        };

        sharesModel = new SharesModel();
        ko.applyBindings(sharesModel);

        var handleDataTableButtons = function (tabClicked) {
        <?php $this->load->view('shares/share_account/states/pending/pending_js'); ?>
        <?php $this->load->view('shares/share_account/states/active/active_js'); ?>
        <?php $this->load->view('shares/share_account/states/inactive/inactive_js'); ?>
        <?php //$this->load->view('shares/share_applications/states/pending/pending_js');?>
        <?php //$this->load->view('shares/share_applications/states/active/active_js'); ?>
        <?php //$this->load->view('shares/share_applications/states/inactive/inactive_js'); ?>
        <?php //$this->load->view('shares/share_applications/payments/payments_js'); ?>
        <?php $this->load->view('shares/transaction/transaction_js'); ?>
        <?php $this->load->view('shares/transaction/transaction_log_js'); ?>
        <?php $this->load->view('shares/transaction/report/shares_report_js'); ?>
        <?php $this->load->view('shares/transaction/report/shares_performance_report_js'); ?>

        <?php //$this->load->view('shares/calls/call_js'); ?>

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

    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formPending_shares":
                dTable['tblShares_Active'].ajax.reload(null, false);
                break;
            case "formBuy_shares":
            case "formConvert_shares":
            case "formTransfer":
                dTable['tblShares_Active_Account'].ajax.reload(null, false);
                break;
            case "formDeposit":
                dTable['tblShares_Active'].ajax.reload(null, true);
                break; 
            case "formReverseShare_transaction":
            case "formReverseShare_transaction":
                dTable['tblShare_transaction'].ajax.reload(null, true);
                break; 
            case "formRefund":
                dTable['tblShares'].ajax.reload(null, true);
                break;
            case "formChange_state":
            sharesModel.share_details(response.share_details);
            break;
            case "formShares":
                dTable['tblShares_Active_Account'].ajax.reload(null, true);
                break;
            case "formShares_application":
            case "formShares_state":
                dTable['tblShares_Pending_application'].ajax.reload(null, true);
                break;
             case "formBulk_deposit":
               if (typeof response.failed !== 'undefined') {
                sharesModel.name_error(response.failed);
                } else{
                sharesModel.name_error(0);
                dTable['tblShare_transaction'].ajax.reload(null, true);
                //dTable['tblSavings_account_pending'].ajax.reload(null, true);
               sharesModel.clients(response.accounts);
                }
                break;
           default:formChange_state;
                //nothing really to do here
                break;
        }
    }

//getting member's savings account
    function get_member_savings_account(data_id) {
        var url = "<?php echo site_url("savings_account/jsonList2"); ?>";
        $.ajax({
            url: url,
            data: {client_id:data_id,state_id:7},
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
            data: {share_account_no_id: data.id,status_id:1,state_id:7},
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

    const handlePrint_active_shares = () => {
        $('#btn_printing_active_shares').css('display', 'flex');
        $('#btn_print_active_shares').css('display', 'none');
        $.ajax({
            url: '<?php echo site_url("shares/active_shares_pdf_print_out"); ?>',
            data: {
                status_id: 1,
                state_id: 7, 
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                $('#btn_printing_active_shares').css('display', 'none');
                $('#btn_print_active_shares').css('display', 'flex');

                $('#div_active_shares_print_out').html(response.the_page_data);
                printJS({printable: 'printable_active_shares', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title});
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                $('#btn_printing_active_shares').css('display', 'none');
                $('#btn_print_active_shares').css('display', 'flex');
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error:  function (err) {
                $('#btn_printing_active_shares').css('display', 'none');
                $('#btn_print_active_shares').css('display', 'flex');
            }
        });
    }

    const handlePrint_pending_shares = () => {
        $('#btn_printing_pending_shares').css('display', 'flex');
        $('#btn_print_pending_shares').css('display', 'none');
        $.ajax({
            url: '<?php echo site_url("shares/pending_shares_pdf_print_out"); ?>',
            data: {
                status_id: 1,
                state_id: 5, 
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //console.log(response);
                $('#btn_printing_pending_shares').css('display', 'none');
                $('#btn_print_pending_shares').css('display', 'flex');

                $('#div_pending_shares_print_out').html(response.the_page_data);
                printJS({printable: 'printable_pending_shares', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title});
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                $('#btn_printing_pending_shares').css('display', 'none');
                $('#btn_print_pending_shares').css('display', 'flex');
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error:  function (err) {
                $('#btn_printing_pending_shares').css('display', 'none');
                $('#btn_print_pending_shares').css('display', 'flex');
            }
        });
    }

    const handlePrint_inactive_shares = () => {
        $('#btn_printing_inactive_shares').css('display', 'flex');
        $('#btn_print_inactive_shares').css('display', 'none');
        $.ajax({
            url: '<?php echo site_url("shares/inactive_shares_pdf_print_out"); ?>',
            data: {
                status_id: 1,
                state_id: 19, 
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //console.log(response);
                $('#btn_printing_inactive_shares').css('display', 'none');
                $('#btn_print_inactive_shares').css('display', 'flex');

                $('#div_in_active_shares_print_out').html(response.the_page_data);
                printJS({printable: 'printable_in_active_shares', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title});
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                $('#btn_printing_inactive_shares').css('display', 'none');
                $('#btn_print_inactive_shares').css('display', 'flex');
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error:  function (err) {
                $('#btn_printing_inactive_shares').css('display', 'none');
                $('#btn_print_inactive_shares').css('display', 'flex');
            }
        });
    }

  function set_active_select_value() {

     
          gender = $("#gender").val() ? $("#gender").val():'';
          if($("#start_date").val()=='' && $("#gender").val()=='' && $("#issuance_name").val()=='' &&  $("#less_more_equal").val()==''){
             
           check_filter =0;
       
             }
           if($("#start_date").val() =='' && $("#gender").val()!=='' && $("#issuance_name").val()!=='' && $("#less_more_equal").val()!==''){

           start_date= "<?php echo date('d-m-Y')?>";
            check_filter =1;

          }
          else{
           
            start_date=$("#start_date").val();
            }
          end_date= $("#end_date").val();
          issuance_name= $("#issuance_name").val()? $("#issuance_name").val():'';
          num_limit=$("#num_limit").val() ? $("#num_limit").val():'';
          less_more_equal=$("#less_more_equal").val()?$("#less_more_equal").val():'';


        if (typeof dTable['tblShare_transaction_report'] != 'undefined' && check_filter !='0') {
            dTable['tblShare_transaction_report'].ajax.reload(null, true);
        }
        
        }
       
        function get_shares_performace_data(that) {
        var shares = $('#shares').val();
        var membership = $('#membership').val();
        var start_date = $('#start_date1').val();
        var end_date = $('#end_date1').val();
        var category = $('#category').val();
        var month= $('#month').val();
        var year= $('#year').val();
        var period = $('#period').val();
        var fiscal_1 = $('#fiscal_one').val();
        var fiscal_2 = $('#fiscal_two').val();
        var fiscal_3 = $('#fiscal_three').val();
        var date_at = $('#date_at').val();
         var end_date_final = (parseInt(period) === 1) ? moment(date_at, 'DD-MM-YYYY').format('YYYY-MM-DD') : moment(end_date, 'DD-MM-YYYY').format('YYYY-MM-DD');
        var url = "<?php echo site_url("Shares/performance_report_query"); ?>";
        $('#gif').css('visibility', 'visible');
        $.ajax({
            url: url,
            data: {
                membership: membership,
                shares: shares,
                category: category,
                year: year,
                month: month,
                end_date: end_date_final,
                start_date: moment(start_date, 'DD-MM-YYYY').format('YYYY-MM-DD'),
                period: period
                //fiscal_1: fiscal_1,
                //fiscal_2: fiscal_2,
                //fiscal_3: fiscal_3
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                sharesModel.selected_period(response.period);
                sharesModel.membership(response.membership);
                sharesModel.shares(response.shares);
                sharesModel.month(response.month);
                
                sharesModel.fiscal_1(response.fiscal_1);
                sharesModel.fiscal_2(response.fiscal_2);
                sharesModel.fiscal_3(response.fiscal_3);
                sharesModel.start_date(response.start_date);
                sharesModel.end_date(response.end_date);
                sharesModel.share_report(response.general_data.share_report);
                sharesModel.rowSpan_value(response.general_data.rowSpan_value);
                sharesModel.no_of_shareholders(response.general_data.share_accounts);
                sharesModel.inactive_accounts(response.general_data.inactive_accounts);
                sharesModel.deactivated_accounts(response.general_data.deactivated_accounts);
                sharesModel.total_credit_sum(response.general_data.total_credit_sum);
                sharesModel.total_debit_sum(response.general_data.total_debit_sum);
                sharesModel.no_of_shares_transfered(response.general_data.no_of_shares_transfered);
                sharesModel.active_shares_accounts(response.general_data.active_shares_accounts);
                sharesModel.gender_summary_data(response.general_data.gender_summary_data);
                sharesModel.no_of_shares_bought(response.general_data.no_of_shares_bought);
                sharesModel.no_trans_reversed(response.general_data.no_trans_reversed);
                sharesModel.overall_total_t1(response.general_data.overall_total_t1);
                sharesModel.overall_total_t2(response.general_data.overall_total_t2);
                sharesModel.total_shares_t2(response.general_data.total_shares_t2);
                sharesModel.monthly_report(response.general_data.monthly_report);
                sharesModel.previous_month(response.general_data.previous_month);
                sharesModel.male_members(response.general_data.male_members);
                sharesModel.female_members(response.general_data.female_members);
                sharesModel.share_price(response.general_data.monthly_report);
                sharesModel.issuance_name2(response.general_data.male_members);
                sharesModel.amount2(response.general_data.monthly_report);
 
                //}
                $('#gif').css('visibility', 'hidden');
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

   $('#month').on('change',function(){
    var month_selected = $('#month').val();
    $('#m_title').text(month_selected);
   });
 
</script>
