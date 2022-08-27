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

<?php $this->load->view('savings_account/savings_tab_data.php'); ?>

<div id="active_savings_accounts_print_out" style="display: none;"></div>
<div id="pending_savings_accounts_print_out" style="display: none;"></div>
<div id="savings_accounts_transaction_print_out" style="display: none;"></div>

<script>
    var dTable = {};
    var savingsModel = {};
    var TableManageButtons = {};
    $(document).ready(function () {
        $(".select2able").select2({
            allowClear: true
        });
        $("#to_account_no_select").select2({
            dropDownParent: $("#transfer")
        });

       <?php $this->load->view('savings_account/savings_knockout.php'); ?>
        var handleDataTableButtons = function (tabClicked) {
        <?php $this->load->view('savings_account/states/active/savings_account_js.php'); ?>
        <?php $this->load->view('savings_account/fixed/fixed_accounts_js'); ?>
        <?php $this->load->view('savings_account/transaction/transaction_js'); ?>
        <?php $this->load->view('savings_account/transaction/transaction_log_js'); ?>
        <?php $this->load->view('savings_account/shares/membershares_js'); ?>
        <?php $this->load->view('savings_account/states/pending/savings_account_pending_js.php'); ?>
        <?php $this->load->view('savings_account/states/inactive/savings_account_inactive_js.php'); ?>
        <?php $this->load->view('savings_account/states/suspended/savings_account_suspended_js.php'); ?>
        <?php $this->load->view('savings_account/states/deleted/savings_account_deleted_js.php'); ?>
        <?php $this->load->view('savings_account/deposit_withdraw_js.php'); ?>
        <?php $this->load->view('savings_account/requests/withdraw_requests_js.php'); ?>
        <?php $this->load->view('savings_account/requests/accepted/accepted_withdraw_js.php'); ?>
        <?php $this->load->view('savings_account/requests/declined/declined_withdraw_js.php'); ?>
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

        function draw_basic_bar_graph(chart_id,chart_title,tooltip,clients,s_amount){
            Highcharts.chart(chart_id, {
                   
            title: {
                text: chart_title
            },

            subtitle: {
                text: 'Showing clients total savings'
            },
            xAxis: {
                    type: 'category',
                    categories:clients,
                    labels: {
                        rotation: -45,
                        style: {
                            fontSize: '13px',
                            fontFamily: 'Verdana, sans-serif'
                        }
                    }
                },
                yAxis: {
                    min: 0,
                    title: {
                        text: 'Uganda Shillings'
                    }
                },
                legend: {
                    enabled: false
                },
                tooltip: {
                    pointFormat: tooltip
                },
           
            series: [{
                type: 'column',
                colorByPoint: false,
                data: s_amount,
                showInLegend: false
            }]
        });
        }

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

    function filter_data(){
        dTable['tblFixed_accounts'].ajax.reload(null, true);

    }


    function reload_data(form_id, response) {
        switch (form_id) {
            case "formSavings_account":                
                TableManageButtons.init("tab-savings_account_pending");
                if(typeof response.accounts !== 'undefined' && response.accounts != ''){
                   savingsModel.clients(response.accounts);
                }
                if(typeof response.new_account_no !== 'undefined'){
                    savingsModel.new_account_no(response.new_account_no);
                }
                if( typeof response.state_totals !== 'undefined'){
                    savingsModel.ac_state_totals(null);
                    savingsModel.ac_state_totals(response.state_totals );
                }
                break;
            case "formChange_state":                
                if( typeof response.state_totals !== 'undefined'  && response.state_totals !== ''){
                    savingsModel.ac_state_totals(null);
                    savingsModel.ac_state_totals(response.state_totals );
                }
                dTable['tblSavings_account'].ajax.reload(null, true);
                dTable['tblSavings_account_pending'].ajax.reload(null, true);
                break;
            case "formWithdraw":
            case "formTransfer":
            case "formDeposit":
               if (typeof response.insert_id !== 'undefined') {
                    window.location = "<?php echo site_url('transaction/print_receipt/'); ?>" + response.insert_id+ "/"+ response.client_type;
                }
                dTable['tblSavings_account'].ajax.reload(null, true);
                dTable['tblSavings_account_pending'].ajax.reload(null, true);
                savingsModel.clients(response.accounts);
                break;
            case "formTransaction":
            case "formReverseTransaction":
                dTable['tblTransaction'].ajax.reload(null, true);
                break;
            case "formBulk_deposit":
               if (typeof response.failed !== 'undefined') {
                 savingsModel.name_error(response.failed);
                } else{
                 savingsModel.name_error(0);
                dTable['tblSavings_account'].ajax.reload(null, true);
                //dTable['tblSavings_account_pending'].ajax.reload(null, true);
                savingsModel.clients(response.accounts);
                }
                break;
            default:formChange_state
                //nothing really to do here
                break;
        }
    }
    <?php $this->load->view('savings_account/deposits/function_js'); ?>

    const balance_end_date_preview = (e) => {
        e.preventDefault();
        e.stopPropagation();
        dTable['tblSavings_account'].ajax.reload(null, true);
    }

    const handlePrint_active_savings = () => {
        let balance_end_date = $('#balance_end_date').val();
        //console.log(balance_end_date);
        savingsModel.isPrinting_active(true);
        $.ajax({
            url: '<?php echo site_url("savings_account/active_savings_print_out"); ?>',
            data: {
                state_id: 7, 
                balance_end_date: balance_end_date,
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //console.log('\n\n SUCCESS', response)
                savingsModel.isPrinting_active(false);
                $('#active_savings_accounts_print_out').html(response.the_page_data);
                printJS({printable: 'printable_active_savings_account', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title})

                
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                savingsModel.isPrinting_active(false);
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }, 
            error:  function (err) {
                console.log('Error', err)
                savingsModel.isPrinting_active(false);
            }
        });
    }
    const handlePrint_pending_savings = () => {
        let balance_end_date = $('#balance_end_date').val();
        savingsModel.isPrinting_pending(true);
        $.ajax({
            url: '<?php echo site_url("savings_account/pending_savings_print_out"); ?>',
            data: {
                state_id: 5, 
                balance_end_date: $('#balance_end_date').val()
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                savingsModel.isPrinting_pending(false);
                $('#pending_savings_accounts_print_out').html(response.the_page_data);
                printJS({printable: 'printable_pending_savings_account', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title})
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                savingsModel.isPrinting_pending(false);
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error:  function (err) {
                //console.log('Error', err)
                savingsModel.isPrinting_pending(false);
            }
        });
    }
    const handlePrint_savings_transaction = () => {
        let balance_end_date = $('#balance_end_date').val();
        savingsModel.isPrinting_active(true);
        $.ajax({
            url: '<?php echo site_url("transaction/savings_transaction_print_out"); ?>',
            data: {
                status_id: 1, 
                balance_end_date: balance_end_date,
            },
            type: 'POST',
            dataType: 'json',
            success: function (response) {
                //console.log('\n\n SUCCESS', response)
                savingsModel.isPrinting_active(false);
                $('#savings_accounts_transaction_print_out').html(response.the_page_data);
                printJS({printable: 'printable_savings_transaction', type: 'html', targetStyles: ['*'], documentTitle: response.sub_title})

                
            },
            fail: function (jqXHR, textStatus, errorThrown) {
                savingsModel.isPrinting_active(false);
            console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }, 
            error:  function (err) {
                console.log('Error', err)
                savingsModel.isPrinting_active(false);
            }
        });
    }
    


    const filter_by_date = () => {
        dTable['tblTransaction'].ajax.reload(null, true);
    }

    $(document).ready(() => {
        $('#balance_end_date').on('change', () => {
            $('#active-savings-excel-link').attr('href', `savings_account/export_excel/7/${$('#balance_end_date').val()}`);
        });
        // $('#fixed-savings-excel-link').attr('href', `fixed_savings/export_excel/${$('#fixed_balance_end_date').val()}`);
    });
</script>
