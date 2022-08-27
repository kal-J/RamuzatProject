<?php
$start_date = date('d-m-Y', strtotime($fiscal_year['start_date']));
$end_date = date('d-m-Y', strtotime($fiscal_year['end_date']));
?>
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
<div class="tabs-container">
<ul class="nav nav-tabs" role="tablist">
    <li><a class="nav-link active" data-toggle="tab" href="#tab-fixed_asset"><i class="fa fa-money"></i> Daily Reports</a></li>
</ul>
<div class="tab-content">

    <div class="wrapper wrapper-content tp-5">

        <!-- date range picker section -->
        <div class="row">
            <div class="col-sm-8">
                <label class="col-sm-3 col-form-label">Staff<span class="text-danger">*</span></label>
                <div class="col-sm-4 form-group">
                    <select class="form-control" name="product_id" id="product_id" data-bind='options: products, optionsText: function(data){ return data.firstname+" "+data.lastname}, optionsCaption: "---select---", optionsAfterRender: setOptionValue("id"), value: product' style="width: 100%">
                    </select>
                </div>
            </div>
            <div class="col-sm-4">
                <div id="reportrange" class="reportrange pull-right">
                    <i class="fa fa-calendar"></i>
                    <span>January 01, 2019 - December 31, 2020</span> <b class="caret"></b>
                </div>
            </div>
        </div>
        <!-- end of date range picker section -->
