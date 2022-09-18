<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date   = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>

<style>
    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner-border {
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

    .spinner-border-sm {
        height: 1rem;
        border-width: .2em;
    }

    .printing {
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        z-index: 10;
        background-color: #000;
        opacity: 0.5;
        height: 100vh;
        width: 100vw;
        visibility: hidden;
        display: none;
    }

    .swal2-container {
        z-index: 2200;
    }
</style>

<div id="div_loan_installment_payments_print_out" style="display: none;"></div>

<div id="printing" class="printing text-white d-flex justify-content-center align-items-center m-auto">
    <p class="text-center">
        <span class="spinner-border spinner-border-sm mr-2" role="status" aria-hidden="true"></span>
        Printing...
    </p>
</div>
<div id="loan_schedule_modal_container">

</div>

<div class="py-4">
    <?php $this->view('client_loan/states/loan_reports_repayments/tab_view'); ?>
</div>


<?php $this->view('client_loan/states/active/multiple_installments_payment'); ?>

<script>
    var dTable = {};
    var response = '';
    var optionSet1 = {}
    var displayed_tab = '';
    var client_loanModel = {},
        TableManageButtons;
    $(document).ready(function() {

        $(".select2_demo_2").select2();
        $('form#formDeposit').validator().on('submit', saveData);
        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
        end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");

        <?php $this->load->view('client_loan/client_loan_knockout.php'); ?>
        var handleDataTableButtons = function(tabClicked) {
            <?php $this->view('client_loan/states/loan_reports_repayments/table_js'); ?>
        };
        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {
                    handleDataTableButtons(tblClicked);
                }
            };
        }();
        TableManageButtons.init("tab-loan_reports_repayments");
        //initializing the date range picker
        daterangepicker_initializer();
        //$this->view('includes/daterangepicker.php'); ?>


    });
    <?php $this->load->view('client_loan/client_loan_dtables_js.php'); ?>

    //Reloading a page after action
    function reload_data(form_id, response) {
        switch (form_id) {
            case "formClient_loan":
                TableManageButtons.init("tab-partial_application");
                if (typeof response.members != 'undefined') {
                    client_loanModel.member_names(null);
                    client_loanModel.member_names(response.members);
                    client_loanModel.group_loan_details(response.group_loan_details);
                }
                if (typeof response.loan_ref_no !== 'undefined') {
                    client_loanModel.loan_ref_no(response.loan_ref_no);
                }
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                break;
            case "formApprove":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);

                }
                if (typeof dTable['tblApproved_client_loan'] !== 'undefined') {
                    dTable['tblApproved_client_loan'].ajax.reload(null, false);

                }
                if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                    dTable['tblPartial_application_loan'].ajax.reload(null, false);

                }
                break;
            case "formReject":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                    dTable['tblPartial_application_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);
                }
                break;
            case "formCancle":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblRejected_loan'] !== 'undefined') {
                    dTable['tblRejected_loan'].ajax.reload(null, false);
                }
                break;
            case "formApplication_withdraw":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                    dTable['tblPartial_application_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblRejected_loan'] !== 'undefined') {
                    dTable['tblRejected_loan'].ajax.reload(null, false);
                }
                break;
            case "formForward_application":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof dTable['tblClient_loan'] !== 'undefined') {
                    dTable['tblClient_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblPartial_application_loan'] !== 'undefined') {
                    dTable['tblPartial_application_loan'].ajax.reload(null, false);
                }
                break;
            case "formActive":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                TableManageButtons.init("tab-active");
                break;
            case "formLock":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                dTable['tblActive_client_loan'].ajax.reload(null, false);
                break;
            case "formWrite_off":
                if (typeof response.state_totals != 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                dTable['tblIn_arrears_loans'].ajax.reload(null, false);
                break;
            case "formPay_off":
                if (typeof response.state_totals !== 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof response.installments !== 'undefined') {
                    client_loanModel.loan_installments(null);
                    client_loanModel.loan_installments(response.loan_installments);
                }
                dTable['tblActive_client_loan'].ajax.reload(null, false);
                break;
            case "formInstallment_payment":
                if (typeof response.state_totals !== 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof response.installments !== 'undefined') {
                    client_loanModel.loan_installments(null);
                    client_loanModel.loan_installments(response.loan_installments);
                }
                dTable['tblActive_client_loan'].ajax.reload(null, false);
                break;
            case "formReverse":
                if (typeof response.state_totals !== 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof dTable['tblRejected_loan'] !== 'undefined') {
                    dTable['tblRejected_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblCancled_loan'] !== 'undefined') {
                    dTable['tblCancled_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblWithdrawn_loan'] !== 'undefined') {
                    dTable['tblWithdrawn_loan'].ajax.reload(null, false);
                }
                if (typeof dTable['tblLocked_loans'] !== 'undefined') {
                    dTable['tblLocked_loans'].ajax.reload(null, false);
                }
                break;
            case "formReverse_approval":
                if (typeof response.state_totals !== 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                dTable['tblApproved_client_loan'].ajax.reload(null, false);
                break;
            case "formClient_loan1":
                if (typeof response.state_totals !== 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof response.active_loans !== 'undefined') {
                    client_loanModel.active_loans(null);
                    client_loanModel.active_loans(response.active_loans);
                }
                if (typeof response.members !== 'undefined') {
                    client_loanModel.member_names(null);
                    client_loanModel.member_names(response.members);
                    client_loanModel.group_loan_details(response.group_loan_details);
                }
                if (typeof response.loan_ref_no !== 'undefined') {
                    client_loanModel.loan_ref_no(response.loan_ref_no);
                }
                <?php if ($org['loan_app_stage'] == 0) { ?>
                    TableManageButtons.init("tab-pending_approval");
                <?php } elseif ($org['loan_app_stage'] == 1) { ?>
                    TableManageButtons.init("tab-approved");
                <?php } elseif ($org['loan_app_stage'] == 2) { ?>
                    TableManageButtons.init("tab-active");
                <?php } ?>
                break;
            case "formTopup_loan":
                if (typeof response.state_totals !== 'undefined') {
                    client_loanModel.state_totals(null);
                    client_loanModel.state_totals(response.state_totals);
                }
                if (typeof response.installments !== 'undefined') {
                    client_loanModel.loan_installments(null);
                    client_loanModel.loan_installments(response.loan_installments);
                }
                if (typeof response.members !== 'undefined') {
                    client_loanModel.member_names(null);
                    client_loanModel.member_names(response.members);
                    client_loanModel.group_loan_details(response.group_loan_details);
                }
                if (typeof response.loan_ref_no !== 'undefined') {
                    client_loanModel.loan_ref_no(response.loan_ref_no);
                }
                <?php if ($org['loan_app_stage'] == 0) { ?>
                    TableManageButtons.init("tab-pending_approval");
                <?php } elseif ($org['loan_app_stage'] == 1) { ?>
                    TableManageButtons.init("tab-approved");
                <?php } elseif ($org['loan_app_stage'] == 2) { ?>
                    TableManageButtons.init("tab-active");
                <?php } ?>
                break;
            default:
                window.location.reload();
                break;
        }
    }

    //getting payment schedule for a loan at application stage
    function get_payment_schedule(data) {
        var new_data = {};
        new_data['application_date1'] = typeof data.application_date === 'undefined' ? client_loanModel.application_date() : data.application_date;
        new_data['action_date1'] = typeof data.action_date === 'undefined' ? client_loanModel.app_action_date() : data.action_date;

        new_data['loan_product_id1'] = typeof client_loanModel.product_name() !== 'undefined' ? client_loanModel.product_name().id : client_loanModel.loan_details().loan_product_id;
        new_data['product_type_id1'] = typeof client_loanModel.product_name() !== 'undefined' ? client_loanModel.product_name().product_type_id : client_loanModel.loan_details().product_type_id;

        new_data['amount1'] = typeof data.amount === 'undefined' ?
            (
                (typeof client_loanModel.app_amount() != 'undefined') ?
                (
                    (typeof client_loanModel.selected_active_loan() != 'undefined') ? (parseFloat(client_loanModel.selected_active_loan().expected_principal) - parseFloat(client_loanModel.selected_active_loan().paid_principal)) + parseFloat(client_loanModel.app_amount()) : client_loanModel.app_amount()
                )

                :
                ((typeof client_loanModel.loan_details() != 'undefined') ?
                    (
                        (typeof client_loanModel.selected_active_loan() != 'undefined') ? (parseFloat(client_loanModel.selected_active_loan().expected_principal) - parseFloat(client_loanModel.selected_active_loan().paid_principal)) + parseFloat(client_loanModel.loan_details().requested_amount) : client_loanModel.loan_details().requested_amount
                    ) : ''

                )
            ) :
            data.amount;
        new_data['offset_period1'] = typeof data.offset_period === 'undefined' ? ((typeof client_loanModel.app_offset_period() != 'undefined') ? client_loanModel.app_offset_period() : ((typeof client_loanModel.loan_details() != 'undefined') ? client_loanModel.loan_details().offset_period : '')) : data.offset_period;
        new_data['offset_made_every1'] = typeof data.offset_made_every === 'undefined' ? ((typeof client_loanModel.app_offset_every() != 'undefined') ? client_loanModel.app_offset_every() : ((typeof client_loanModel.loan_details() != 'undefined') ? client_loanModel.loan_details().offset_made_every : '')) : data.offset_every;
        new_data['interest_rate1'] = typeof data.interest === 'undefined' ? ((typeof client_loanModel.app_interest() != 'undefined') ? client_loanModel.app_interest() : ((typeof client_loanModel.loan_details() != 'undefined') ? client_loanModel.loan_details().interest_rate : '')) : data.interest;
        new_data['repayment_made_every1'] = typeof data.repayment_made_every === 'undefined' ? ((typeof client_loanModel.app_repayment_made_every() != 'undefined') ? client_loanModel.app_repayment_made_every() : ((typeof client_loanModel.loan_details() != 'undefined') ? client_loanModel.loan_details().repayment_made_every : '')) : data.repayment_made_every;

        new_data['repayment_frequency1'] = typeof data.repayment_frequency === 'undefined' ? ((typeof client_loanModel.app_repayment_frequency() != 'undefined') ? client_loanModel.app_repayment_frequency() : ((typeof client_loanModel.loan_details() != 'undefined') ? client_loanModel.loan_details().repayment_frequency : '')) : data.repayment_frequency;

        new_data['installments1'] = typeof data.installments === 'undefined' ? ((typeof client_loanModel.app_installments() != 'undefined') ? client_loanModel.app_installments() : ((typeof client_loanModel.loan_details() != 'undefined') ? client_loanModel.loan_details().installments : '')) : data.installments;

        var url = "<?php echo site_url("client_loan/disbursement1"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            timeout: 3000,
            success: function(response) {
                console.log('here again1');

                //clear the the other fields because we are starting the selection afresh
                client_loanModel.payment_summation(null);
                client_loanModel.payment_schedule(null);
                client_loanModel.available_loan_fees(null);

                //populate the observables
                client_loanModel.payment_schedule(response.payment_schedule);
                client_loanModel.available_loan_fees(response.available_loan_fees);
                client_loanModel.payment_summation(response.payment_summation);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting new schedule
    function get_new_schedule(data, call_type) {
        var new_data = {};
        new_data['compute_interest_from_disbursement_date'] = client_loanModel.compute_interest_from_disbursement_date();


        if (call_type === 1) {
            new_data['action_date'] = typeof data.action_date === 'undefined' ? client_loanModel.action_date() : data.action_date;
            new_data['id'] = client_loanModel.loan_details().id;
            new_data['loan_product_id'] = typeof data.loan_product_id === 'undefined' ? client_loanModel.loan_details().loan_product_id : data.loan_product_id;
        } else {
            new_data['amount'] = typeof data.amount === 'undefined' ? (parseFloat(client_loanModel.amount) + (parseFloat(client_loanModel.disbursed_amount) - parseFloat(client_loanModel.parent_paid_principal))) : data.amount;
            new_data['loan_product_id'] = typeof data.loan_product_id === 'undefined' ? client_loanModel.product_name().id : data.loan_product_id;
            new_data['interest_rate'] = typeof data.interest_rate === 'undefined' ? client_loanModel.interest_rate() : data.interest_rate;
            new_data['repayment_made_every'] = typeof data.repayment_made_every === 'undefined' ? client_loanModel.repayment_made_every() : data.repayment_made_every;
            new_data['repayment_frequency'] = typeof data.repayment_frequency === 'undefined' ? client_loanModel.repayment_frequency() : data.repayment_frequency;
            new_data['installments'] = typeof data.installments === 'undefined' ? client_loanModel.installments() : data.installments;
            new_data['new_repayment_date'] = typeof data.new_repayment_date === 'undefined' ? client_loanModel.payment_date() : data.new_repayment_date;
        }

        var url = "<?php echo site_url("client_loan/disbursement"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                console.log('here again');

                //clear the the other fields because we are starting the selection afresh
                client_loanModel.payment_summation(null);
                client_loanModel.payment_schedule(null);
                client_loanModel.available_loan_fees(null);
                //populate the observables
                client_loanModel.payment_schedule(response.payment_schedule);
                client_loanModel.available_loan_fees(response.available_loan_fees);

                client_loanModel.payment_summation(response.payment_summation);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting payment data
    function get_payment_detail(new_data) {
        var url = "<?php echo site_url("loan_installment_payment/payment_data"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                client_loanModel.payment_data(response.payment_data);
                client_loanModel.penalty_amount(response.penalty_data);

                let next_installment = client_loanModel.loan_installments().find(val => {
                    if (client_loanModel.loan_installment()) {
                        return parseInt(val.installment_number) === parseInt(client_loanModel.loan_installment().installment_number) + 1;
                    }
                    return false;
                });


                if (next_installment) {
                    get_next_payment_detail({
                        ...new_data,
                        installment_number: next_installment.installment_number
                    });
                } else {
                    client_loanModel.next_payment_data(null);
                }

            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    function fetch_installments(client_loan_id) {
        var url = "<?php echo site_url("repayment_schedule/jsonList3"); ?>";
        var new_data = {
            payment_status: [2, 4],
            status_id: 1,
            client_loan_id: client_loan_id
        };
        // var data = 'payment_status <> 1 AND repayment_schedule.status_id=1 AND client_loan_id='+client_loan_id;

        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                client_loanModel.loan_installments(response.data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting payment data for next installment
    function get_next_payment_detail(new_data) {
        var url = "<?php echo site_url("loan_installment_payment/payment_data"); ?>";
        $.ajax({
            url: url,
            data: new_data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                //console.log('\n\n',response, '\n\n')
                client_loanModel.next_payment_data(response.payment_data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    //getting new penalty data
    function get_new_penalty(new_data) {
        var data = {};
        data['payment_date'] = new_data;
        data['state_id'] = client_loanModel.active_loan().state_id;
        data['client_loan_id'] = client_loanModel.payment_data() ? client_loanModel.payment_data().id : client_loanModel.active_loan().id;
        data['installment_number'] = client_loanModel.payment_data() ? client_loanModel.payment_data().installment_number : client_loanModel.loan_installments()[0].installment_number;
        var url = "<?php echo site_url("loan_installment_payment/get_penalty_data"); ?>";
        $.ajax({
            url: url,
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                //populate the observables
                client_loanModel.penalty_amount(response.penalty_data);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });
    }

    function handleDateRangePicker(startDate, endDate) {

        if (typeof displayed_tab !== 'undefined') {
            start_date = startDate;
            end_date = endDate;
            TableManageButtons.init(displayed_tab);
        }
    }

    let handlePrint_installment_payments = (client_loan_id, status_id) => {
        $('#printing').css('display', 'block').css('visibility', 'visible');
        $.ajax({
            url: '<?php echo site_url("client_loan/loan_installment_payments_statement"); ?>',
            data: {
                client_loan_id: client_loan_id,
                status_id: status_id
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#printing').css('display', 'none').css('visibility', 'hidden');
                //console.log(response);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                $('#printing').css('display', 'none').css('visibility', 'hidden');
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }

    let handlePrint_loan_schedule = (id, loan_no) => {
        //$('#printing').css('display', 'block').css('visibility', 'visible');
        $.ajax({
            url: '<?php echo site_url("loan_approval/pdf_disburse"); ?>',
            data: {
                client_loan_id: id,
                paper: 'A4',
                orientation: 'Landscape',
                stream: 1
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#loan_schedule_modal_container').html(response.pdf_data);
                $('#loan_schedule_modal').modal('show');
                //document.title = response.filename;
                //console.log(response);
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }
    let handlePrint_loan_payable_today = () => {
        let current_date = $('#current_date').val();
        $('#btn_print_loan_payable_today').css('display', 'none');
        $('#btn_printing_loan_payable_today').css('display', 'flex');
        $.ajax({
            url: '<?php echo site_url("client_loan/loan_payable_print_out"); ?>',
            data: {
                status_id: 1,
                current_date: current_date ? moment(current_date, 'DD-MM-YYYY').format('YYYY-MM-DD') : '',
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#btn_print_loan_payable_today').css('display', 'flex');
                $('#btn_printing_loan_payable_today').css('display', 'none');

                $('#div_loan_installment_payments_print_out').html(response.the_page_data);
                printJS({
                    printable: 'printable_loan_payable_today',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.sub_title
                });
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                $('#btn_print_loan_payable_today').css('display', 'flex');
                $('#btn_printing_loan_payable_today').css('display', 'none');
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }


    let handlePrint_loan_payments = () => {
        let start_date = $('#start_date').val();
        let end_date = $('#end_date').val();
        $('#btn_print_loan_payments').css('display', 'none');
        $('#btn_printing_loan_payments').css('display', 'flex');
        $.ajax({
            url: '<?php echo site_url("client_loan/loan_payments_print_out"); ?>',
            data: {
                status_id: 1,
                start_date: start_date ? moment(start_date, 'DD-MM-YYYY').format('YYYY-MM-DD') : '',
                end_date: end_date ? moment(end_date, 'DD-MM-YYYY').format('YYYY-MM-DD') : '',
            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#btn_print_loan_payments').css('display', 'flex');
                $('#btn_printing_loan_payments').css('display', 'none');

                $('#div_loan_installment_payments_print_out').html(response.the_page_data);
                printJS({
                    printable: 'printable_loan_installment_payments',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.sub_title
                });
            },
            fail: function(jqXHR, textStatus, errorThrown) {
                $('#btn_print_loan_payments').css('display', 'flex');
                $('#btn_printing_loan_payments').css('display', 'none');
                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            }
        });

    }

    const filter_by_date = () => {
        dTable['tblLoan_payments'].ajax.reload(null, true);
    }

    const filter_loan_fees_by_date = () => {
        dTable['tblApplied_loan_fee'].ajax.reload(null, true);
    }


    //const filter_pyables_by_date = () => {
        //console.log("am here")
        //dTable['tblPayable_today'].ajax.reload(null, true);
    //}

    $(document).ready(() => {
        $('#btn_printing_loan_payments').css('display', 'none');
        $('#btn_printing_loan_payable_today').css('display', 'none');

        $("#start_date").datepicker({
            onClose: function(selectedDate) {
                // Set the minDate of 'to' as the selectedDate of 'from'
                $("#end_date").datepicker("option", "minDate", selectedDate);
            }
        });

        $('#multiple_installment_payment-modal').on('hide.bs.modal', function(e) {
            // clear form 
        });
        $('#installment_payment-modal').on('hide.bs.modal', function(e) {
            // clear form
        });

        // Add select2 to Loan calculator client select
        $('#loan_calculator_member_id').select2({
            //dropdownParent: $('#loan_calculator-modal')
        });

     
    });
    const get_guarantors = () => {
        dTable['tblGuarantor'].ajax.reload(null, true);
      }
</script>