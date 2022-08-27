<?php
if (!$has_changed_password) {
    $this->load->view('client/profile/password/client_password_modal');
}
?>

<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2> Hello, <?php echo $_SESSION['firstname'] . " " . $_SESSION['lastname']; ?></h2>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="#">Welcome</a>
            </li>
        </ol>
    </div>
    <div class="col-sm-8">

    </div>
</div>
<div id="div_client_bio_print_out" style="display: none;"></div>

<div class="wrapper wrapper-content">

    <div class="animated fadeInRightBig">
        <!-- // start content here  -->
        <div class="row">
            <div class="col-12 d-flex flex-row-reverse my-1">
                <button class="btn btn-sm btn-primary" data-toggle="modal" onclick="handlePrint_client_bio_data(event)">
                    <i class="fa fa-print fa-2x"></i>
                </button>
            </div>


            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3>Active Loans Status Report</h3>
                    </div>
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover dataTables-example margin bottom" id="tblActive_client_loan" style="width: 100%">
                                <thead>
                                    <tr>
                                        <th><b>Ref No#</b></th>
                                        <th><b>Paid Amount</b> </th>
                                        <th><b>Principal bal</b> </th>
                                        <th><b>Interest</b> </th>
                                        <!-- <th ><b>Penalties</b> </th> -->
                                        <th><b>Total Remaining bal</b> </th>
                                        <th><b>Next Pay Date</b></th>
                                        <th><b>Due Date</b></th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <?php if ($org['savings_shares'] == 1) { ?>
                <div class="col-lg-12">
                    <div id="bar_graph" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                </div>
            <?php }
            if (!empty($shares_module)) { ?>
                <div class="col-xs-12 col-sm-12 col-md-7 col-lg-7">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Shares</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped  table-hover" id="tblShares_Active_Account" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Share A/C NO</th>
                                            <th>Account Name</th>
                                            <th>Price Per Share (UGX)</th>
                                            <th>No for Shares</th>
                                            <th>Total Amount (UGX)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (!empty($savings_module)) { ?>
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Withdraw request(s)</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped  table-hover" id="tblWithdraw_request" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Account No</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php if (!empty($savings_module)) { ?>
                <div class="col-xs-12 col-sm-12 col-md-5 col-lg-5">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h3>Savings Account (s) balance</h3>
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped  table-hover" id="tblSavings_account" width="100%">
                                    <thead>
                                        <tr>
                                            <th>Account No</th>
                                            <th>Account Balance</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>




<?php
if (!$has_changed_password) {
?>
    <script type="text/javascript">
        $(document).ready(function() {
            $("#client-password-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
        })
    </script>
<?php
}
?>

<script type="text/javascript">
    var dTable = {};
    var TableManageButtons = {};

    $(document).ready(function() {

        // client set password
        $("#clientSetPassword").validate({
            rules: {
                password: {
                    minlength: 8
                },
                confirmpassword: {
                    minlength: 8,
                    equalTo: "#password"
                }
            },
            submitHandler: function(form) {
                $.ajax({
                        type: $(form).attr('method'),
                        url: $(form).attr('action'),
                        data: $(form).serialize(),
                        dataType: 'json'
                    })
                    .done(function(response) {
                        if (response.success == true) {
                            toastr.success(response.message, "Success");
                            $("#client-password-modal").modal('hide');
                        } else {
                            toastr.warning(response.message, "Failure!");
                        }
                    });
                return false;
            }
        });
        // end client set password

        TableManageButtons = function() {
            "use strict";
            return {
                init: function(tblClicked) {}
            };
        }();
        var ClientsModel = function() {
            var self = this;
            self.user_savings = ko.observableArray([]);
            self.user_loans = ko.observableArray([]);
            self.user_shares = ko.observableArray([]);

            var savings_data = ko.observableArray(<?php echo json_encode($savings_data); ?>);
            var clients = [];
            var s_amount = [];
            savings_data().forEach(function(v, i) {
                clients.push(v.name);
                s_amount.push(parseFloat(v.y));
            });
            <?php if ($org['savings_shares'] == 1) { ?>
                draw_basic_bar_graph("bar_graph", "Savings per Individual", "Remaining Balance: <b>{point.y:,.2f}</b>", clients, s_amount);
            <?php } ?>
        };

        clientsModel = new ClientsModel();
        ko.applyBindings(clientsModel);


        if ($("#tblSavings_account").length) {
            if (typeof(dTable['tblSavings_account']) !== 'undefined') {

                dTable['tblSavings_account'].ajax.reload(null, true);
            } else {
                dTable['tblSavings_account'] = $('#tblSavings_account').DataTable({

                    order: [
                        [1, 'asc']
                    ],
                    deferRender: true,
                    searching: false,
                    paging: false,
                    bInfo: false,
                    ajax: {
                        "url": "<?php
                                if (isset($user['id'])) {
                                    echo site_url('u/savings/jsonList_member');
                                } else if (isset($group['id'])) {
                                    echo site_url('u/savings/jsonList_group');
                                } else {
                                    echo site_url('u/savings/jsonList');
                                }
                                ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(e) {
                            e.client_id = <?php echo $_SESSION['member_id'] ?>;
                            e.client_type = 1;
                            <?php if (isset($group['id'])) { ?>
                                e.client_id = <?php echo $group['id'] ?>;
                                e.client_type = 2;
                            <?php } ?>
                        }
                    },
                    columns: [{
                            data: 'account_no',
                            render: function(data, type, full, meta) {
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                return "<a class='project-title' href='<?php echo site_url('u/savings/view'); ?>/" + full.id + "' title='View account details'>" + data + "</a>";
                            }
                        },

                        {
                            data: 'real_bal',
                            render: function(data, type, full, meta) {
                                return curr_format(data * 1);
                            }
                        },
                        {
                            data: 'id',
                            render: function(data, type, full, meta) {
                                var display_btn = "<div>";

                                display_btn += "<a class='btn btn-sm text-muted' href='<?php echo site_url('u/savings/view'); ?>/" + full.id + "' title='View account details'><i class='fa fa-eye'></i> View</a>";
                                display_btn += "</div>";
                                return display_btn;
                            }
                        }
                    ],
                    buttons: getBtnConfig('Saving Accounts'),
                    responsive: true
                });
            }
        }


        //Active client loan javascript 
        if ($('#tblActive_client_loan').length) {
            //reinitailizing daterange picker
            daterangepicker_initializer();
            if (typeof(dTable['tblActive_client_loan']) !== 'undefined') {
                dTable['tblActive_client_loan'].ajax.reload(null, true);
            } else {
                dTable['tblActive_client_loan'] = $('#tblActive_client_loan').DataTable({
                    order: [
                        [1, 'asc']
                    ],
                    deferRender: true,
                    searching: false,
                    paging: false,
                    bInfo: false,
                    "ajax": {
                        "url": "<?php echo site_url('u/loans/jsonList'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d) {
                            d.state_id = 7;
                            <?php if (isset($_SESSION['member_id'])) { ?>
                                d.client_id = <?php echo $_SESSION['member_id'] ?>;
                            <?php } ?>
                            <?php if (isset($group_id)) { ?>
                                d.group_id = <?php echo $group_id ?>;
                            <?php } ?>
                        }
                    },
                    rowCallback: function(row, data) {
                        if (data.unpaid_installments >= 1) {
                            $(row).addClass('text-danger');
                        }

                    },
                    "columns": [

                        {
                            data: 'loan_no',
                            render: function(data, type, full, meta) {
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                var link1 = "<a href='<?php echo site_url('u/loans/view'); ?>/" + full.group_loan_id + "/1' title='View this Loan details'>" + data + "</a>";
                                var link2 = "<a href='<?php echo site_url('u/loans/view'); ?>/" + full.id + "' title='View this Loan details'>" + data + "</a>";
                                return (full.member_name == null) ? link1 : link2;
                            }
                        },

                        {
                            data: "paid_amount",
                            render: function(data, type, full, meta) {
                                return "<span class='text-success' style='font-weight: bold;'>" + curr_format(data * 1) + "</span>";
                            }
                        },
                        {
                            data: "expected_principal",
                            render: function(data, type, full, meta) {
                                var principal_bal = (full.paid_principal) ? (parseFloat(data * 1) - parseFloat(full.paid_principal) * 1) : parseFloat(data * 1);
                                return "<span class='text-danger' style='font-weight: bold;'>" + curr_format(round(principal_bal, 2)) + "</span>";
                            }
                        },
                        {
                            data: "expected_interest",
                            render: function(data, type, full, meta) {
                                var interest_bal = (full.paid_interest) ? (parseFloat(data * 1) - parseFloat(full.paid_interest) * 1) : parseFloat(data * 1);
                                return "<span class='text-danger' style='font-weight: bold;'>" + curr_format(round(interest_bal, 2)) + "</span>";
                            }
                        },
                        //   { data: "expected_penalties" , render:function( data, type, full, meta ){
                        //       var penalty_bal = (data)?parseFloat(data*1):0;
                        // return "<span class='text-danger' style='font-weight: bold;'>"+ curr_format(penalty_bal) +"</span>";
                        //   } },
                        {
                            data: "expected_interest",
                            render: function(data, type, full, meta) {

                                var rem_bal = (full.paid_amount) ? curr_format(round(((parseFloat(full.expected_principal) + parseFloat(data)) - parseFloat(full.paid_amount)) * 1, 2)) : curr_format(round((parseFloat(full.expected_principal) + parseFloat(data)) * 1, 2));

                                return "<span class='text-danger' style='font-weight: bold;'>" + rem_bal + "</span>";
                            }
                        },
                        {
                            data: "next_pay_date",
                            render: function(data, type, full, meta) {
                                return (data) ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : 'None';;
                            }
                        },
                        {
                            data: "last_pay_date",
                            render: function(data, type, full, meta) {
                                return (data) ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : 'None';;
                            }
                        }
                    ]

                });
            }
        }

        if ($("#tblShares_Active_Account").length) {
            dTable['tblShares_Active_Account'] = $('#tblShares_Active_Account').DataTable({
                order: [
                    [1, 'asc']
                ],
                deferRender: true,
                searching: false,
                paging: false,
                bInfo: false,
                ajax: {
                    "url": "<?php echo site_url('u/shares/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(e) {
                        e.status_id = '1';
                        e.state_id = '7'; //Active 
                        <?php if (isset($_SESSION['member_id'])) { ?>
                            e.client_id = <?php echo $_SESSION['member_id']; ?>;
                        <?php } ?>
                    }

                },
                "columnDefs": [{
                    "targets": [3],
                    "orderable": false,
                    "searchable": false
                }],
                columns: [

                    {
                        data: 'share_account_no',
                        render: function(data, type, full, meta) {
                            return "<a href='<?php echo site_url('u/shares/view'); ?>/" + full.id + "' title='View share details'>" + data + "</a>";
                        }
                    },
                    {
                        data: 'salutation',
                        render: function(data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return "<a href='<?php echo site_url('u/profile'); ?>' title='View user profile'>" + data.salutation + ' ' + data.firstname + ' ' + data.lastname + ' ' + data.othernames + "</a>";
                            }
                            return "<a href='<?php echo site_url('u/profile'); ?>' title='View user profile'>" + full.salutation + ' ' + full.firstname + ' ' + full.lastname + ' ' + full.othernames + "</a>";
                        }
                    },

                    {
                        data: 'price_per_share',
                        render: function(data, type, full, meta) {
                            return (data) ? curr_format(data * 1) : 0;

                        }
                    },
                    {
                        data: 'total_amount',
                        render: function(data, type, full, meta) {
                            return round((parseFloat(data) / parseFloat(full.price_per_share)), 2);

                        }
                    },
                    {
                        data: 'total_amount',
                        render: function(data, type, full, meta) {
                            return (data) ? curr_format(data * 1) : 0;

                        }
                    }

                ],
                buttons: getBtnConfig('Active Shares Accounts'),
                responsive: true
            });
        }




        //withdraws
        if ($("#tblWithdraw_request").length) {
            if (typeof(dTable['tblWithdraw_request']) !== 'undefined') {
                dTable['tblWithdraw_request'].ajax.reload(null, true);
            } else {
                dTable['tblWithdraw_request'] = $('#tblWithdraw_request').DataTable({
                    order: [
                        [1, 'asc']
                    ],
                    deferRender: true,
                    searching: false,
                    paging: false,
                    bInfo: false,
                    ajax: {
                        "url": "<?php echo site_url('u/Withdraw_requests/get_memeber_withdraw_requestsToJson'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(e) {
                            e.client_id = <?php echo $_SESSION['member_id'] ?>;
                            e.client_type = 1;
                            <?php if (isset($group['id'])) { ?>
                                e.client_id = <?php echo $group['id'] ?>;
                                e.client_type = 2;
                            <?php } ?>
                        }
                    },
                    columns: [{
                            data: 'account_no',
                            render: function(data, type, full, meta) {
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                return "<a class='project-title' href='<?php echo site_url('u/savings/view'); ?>/" + full.account_no_id + "' title='View account details'>" + data + "</a>";
                            }
                        },

                        {
                            data: 'amount',
                            render: function(data, type, full, meta) {
                                return curr_format(data * 1);

                            }
                        },

                        {
                            data: 'status',
                            render: function(data, type, full, meta) {
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                if (data == 1) {
                                    return '<span class="badge badge-info">Pending <i class="fa  fa-clock-o"></i></span>'
                                }
                                if (data == 2) {
                                    return '<span class="badge bg-green">Approved <i class="fa fa-check-circle"></span>'
                                }

                                if (data == 3) {
                                    return '<span class="badge bg-red">Declined <i class="fa  fa-times"></span>'
                                }
                            }
                        },

                    ],
                    buttons: getBtnConfig('Withdraw request'),
                    responsive: true
                });
            }
        }

    });
    //end withdraws

    function draw_basic_bar_graph(chart_id, chart_title, tooltip, clients, s_amount) {
        Highcharts.chart(chart_id, {

            title: {
                text: chart_title
            },

            subtitle: {
                text: 'Showing clients total savings'
            },
            xAxis: {
                type: 'category',
                categories: clients,
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


    let handlePrint_client_bio_data = () => {
        $('#printable_client_bio_data').css('display', 'flex');
        $('#printable_client_bio_data').css('display', 'none');
        $.ajax({
            url: '<?php echo site_url("u/home/print_client_data"); ?>',
            data: {
                status_id: 1,
                id: <?php echo $_SESSION['member_id']; ?>


            },
            type: 'POST',
            dataType: 'json',
            success: function(response) {
                $('#div_client_bio_print_out').html(response.the_page_data);
                printJS({
                    printable: 'printable_client_bio_data',
                    type: 'html',
                    targetStyles: ['*'],
                    documentTitle: response.sub_title
                });

            },
            fail: function(jqXHR, textStatus, errorThrown) {

                console.log("Network error. Data could not be loaded." + errorThrown + " " + textStatus);
            },
            error: function(err) {
                console.log(`${err} has occurred`);

            }
        });


    }
</script>