<div class="animated fadeInRightBig">
    <!-- // start content here  -->
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>
                        Savings transactions summary
                        <!-- ko if: start_datev()!=end_datev() -->
                        <span style="float:right;" class="badge badge-success" data-bind="text:start_datev() +'   -   '+ end_datev()"></span>
                        <!-- /ko -->
                        <!-- ko if: start_datev()==end_datev() -->
                        <span style="float:right;" class="badge badge-success" data-bind="text:start_datev()"></span>
                        <!-- /ko -->
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tblSavings_summary" width="100%">
                            <thead>
                                <tr>
                                    <th><b>Narrative</b> </th>
                                    <th><b>Amount</b></th>
                                    <th><b>Details</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Savings withdraws</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: savings_withdraws() <= 0 ? 'red' : '#007bff'}, text:savings_withdraws()?curr_format(round(savings_withdraws(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/8") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Savings Deposits</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: savings_deposits() <= 0 ? 'red' : '#007bff'}, text:savings_deposits()?curr_format(round(savings_deposits(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/7") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Savings Charges/Fees</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: savings_charges() <= 0 ? 'red' : '#007bff'}, text:savings_charges()?curr_format(round(savings_charges(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/9-10-20") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>
                        Shares transactions summary
                        <!-- ko if: start_datev()!=end_datev() -->
                        <span class="badge badge-success" style="float:right;" data-bind="text:start_datev() +'   -   '+ end_datev()"></span>
                        <!-- /ko -->
                        <!-- ko if: start_datev()==end_datev() -->
                        <span style="float:right;" class="badge badge-success" data-bind="text:start_datev()"></span>
                        <!-- /ko -->
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
            <table class="table table-hover" id="tblShares_summary" width="100%">
                <thead>
                    <tr>
                        <th><b>Narrative</b> </th>
                        <th><b>Amount</b></th>
                        <th><b>Details</b></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Share Bought</td>
                        <td>
                            <span class="font-bold m-t block" data-bind="style: { color: share_payments() <= 0 ? 'red' : '#007bff'}, text:share_payments()?curr_format(round(share_payments(),2)*1):0">
                            </span>
                        </td>
                        <td>
                            <form method='post' action="<?php echo site_url("SummaryReports/view/22") ?>">
                                <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>Share Sold</td>
                        <td>
                            <span class="font-bold m-t block" data-bind="style: { color: share_transfer() <= 0 ? 'red' : '#007bff'},text:share_transfer()?curr_format(round(share_transfer(),2)*1):0">
                            </span>
                        </td>
                        <td>
                            <form method='post' action="<?php echo site_url("SummaryReports/view/24") ?>">
                                <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>Share Charges/Fees</td>
                        <td>
                            <span class="font-bold m-t block" data-bind="style: { color: share_charges() <= 0 ? 'red' : '#007bff'},text:share_charges()?curr_format(round(share_charges(),2)*1):0">
                            </span>
                        </td>
                        <td>
                            <form method='post' action="<?php echo site_url("SummaryReports/view/32-33") ?>">
                                <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 col-md-6 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>
                        Loans transactions summary
                        <!-- ko if: start_datev()!=end_datev() -->
                        <span style="float:right;" class="badge badge-success" data-bind="text:start_datev() +'   -   '+ end_datev()"></span>
                        <!-- /ko -->
                        <!-- ko if: start_datev()==end_datev() -->
                        <span style="float:right;" class="badge badge-success" data-bind="text:start_datev()"></span>
                        <!-- /ko -->
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
    <table class="table table-hover" id="tblLoans_summary" style="width: 100%">
        <thead>
            <tr>
                <th><b>Narrative</b> </th>
                <th><b>Amount</b></th>
                <th><b>Details</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Loan re-payments</td>
                <td>
                    <span class="font-bold m-t block" data-bind="style: { color: loan_payments() <= 0 ? 'red' : '#007bff'}, text:loan_payments()?curr_format(round(loan_payments(),2)*1):0">
                    </span>
                </td>
                <td>
                    <form method='post' action="<?php echo site_url("SummaryReports/view/6") ?>">
                        <input type='hidden' name='start_date' data-bind="value: start_datev()">
                        <input type='hidden' name='end_date' data-bind="value: end_datev()">
                        <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Disbursted loans</td>
                <td>
                    <span class="font-bold m-t block" data-bind="style: { color: disbursted_loans() <= 0 ? 'red' : '#007bff'}, text:disbursted_loans()?curr_format(round(disbursted_loans(),2)*1):0">
                    </span>
                </td>
                <td>
                    <form method='post' action="<?php echo site_url("SummaryReports/view/4") ?>">
                        <input type='hidden' name='start_date' data-bind="value: start_datev()">
                        <input type='hidden' name='end_date' data-bind="value: end_datev()">
                        <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Loan Penalty</td>
                <td>
                    <span class="font-bold m-t block" data-bind="style: { color: loan_penalty() <= 0 ? 'red' : '#007bff'}, text:loan_penalty()?curr_format(round(loan_penalty(),2)*1):0">
                    </span>
                </td>
                <td>
                    <form method='post' action="<?php echo site_url("SummaryReports/view/5") ?>">
                        <input type='hidden' name='start_date' data-bind="value: start_datev()">
                        <input type='hidden' name='end_date' data-bind="value: end_datev()">
                        <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Loan Charges/Fees</td>
                <td>
                    <span class="font-bold m-t block" data-bind="style: { color: loan_charges() <= 0 ? 'red' : '#007bff'}, text:loan_charges()?curr_format(round(loan_charges(),2)*1):0">
                    </span>
                </td>
                <td>
                    <form method='post' action="<?php echo site_url("SummaryReports/view/28") ?>">
                        <input type='hidden' name='start_date' data-bind="value: start_datev()">
                        <input type='hidden' name='end_date' data-bind="value: end_datev()">
                        <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                    </form>
                </td>
            </tr>
            <tr>
                <td>Paid interests</td>
                <td>
                    <span class="font-bold m-t block" data-bind="style: { color: paid_interests() <= 0 ? 'red' : '#007bff'}, text:paid_interests()?curr_format(round(paid_interests(),2)*1):0">
                    </span>
                </td>
                <td>
                    <form method='post' action="<?php echo site_url("SummaryReports/view/30-31") ?>">
                        <input type='hidden' name='start_date' data-bind="value: start_datev()">
                        <input type='hidden' name='end_date' data-bind="value: end_datev()">
                        <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xs-12 col-md-6 col-lg-6">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3>
                        General transactions summary
                        <!-- ko if: start_datev()!=end_datev() -->
                        <span style="float:right;" class="badge badge-success" data-bind="text:start_datev() +'   -   '+ end_datev()"></span>
                        <!-- /ko -->
                        <!-- ko if: start_datev()==end_datev() -->
                        <span style="float:right;" class="badge badge-success" data-bind="text:start_datev()"></span>
                        <!-- /ko -->
                    </h3>


                </div>
                <div class="panel-body" id="">
                    <div class="table-responsive">
                        <table class="table table-hover" id="tblGeneral_summary" style="width: 100%">
                            <thead>
                                <tr>
                                    <th><b>Narrative</b> </th>
                                    <th><b>Amount</b></th>
                                    <th><b>Details</b></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>General Expenses</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: general_expenses() <= 0 ? 'red' : 'blue'}, text:general_expenses()?curr_format(round(general_expenses(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/2-3-15") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Invoice payments</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: invoice_payments() <= 0 ? 'red' : 'blue'}, text:invoice_payments()?curr_format(round(invoice_payments(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/16-17") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Mantenance Fees</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: mantanance_fees() <= 0 ? 'red' : 'blue'}, text:mantanance_fees()?curr_format(round(mantanance_fees(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/13") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Subscription Membership</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: subscription_membership() <= 0 ? 'red' : 'blue'}, text:subscription_membership()?curr_format(round(subscription_membership(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/11-12") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                                <tr>
                                    <td>Asset transactions</td>
                                    <td>
                                        <span class="font-bold m-t block" data-bind="style: { color: asset_transactions() <= 0 ? 'red' : 'blue'}, text:asset_transactions()?curr_format(round(asset_transactions(),2)*1):0">
                                        </span>
                                    </td>
                                    <td>
                                        <form method='post' action="<?php echo site_url("SummaryReports/view/29-34") ?>">
                                            <input type='hidden' name='start_date' data-bind="value: start_datev()">
                                            <input type='hidden' name='end_date' data-bind="value: end_datev()">
                                            <a class="nav-link" style="color: green;" onclick="this.closest('form').submit();return false;"><i class="fa fa-eye"></i> View</a>
                                        </form>
                                    </td>
                                </tr>
                    </div>
                   
                </div>
            </div>
        </div>
        <!-- // end content here -->
    </div>
</div>
</div>
</div>
</div>
</div>
</div>
</div>
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

                self.start_datev = ko.observable();
                self.end_datev = ko.observable();

                self.savings_withdraws = ko.observable();
                self.savings_deposits = ko.observable();
                self.loan_penalty = ko.observable();
                self.loan_payments = ko.observable();
                self.bad_loans = ko.observable();
                self.disbursted_loans = ko.observable();
                self.savings_charges = ko.observable();
                self.share_payments = ko.observable();
                self.share_transfer = ko.observable();
                self.share_charges = ko.observable();
                self.loan_charges = ko.observable();
                self.general_expenses = ko.observable();
                self.invoice_payments = ko.observable();
                self.mantanance_fees = ko.observable();
                self.subscription_membership = ko.observable();
                self.asset_transactions = ko.observable();
                self.paid_interests = ko.observable();

                self.products = ko.observableArray(<?php echo (isset($members)) ? json_encode($members) : ''; ?>);
                self.product = ko.observable();
                self.product.subscribe((data) => {
                    updateData(data.id);

                });


                daterangepicker_initializer();

            };

            summaryModel = new SummaryModel();
            ko.applyBindings(summaryModel);

        });


        function handleDateRangePicker(startDate, endDate) {
            start_date = startDate;
            end_date = endDate;

            updateData();
        }

        let updateData = function(id) {

            product_id = id || '';

            $.ajax({
                "url": "<?php echo site_url('SummaryReports/jsonList') ?>",
                "dataType": "json",
                "type": "POST",
                "data": {
                    start_date: moment(start_date, 'X').format('YYYY-MM-DD'),
                    end_date: moment(end_date, 'X').format('YYYY-MM-DD'),
                    member_id: product_id,
                    status_id: '1',
                },
                success: function(response) {
                    summaryModel.savings_withdraws(response.savings_withdraws[0].credit_sum);
                    summaryModel.savings_deposits(response.savings_deposits[0].credit_sum);
                    summaryModel.loan_penalty(response.loan_penalty[0].credit_sum);
                    summaryModel.disbursted_loans(response.disbursted_loans[0].credit_sum);
                    summaryModel.bad_loans(response.bad_loans[0].credit_sum);
                    summaryModel.loan_payments(response.loan_payments[0].credit_sum);
                    summaryModel.savings_charges(response.savings_charges[0].credit_sum);
                    summaryModel.share_payments(response.share_payments[0].credit_sum);
                    summaryModel.share_transfer(response.share_transfer[0].credit_sum);
                    summaryModel.share_charges(response.share_charges[0].credit_sum);
                    summaryModel.loan_charges(response.loan_charges[0].credit_sum);
                    summaryModel.general_expenses(response.general_expenses[0].credit_sum);
                    summaryModel.invoice_payments(response.invoice_payments[0].credit_sum);
                    summaryModel.mantanance_fees(response.mantanance_fees[0].credit_sum);
                    summaryModel.subscription_membership(response.subscription_membership[0].credit_sum);
                    summaryModel.asset_transactions(response.asset_transactions[0].credit_sum);
                    summaryModel.paid_interests(response.paid_interests[0].credit_sum);

                    summaryModel.start_datev(moment(start_date, 'X').format('MMM Do YYYY'));
                    summaryModel.end_datev(moment(end_date, 'X').format('MMM Do YYYY'));

                }
            })
        };
    </script>