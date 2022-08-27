<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));

$data['module_list'] = $this->RolePrivilege_model->get_user_modules($this->session->userdata('staff_id'));
if (empty($data['module_list'])) {
    redirect('welcome/logout/4');
} else {
    $modules = array_column($data['module_list'], "module_id");
}


?>



<style>
    @keyframes spinner-border {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner-border {
        display: inline-block;
        width: 2rem;
        height: 2rem;
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

    #loading {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 2;
        background-color: rgba(0, 0, 0, 0.1);
        width: 100%;
    }
</style>

<div id="loading" class="row justify-content-center align-items-center m-1 text-primary">
    <span class="spinner-border"></span>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="ibox ">
            <div class="ibox-title">
                <ul class="breadcrumb">
                    <li><a href="<?php echo site_url("dashboard"); ?>">Dashboard</a></li>
                    <li><span style="font-weight:bold; color:gray;  font-size:14px;">
                            <?php echo $title; ?></span></li>
                </ul>
            </div>
            <div class="ibox-content">
                <!-- date range picker section -->
                <div class="row">
                    <div class="col-sm-8">
                        <!-- <label class="col-sm-3 col-form-label">Staff<span class="text-danger">*</span></label>
                            <div class="col-sm-4 form-group">
                            <select class="form-control" name="product_id" id="product_id" data-bind='options: products, optionsText: function(data){ return data.firstname+" "+data.lastname}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: product' style="width: 100%">
                            </select>
                        </div> -->
                    </div>
                    <div class="col-12 d-flex justify-content-between mb-3">
                        <div class="">
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#summary_report_modal">
                                Print summary report <i class="fa fa-print"></i>
                            </button>
                        </div>

                        <div id="reportrange" class="reportrange">
                            <i class="fa fa-calendar"></i>
                            <span>January 01, 2019 - December 31, 2020</span> <b class="caret"></b>
                        </div>
                    </div>
                </div>
                <!-- end of date range picker section -->

                <div class="d-flex mx-3 align-items-center flex-row-reverse">
                    <span class=" text-muted" data-bind="text: 'From ' + moment($root.start_datev(),'MMM Do YYYY').format('MMMM Do YYYY') + '&nbsp;  to  &nbsp;' + moment($root.end_datev(),'MMM Do YYYY').format('MMMM Do YYYY')"></span>
                </div>

                <div class="row my-3 mx-3">

                    <?php if (in_array('4', $modules)) { ?>
                        <button type="button" class="btn btn-info my-2 mr-1">
                            Active Loans <span class="badge badge-light" data-bind="text: $root.active_loans_count()"></span>
                        </button>
                        <button type="button" class="btn btn-info my-2 mr-1">
                            Closed Loans <span class="badge badge-light" data-bind="text: $root.closed_loans_count()"></span>
                        </button>
                    <?php } ?>


                    <table class="table table-sm table-bordered" id="balancesheet" width="100%">
                        <tbody>
                            <?php if (in_array('4', $modules)) { ?>
                                <tr>
                                    <td>
                                        <h3>Expected Amounts</h3>
                                    </td>
                                </tr>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Loan Principal
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text:curr_format(round($root.expected_principal() , 2) )">0</span></h4>
                                    </td>
                                </tr>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Loan Interest
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format( round($root.expected_interest(), 2))">0</span></h4>
                                    </td>
                                </tr>

                                <tr class="table-primary">
                                    <th>Total Expected Amounts </th>
                                    <th> <span> <span data-bind="text:curr_format(round(parseFloat($root.expected_interest()) + parseFloat($root.expected_principal()) ,2))">0</span> </span> </th>
                                </tr>

                            <?php } ?>

                            <tr>
                                <td>
                                    <h3>Collected Amounts</h3>
                                </td>
                            </tr>

                            <?php if (in_array('4', $modules)) { ?>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Loan Principal
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.collected_principal() , 2) )">0</span></h4>
                                    </td>
                                </tr>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Loan Interest
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.collected_interest() , 2) )">0</span></h4>
                                    </td>
                                </tr>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Loan Penalty
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.collected_penalty() , 2) )">0</span></h4>
                                    </td>
                                </tr>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Loan Fees
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: $root.loan_charges() ? curr_format( round($root.loan_charges(), 2) ) : 0">0</span></h4>
                                    </td>
                                </tr>
                            <?php } ?>

                            <?php if (in_array('6', $modules)) { ?>

                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Savings Deposits
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format( round($root.savings_deposits(), 2) )">0</span></h4>
                                    </td>
                                </tr>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Savings Fees
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: $root.savings_charges() ? curr_format(round($root.savings_charges(),2)) : 0">0</span></h4>
                                    </td>
                                </tr>
                            <?php } ?>
                            <tr class="table-primary">
                                <th>Total Collected Amounts <span class="text-xs">(Excluding any other incomes not listed above)</span> </th>
                                <th> <span> <span data-bind="text: (() => {
                                    let loans = '<?php echo in_array('4', $modules); ?>';
                                    let savings = '<?php echo in_array('6', $modules); ?>';
                                    let savings_sum = 0;
                                    let loans_sum = 0;
                                    if(savings) savings_sum = (parseFloat($root.savings_deposits()) + parseFloat($root.savings_charges() ? $root.savings_charges() : 0));
                                    if(loans) loans_sum = (parseFloat($root.collected_principal()) + parseFloat($root.collected_interest()) + parseFloat($root.collected_penalty()) + parseFloat($root.loan_charges() ? $root.loan_charges() : 0));

                                    return curr_format(round(savings_sum + loans_sum, 2));

                                })()">0</span> </span> </th>

                            </tr>

                            <?php if (in_array('4', $modules)) { ?>

                                <tr>
                                    <td>
                                        <h3>Disbursed Amounts</h3>
                                    </td>
                                </tr>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Loan Principal
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.principal_disbursed(),2))">0</span></h4>
                                    </td>
                                </tr>
                            <?php } ?>

                            <tr>
                                <td>
                                    <h3>Withdrawn Amounts</h3>
                                </td>
                            </tr>
                            <?php if (in_array('6', $modules)) { ?>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Savings Withdrawn
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.savings_withdraws(),2))">0</span></h4>
                                    </td>
                                </tr>
                            <?php } ?>
                            <?php if (in_array('12', $modules)) { ?>
                                <tr style="background-color: #fafafc;">
                                    <td style="padding-left:40px; font-weight:bold;">
                                        Shares Withdrawn
                                    </td>
                                    <td>
                                        <h4 class="no-margins"><span style="font-weight:bold;" data-bind="text: curr_format(round($root.share_withdraws(),2))">0</span></h4>
                                    </td>
                                </tr>
                            <?php } ?>

                            <tr class="table-primary">
                                <th>Total Withdraws</th>
                                <th> <span> <span data-bind="text: (() => {
                                    let shares = '<?php echo in_array('12', $modules); ?>';
                                    let savings = '<?php echo in_array('6', $modules); ?>';
                                    let shares_w = 0;
                                    let savings_w = 0;
                                    if(shares) shares_w = parseFloat($root.share_withdraws());
                                    if(savings) savings_w = parseFloat($root.savings_withdraws());

                                    return curr_format(round(shares_w+savings_w, 2));

                                })()">0</span> </span> </th>
                            </tr>

                            <tr>
                                <td>
                                    <h3></h3>
                                </td>
                            </tr>

                            <tr class="table-primary">
                                <th>Total Expenses</th>
                                <th> <span> <span data-bind="text:curr_format(round(
                                    parseFloat($root.expenses())
                                    ,2))">0</span> </span> </th>
                            </tr>

                        </tbody>
                    </table>

                </div>


            </div>

        </div>
    </div>
