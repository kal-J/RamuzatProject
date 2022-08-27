if ($("#tblSubscription_plan").length && tabClicked === "tab-social_fund") {
        if (typeof (dTable['tblSubscription_plan']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-subscription_plan").addClass("active");
            dTable['tblSubscription_plan'].ajax.reload(null, true);
        } else {
            dTable['tblSubscription_plan'] = $('#tblSubscription_plan').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                order: [[0, 'asc']],
                deferRender: true,
                ajax: {
                    "url": "<?php echo site_url('subscription_plan/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = '1';
                    }
                },
                "columnDefs": [{
                        "targets": [7],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'plan_name'},
                    {data: 'amount_payable', render: function (data, type, full, meta) { 
                            if (type === "sort" || type === "filter") {
                                return data;
                            }
                            return data?curr_format(data):0;
                        }
                    },
                    {data: 'repayment_frequency', render: function (data, type, full, meta) {
                            if (type === "sort" || type === "filter") {
                                return data;
                            }
                            return data + ' ' + full.made_every_name;
                        }
                    },
                    {data: 'repayment_start_option_name'},
                    {data: 'notes'},
                    {data: 'income_account'},
                    {data: 'income_receivable_account'},
                    {data: 'id', render: function (data, type, full, meta) {
                            var display_btn = "<div class='btn-grp'> ";
                            display_btn += <?php if (in_array('3', $subscription_privilege)) { ?>
                            " <a href='#add_subscription_plan-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update Subscription plan details'><i class='fa fa-pencil'></i></a>";
    <?php } ?>
                            display_btn += <?php if (in_array('4', $subscription_privilege)) { ?>
                            " <a href='#' class='btn btn-sm change_status' title='Deactivate Subscription plan'><i class='fa fa-ban'></i></a>";
    <?php } ?>
                            display_btn += "</div>";
                            return display_btn;
                        }
                    }
                ],
                "buttons": <?php if (in_array('6', $subscription_privilege)) { ?> getBtnConfig('Subscription plan'), <?php } else {
    echo "[],";
} ?>
                responsive: true
            });
        }
    }
