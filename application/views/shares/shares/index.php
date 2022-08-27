<?php $this->load->view('shares/shares_tab_data.php');  
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
    var gender,start_date,end_date,issuance_name,less_more_equal,num_limit,check_filter,start_date1,end_date1,start_date3,end_date3
    $(document).ready(function () {
     
      $('#share_summary_report_yr_m_filter').on('change', (e) => {
            
            let select_val = $('#share_summary_report_yr_m_filter').val();
            if(select_val === 'year') {
                sharesModel.month(false);
                sharesModel.year(true);
            }
            if(select_val === 'month') {
                sharesModel.year(false);
                sharesModel.month(true);
            }
        });
        

     start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
     end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
 
     $(".select2able").select2({
        allowClear: false
     });
     
       // $('form#formMake_acall').validate({submitHandler: saveData2});
       // $('form#formShares_application').validate({submitHandler: saveData2});

        <?php $this->load->view('shares/shares_knockout'); ?>
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
         <?php $this->load->view('shares/transaction/report/alert_setting_js'); ?>

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

        TableManageButtons.init("tab-share_active_accounts");

    });

    function reload_data(form_id, response) {
        switch (form_id) {
            case "formPending_shares":
                dTable['tblShares_Active'].ajax.reload(null, false);
                break;
            case "formBuy_shares":
                  dTable['tblShares_Active_Account'].ajax.reload(null,true);
                  break;
            case "formConvert_shares":
              dTable['tblShares_Active_Account'].ajax.reload(null,true);
                  break;
            case "formTransfer_share":
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

    const handlePrint_shares_report_pdf = () => {
        $('#btn_printing_shares_report').css('display', 'flex');
        $('#btn_print_active_report').css('display', 'none');
        $.ajax({
            url: '<?php echo site_url("share_transaction/shares_report_pdf_print_out"); ?>',
            data: {
                status_id: 1,
                state_id: 7, 
                transaction_status:sharesModel.transaction_status().id,
                start_date:sharesModel.start_date3,
                end_date:sharesModel.end_date3
                

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
     function filter_transaction_by_date() {
        dTable['tblShare_transaction'].ajax.reload(null, true);

    }

    function transaction_end_date_preview(){
        dTable['tblShares_Active_Account'].ajax.reload(null, true);
    }

  function set_active_select_value(that){
               var  start_date      = $("#start_date3").val();
               var  end_date        = $("#end_date3").val();
               var  gender          = $("#gender").val();
               var  issuance_id     = $("#issuance_id").val();
               var  num_limit       = $("#num_limit").val();
               var  less_more_equal = $("#less_more_equal").val();
               var transaction_status = $("#transaction_status").val();

            
              var url = "<?php echo site_url("shares/share_full_report");?>";

            $.ajax({
             url: url,
              data: {
                start_date:start_date,
                end_date:end_date,
                gender:gender,
                issuance_id:issuance_id,
                num_limit:num_limit,
                less_more_equal:less_more_equal,
                transaction_status:transaction_status
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                sharesModel.summary_data(response.summary_data);

                //}
                $('#gif').css('visibility', 'hidden');

            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
          dTable['tblShare_transaction_report'].ajax.reload(null, true);

        }
       
function get_shares_performace_data() {
        var shares = $('#shares').val();
        var membership = $('#membership').val();
        var start_date = $('#start_date1').val();
        var end_date = $('#end_date1').val();
        var category = $('#category').val();
        var month= sharesModel.month() ? 1 : 0;
        var year= sharesModel.year() ? 1 : 0;
        var period = $('#period').val();
        //var fiscal_1 = $('#fiscal_one').val();
        //var fiscal_2 = $('#fiscal_two').val();
        //var fiscal_3 = $('#fiscal_three').val();
        var transaction_status = $("#transaction_status").val();
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
                start_date: moment(start_date, 'DD-MM-YYYY').format('YYYY-MM-DD'),
                end_date: end_date_final,
                period: period,
                transaction_status:transaction_status
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
                //sharesModel.month(response.month);
                sharesModel.fiscal_1(response.fiscal_1);
                sharesModel.fiscal_2(response.fiscal_2);
                sharesModel.fiscal_3(response.fiscal_3);
                sharesModel.start_date1(response.start_date);
                sharesModel.end_date1(response.end_date);
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
                sharesModel.total_shares_amount(response.general_data.total_shares_amount);
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
                sharesModel.total_credit(response.general_data.total_credit);
                sharesModel.total_debit(response.general_data.total_debit);
                sharesModel.total_shares(response.general_data.total_shares);
                sharesModel.no_of_shares_sold(response.general_data.no_of_shares_sold);
 
                //}
                $('#gif').css('visibility', 'hidden');
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }
    //checks and unchecks the radio button trigger for the monthly and year report.
    
    $(document).on('pageinit', function () {
    $(document).on('change', '[type=radio]', function (e) {
        $('[type=radio]:checked').prop('checked', false).checkboxradio('refresh');
        $(this).prop('checked', true).checkboxradio('refresh');
    });
});
   
</script>