</div>

<?php $this->load->view('summary_reports/print_out'); ?>

<script>
    var dTable = {};
    var summaryModel = {};
    var TableManageButtons = {};
    var displayed_tab = '';
    var start_date;
    var end_date;
    var drp;
    var product_id, status_id;
    $(document).ready(function() {

        start_date = moment('<?php echo $start_date; ?>', "DD-MM-YYYY");
        end_date = moment('<?php echo ($end_date > date("d-m-Y")) ? date("d-m-Y") : $end_date; ?>', "DD-MM-YYYY");

        updateData();
        var SummaryModel = function() {
            var self = this;

            ko.bindingHandlers.stopBinding = {
                init: function() {
                    return {
                        controlsDescendantBindings: true
                    };
                }
            };
            ko.virtualElements.allowedBindings.stopBinding = true;

            self.start_datev = ko.observable(start_date);
            self.end_datev = ko.observable(end_date);

            self.savings_withdraws = ko.observable(0);
            self.share_withdraws = ko.observable(0);
            self.savings_deposits = ko.observable(0);
            self.share_deposits = ko.observable(0);
            self.collected_principal = ko.observable(0);
            self.collected_interest = ko.observable(0);
            self.disbursed_principal = ko.observable(0);

            self.loan_penalty = ko.observable(0);
            self.collected_penalty = ko.observable(0);
            self.active_loans_count = ko.observable(0);
            self.closed_loans_count = ko.observable(0);
            self.loan_payments = ko.observable();
            self.bad_loans = ko.observable();
            //self.disbursed_loans = ko.observable(0);
            self.expected_principal = ko.observable(0);
            self.expected_interest = ko.observable(0);
            self.savings_charges = ko.observable();
            self.share_payments = ko.observable();
            self.share_transfer = ko.observable();
            self.share_charges = ko.observable();
            self.loan_charges = ko.observable();

            self.general_expenses = ko.observable(0);
            self.expenses = ko.observable(0);
            self.income = ko.observable(0);

            self.invoice_payments = ko.observable();
            self.mantanance_fees = ko.observable();
            self.subscription_membership = ko.observable();
            self.asset_transactions = ko.observable();
            self.paid_interests = ko.observable();

            self.products = ko.observableArray(<?php echo (isset($members)) ? json_encode($members) : ''; ?>);
            self.product = ko.observable();

            self.principal_disbursed = ko.observable(0);
            self.amount_paid = ko.observable();
            self.unpaid_penalty = ko.observable(0);


            self.updateData = function() {
                $("#loading").css('display', 'flex');
                return new Promise((resolve, reject) => {
                    let start_date5 = moment(self.start_datev(), 'MMM Do YYYY').format('YYYY-MM-DD');
                    let end_date5 = moment(self.end_datev(), 'MMM Do YYYY').format('YYYY-MM-DD');

                    console.log("start_date => ", start_date5);
                    console.log("end_date => ", end_date5);

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            start_date: start_date5,
                            end_date: end_date5,
                            //credit_officer_id: credit_officer_id,
                            //origin: "reports"
                        },
                        url: "<?php echo site_url('reports/get_daily_reports_data') ?>",
                        success: function(response) {

                            self.principal_disbursed(response.principal_disbursed);

                            self.expected_principal(response.expected_principal);

                            self.expected_interest(response.expected_interest);

                            self.collected_principal(response.collected_principal);

                            self.collected_interest(response.collected_interest);

                            self.collected_penalty(response.collected_penalty);

                            // self.disbursed_principal(response.disbursed_principal);

                            self.share_deposits(response.share_deposits);
                            self.savings_deposits(response.savings_deposits);
                            self.savings_withdraws(response.savings_withdraws);
                            self.share_withdraws(response.share_withdraws);

                            self.unpaid_penalty(response.penalty_total);
                            self.active_loans_count(response.active_loans_count);
                            self.closed_loans_count(response.closed_loans_count);

                            return resolve(true);

                        },
                        error: function() {
                            reject();
                        },
                        failure: () => {
                            reject();
                        }

                    })

                });


            };

            self.product.subscribe((data) => {
                updateData(data.id);

            });

            daterangepicker_initializer();

            self.updateData().then(() => {
                updateData2();
            }).catch(() => {
                updateData2();
            })

        };

        summaryModel = new SummaryModel();
        ko.applyBindings(summaryModel);

    });


    function handleDateRangePicker(startDate, endDate) {
        start_date = startDate;
        end_date = endDate;

        summaryModel.start_datev(moment(start_date, 'X').format('MMM Do YYYY'));
        summaryModel.end_datev(moment(end_date, 'X').format('MMM Do YYYY'));

        updateData();
    }

    let updateData = function(id) {

        if (summaryModel.updateData) {
            summaryModel.updateData().then(() => {
                updateData2(id);
            }).catch(() => {
                updateData2(id)
            })
        }

    };


    let updateData2 = (id) => {
        $("#loading").css('display', 'flex');

        product_id = id || '';
        start_date1 = moment(start_date, 'X').format('YYYY-MM-DD');
        end_date1 = moment(end_date, 'X').format('YYYY-MM-DD');

        $.ajax({
            "url": "<?php echo site_url('SummaryReports/jsonList') ?>",
            "dataType": "json",
            "type": "POST",
            "data": {
                start_date: start_date1,
                end_date: end_date1,
                //member_id: product_id,
                status_id: '1',
                fisc_date_from: start_date1,
                fisc_date_to: end_date1,
            },
            success: function(response) {
                summaryModel.loan_penalty(response.loan_penalty[0].credit_sum);
                summaryModel.bad_loans(response.bad_loans[0].credit_sum);
                summaryModel.loan_payments(response.loan_payments[0].credit_sum);
                summaryModel.savings_charges(response.savings_charges[0].credit_sum);
                summaryModel.share_payments(response.share_payments[0].credit_sum);
                summaryModel.share_transfer(response.share_transfer[0].credit_sum);
                summaryModel.share_charges(response.share_charges[0].credit_sum);
                summaryModel.loan_charges(response.loan_charges[0].credit_sum);

                summaryModel.general_expenses(response.general_expenses[0].credit_sum);
                summaryModel.expenses(response.profitloss_sums.total_expense);
                summaryModel.income(response.profitloss_sums.total_income);

                summaryModel.invoice_payments(response.invoice_payments[0].credit_sum);
                summaryModel.mantanance_fees(response.mantanance_fees[0].credit_sum);
                summaryModel.subscription_membership(response.subscription_membership[0].credit_sum);
                summaryModel.asset_transactions(response.asset_transactions[0].credit_sum);
                summaryModel.paid_interests(response.paid_interests[0].credit_sum);

                summaryModel.start_datev(moment(start_date, 'X').format('MMM Do YYYY'));
                summaryModel.end_datev(moment(end_date, 'X').format('MMM Do YYYY'));

                $("#loading").css('display', 'none');

            }
        })
    }
</script>