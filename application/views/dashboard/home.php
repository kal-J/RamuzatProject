<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = $fiscal_year['end_date'] <= date('Y-m-d') ? date('d-m-Y', strtotime($fiscal_year['end_date'])) : date('d-m-Y');

if (empty($fiscal_all)) { ?>
    <br>
    <div class="alert alert-danger">
        <center>
            <b>Please Set your Fiscal Year to continue </b>
            &nbsp; &nbsp;&nbsp;
            <a data-toggle="modal" href="#fiscal-modal" class="btn btn-sm btn-flat btn-success">
                Set Fiscal Year</a>
        </center>
    </div>
    <?php } else {
    if (empty($fiscal_active)) {
        $this->view('dashboard/activate_fiscal_year');
    } else {
        if (!empty($lock_month_access)) {
            if (empty($active_month)) {
    ?>
                <div class="panel panel-warning">
                    <div class="panel-heading">
                    <b>Please Activate / Add a Month within your Fiscal year to continue ...</b></div>
    <?php
                $this->view('setting/locked_month/tab_view');
                echo "</div>";
            } else {
                $this->view('dashboard/admin_dash');
            }
        } else {
            $this->view('dashboard/admin_dash');
        }
    }
}
$this->view('dashboard/add_fiscal_modal');
    ?>
    <?php $this->view('setting/locked_month/add_modal'); ?>

    <script type="text/javascript">
        var dTable = {};
        var TableManageButtons = {};
        var displayed_tab = '';
        var start_date, end_date;
        var dashModel = {};
        $(document).ready(function() {
            $('form#formFiscal_year').validator().on('submit', msaveData);
            $('form#formFiscal_year2').validator().on('submit', msaveData);
            $('form#formMember').validate({
                submitHandler: saveData2
            });

            $('form#formFiscal_month').validate({
                submitHandler: saveData2
            });
            $("#member_account").select2({
                dropdownParent: $("#add_transaction")
            });
            
             $('#introduced_by_id').select2({
            dropDownParent: $('#add_member-modal')       
            });
            start_date_ = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
            end_date_ = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");

            start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
            end_date = moment('<?php echo $end_date; ?>', "DD-MM-YYYY");
            var DashModel = function() {
                var self = this;

                ko.bindingHandlers.stopBinding = {
                    init: function() {
                        return {
                            controlsDescendantBindings: true
                        };
                    }
                };
                ko.virtualElements.allowedBindings.stopBinding = true;

                self.start_datev = ko.observable();
                self.end_datev = ko.observable();

                self.savings_sums = ko.observable();
                self.withdraw_sum = ko.observable();
                self.deposits_sum = ko.observable();
                self.share_sums = ko.observable();
                self.share_withdraw_sum = ko.observable();
                self.share_deposits_sum = ko.observable();
                self.projected_intrest_earnings = ko.observable();
                self.intrest_in_suspense = ko.observable();
                self.change_in_Portfolio = ko.observable();
                self.gross_loan_portfolio = ko.observable();
                self.principal_disbursed = ko.observable();
                self.amount_disbursed = ko.observable();
                self.projected_interest_amount = ko.observable();
                self.amount_paid = ko.observable();
                self.portfolio_at_risk = ko.observable();
                self.value_at_risk = ko.observable();
                self.extraordinary_writeoff = ko.observable();

                self.client_count_active = ko.observable();
                self.staff_count_active = ko.observable();
                self.client_count_inactive = ko.observable();
                self.staff_count_inactive = ko.observable();

                self.loan_count_active = ko.observable();
                self.unpaid_penalty = ko.observable();
                self.loan_count_writeoff = ko.observable();
                self.loan_count_pend_approval = ko.observable();
                self.loan_count_partial = ko.observable();
                self.loan_count_arrias = ko.observable();
                self.loan_count_approved = ko.observable();
                self.loan_count_locked = ko.observable();

                self.total_sms = ko.observable();
                self.loan_sms_total = ko.observable();
                self.savings_sms_total = ko.observable();

                self.marital_status_id = ko.observable();
                self.client_no = ko.observable("<?php echo $new_client_no; ?>");
                self.member_referral_status = ko.observable(<?php echo (isset($member_referral))?json_encode($member_referral):''; ?>);
                self.members = ko.observableArray(<?php echo (isset($sorted_users))?json_encode($sorted_users):''; ?>);
                self.member_referral =ko.observable();
                self.member = ko.observable();

                self.totalamount = ko.computed(function() {
                    total = 0;
                    if ((typeof self.loan_count_active() !== 'undefined') && (typeof self.loan_count_arrias() !== 'undefined')) {
                        total_count = parseInt(self.loan_count_active().loan_count) + parseInt(self.loan_count_arrias().loan_count);
                        total = parseFloat(self.gross_loan_portfolio()) / parseInt(total_count);
                    }

                    return total;
                });
                self.start_datev(moment(start_date, 'X').format('MMM Do YYYY'));
                self.end_datev(moment(end_date, 'X').format('MMM Do YYYY'));
                //fetch the dashboard data from server
                self.get_data = function() {
                    self.start_datev(moment(start_date, 'X').format('MMM Do YYYY'));
                    self.end_datev(moment(end_date, 'X').format('MMM Do YYYY'));
                    if (start_date_.diff(start_date) == 0 && end_date_.diff(end_date) == 0) {
                        data = {
                            end_date: moment(new Date()).format('YYYY-MM-DD'),
                            origin: "dashboard"
                        }
                    } else {
                        data = {
                            start_date: moment(start_date, 'X').format('YYYY-MM-DD'),
                            end_date: moment(end_date, 'X').format('YYYY-MM-DD'),
                            origin: "dashboard"
                        }
                    }
                    // {start_date: moment(start_date,'X').format('YYYY-MM-DD'), end_date: moment(end_date,'X').format('YYYY-MM-DD'), origin: "dashboard"}
                    $.ajax({
                        data: data,

                        url: '<?php echo site_url("dashboard/ajax_data"); ?>',
                        type: "post",
                        dataType: "json",
                        success: function(result) {
                            if (typeof result !== "undefined") {
                                self.client_count_active(result.client_count_active);
                                self.client_count_inactive(result.client_count_inactive);
                                self.loan_count_active(result.loan_count_active);
                                self.loan_count_writeoff(result.loan_count_writeoff);
                                self.loan_count_pend_approval(result.loan_count_pend_approval);
                                self.loan_count_partial(result.loan_count_partial);
                                self.loan_count_arrias(result.loan_count_arrias);
                                self.loan_count_approved(result.loan_count_approved);
                                self.loan_count_locked(result.loan_count_locked);
                                <?php if (in_array('6', $modules)) { ?>
                                    semi_cicrle(result.savings_dataset);
                                <?php } ?>
                                inverted_bar_graph(result.loans_dataset);


                            }
                        }
                    });
                };
                TableManageButtons = function() {
                    "use strict";
                    return {
                        init: function(tblClicked) {}
                    };
                }();
                daterangepicker_initializer();
                self.updateData = function() {
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            start_date: moment(start_date, 'X').format('YYYY-MM-DD'),
                            end_date: moment(end_date, 'X').format('YYYY-MM-DD'),
                            origin: "reports"
                        },
                        url: "<?php echo site_url('dashboard/get_indicators_data') ?>",
                        success: function(response) {
                            draw_line_chart("line_graph1", response.income_expense);

                            self.savings_sums(response.savings_sums);
                            self.deposits_sum(response.deposits_sum);
                            self.withdraw_sum(response.withdraw_sum);
                            self.share_sums(response.share_sums);
                            self.share_deposits_sum(response.share_deposits_sum);
                            self.share_withdraw_sum(response.share_withdraw_sum);
                            //    self.loan_sms_total(response.loan_sms_total['total_sms']);
                            //    self.savings_sms_total(response.savings_sms_total['total_sms']);
                            //    self.total_sms(response.total_sms['total_sms']);

                        }
                    })
                };
            };
            dashModel = new DashModel();
            ko.applyBindings(dashModel);
            dashModel.get_data();
            dashModel.updateData();

            //============ START SAVINGS KNOCKOUT AND FORM VALIDATIONS ================
            <?php if (in_array('6', $modules)) {
                $this->load->view('savings_account/savings_knockout.php');
            } ?>
            //============ END SAVINGS KNOCKOUT AND FORM VALIDATIONS ================
            <?php if (in_array('4', $modules)) {
                $this->load->view('client_loan/client_loan_knockout.php');
            } ?>
            <?php $this->view('dashboard/table_js'); ?>

        });

        setInterval(function() {
            dashModel.get_data();
            dashModel.updateData();
        }, 300000); /*Refresh every thirty seconds*/
        <?php $this->view('reports/highcharts_js'); ?>

        function inverted_bar_graph(sent_data) {

            Highcharts.chart('semi_circle2', {

                title: {
                    text: 'Loan statistics for all the products'
                },

                chart: {
                    inverted: true,
                    polar: false
                },
                subtitle: {
                    text: 'If a state is missing, then the state has no loan in it'
                },

                xAxis: {
                    categories: sent_data['xAxis']
                },

                series: [{
                    type: 'column',
                    colorByPoint: true,
                    data: sent_data['yAxis'],
                    showInLegend: false
                }]

            });

        }

        function semi_cicrle(sent_data) {
            Highcharts.chart('semi_circle1', {
                chart: {
                    plotBackgroundColor: null,
                    plotBorderWidth: 0,
                    plotShadow: false
                },
                title: {
                    text: 'Savings Data Comparison',
                    align: 'center',
                    verticalAlign: 'middle',
                    y: 90
                },
                tooltip: {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        dataLabels: {
                            enabled: true,
                            distance: -50,
                            style: {
                                fontWeight: 'bold',
                                color: 'white'
                            }
                        },
                        startAngle: -90,
                        endAngle: 90,
                        center: ['50%', '75%'],
                        size: '110%'
                    }
                },
                series: [{
                    type: 'pie',
                    name: 'Savings share',
                    innerSize: '50%',
                    data: [
                        ['Deposit', sent_data['deposits_percentage']],
                        ['Withdraw', sent_data['withdraw_percentage']]
                    ]
                }]
            });
        }

        function handleDateRangePicker(startDate, endDate) {
            if (typeof displayed_tab !== 'undefined') {

            }
            start_date = startDate;
            end_date = endDate;
            TableManageButtons.init(displayed_tab);
            dashModel.updateData();
            dashModel.get_data();
        }

        function msaveData(e) {
            if (e.isDefaultPrevented()) {
                // handle the invalid form...
                console.log('Please fill all the fields correctly');
            } else {
                // everything looks good!
                e.preventDefault();
                mysaveData(e.target);
            }
        } //End of the saveData function
        function mysaveData(form) {
            var $form = $(form); //fv = $form.data('formValidation'),
            enableDisableButton(form, true);
            var formData = new FormData($form[0]);
            var id = $form.attr('id');
            $.ajax({
                url: $form.attr('action'),
                type: 'POST',
                data: formData,
                async: false,
                cache: false,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(feedback) {
                    if (feedback.success) {
                        if (isNaN(parseInt($form.attr('id')))) {
                            $form[0].reset();
                            $modal = $form.parents('div.modal');
                            if ($modal.length) {
                                $($modal[0]).modal('hide');
                            }
                        }
                        setTimeout(function() {
                            var formId = $form.attr('id');
                            var tblId = formId.replace("form", "tbl");
                            if (typeof dTable !== 'undefined' && typeof dTable[tblId] !== 'undefined') {
                                dTable[tblId].ajax.reload((typeof consumeDtableData !== 'undefined') ? consumeDtableData : null, false);
                            }
                            if (typeof reload_data === "function") {
                                reload_data(formId, feedback);
                            }
                        }, 1000);
                        toastr.success(feedback.message, "Success");
                        location.reload();
                    } else {
                        toastr.warning(feedback.message, "Failure!");
                    }
                    enableDisableButton(form, false);
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    network_error(jqXHR, textStatus, errorThrown, form);
                }
            });
        } //End of the saveData2 function

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
                success: function(response) {
                    //clear the the other fields because we are starting the selection afresh
                    client_loanModel.payment_summation(null);
                    client_loanModel.payment_schedule(null);
                    client_loanModel.available_loan_fees(null);
                    //populate the observables
                    client_loanModel.payment_schedule(response.payment_schedule);
                    client_loanModel.payment_summation(response.payment_summation);

                    client_loanModel.available_loan_fees(response.available_loan_fees);
                },
                fail: function(jqXHR, textStatus, errorThrown) {
                    console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
        }


        //getting new schedule
        function get_new_schedule(data, call_type) {

            var new_data = {};
            if (call_type === 1) {
                console.log('here again');
                new_data['action_date'] = typeof data.action_date === 'undefined' ? client_loanModel.action_date() : data.action_date;
                new_data['id'] = client_loanModel.loan_details().id;
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
                    //clear the the other fields because we are starting the selection afresh
                    client_loanModel.payment_summation(null);
                    client_loanModel.payment_schedule(null);
                    client_loanModel.available_loan_fees(null);

                    //populate the observables
                    client_loanModel.payment_schedule(response.payment_schedule);
                    client_loanModel.payment_summation(response.payment_summation);

                    client_loanModel.available_loan_fees(response.available_loan_fees);
                },
                fail: function(jqXHR, textStatus, errorThrown) {
                    console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
                }
            });
        }

        function reload_data(form_id, response) {
            switch (form_id) {
                case "formClient_loan1":
                    dashModel.get_data();
                    if (typeof response.loan_ref_no !== 'undefined') {
                        client_loanModel.loan_ref_no(response.loan_ref_no);
                    }
                    break;
                case "formMember":
                    dashModel.get_data();
                    if (typeof response.client_no !== 'undefined') {
                        dashModel.client_no(response.client_no);
                    }
                    break;
                case "formDeposit":
                    dashModel.updateData();
                    break;
                default:
                    //nothing really to do here
                    break;
            }
        }
        <?php $this->load->view('savings_account/deposits/function_js'); ?>
    </script>