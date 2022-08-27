if ($("#tbl_periodic_report").length && tabClicked === "tab-periodic") {
    if (typeof (dTable['tbl_periodic_report']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-periodic").addClass("active");
        dTable['tbl_periodic_report'].ajax.reload(null, true);
    } else {
        dTable['tbl_periodic_report'] = $('#tbl_periodic_report').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
            "processing": true,
            "deferRender": true,
            responsive: true,
            "order": [[0, "asc"]],
            ajax: {
                "url": "<?php echo site_url('reports/savings_accounts_periodic_reports') ?>",
                "dataType": "json",
                "type": "POST",
                "data": function (d) {
                    d.status_id = '1';
                    d.deposit = $("#deposit").val();
                    d.start_date = $("#min").val();
                    d.end_date = $("#max").val();
                }
            },
            "columnDefs": [{
                "orderable": false,
                "searchable": false
            }],
            "footerCallback": function (tfoot, data, start, end, display) {
                var api = this.api();
//display_footer_sum(api,[4,5,6,7]);
                let total_savings = 0;
                let total_withdraws = 0;
                let total_deposits = 0;
                let total_payments = 0;
                let total_transfers = 0;
                let total_charges = 0;
                $.each(data, function (key, value) {
                    const savings = (value.real_bal) ? value.real_bal : 0;
                    const t_deposits = (value.deposits) ? value.deposits : 0;
                    const t_withdraws = (value.withdraws) ? value.withdraws : 0;
                    const t_payments = (value.payments) ? value.payments : 0;
                    const t_transfers = (value.transfers) ? value.transfers : 0;
                    const t_charges = (value.charges) ? value.charges : 0;
                    total_savings += savings * 1;
                    total_deposits += t_deposits * 1;
                    total_withdraws += t_withdraws * 1;
                    total_payments += t_payments * 1;
                    total_transfers += t_transfers * 1;
                    total_charges += t_charges * 1;
                });
                $(api.column(2).footer()).html(curr_format(round(total_deposits, 2)));
                $(api.column(3).footer()).html(curr_format(round(total_withdraws, 2)));
                $(api.column(4).footer()).html(curr_format(round(total_transfers, 2)));
                $(api.column(5).footer()).html(curr_format(round(total_payments, 2)));
                $(api.column(6).footer()).html(curr_format(round(total_charges, 2)));
                $(api.column(7).footer()).html(curr_format(round(total_savings, 2)));
            },
            columns: [
                {
                    data: 'account_no', render: function (data, type, full, meta) {
                        return "<a href='<?php echo site_url('Savings_account/view'); ?>/" + full.id + "' title='View account details'>" + data + "</a>";
                    }
                },
                {
                    data: "member_name", render: function (data, type, full, meta) {
                        return "<a href='<?php echo site_url("member / member_personal_info"); ?>/" + full.member_id + "'>" + data + "</a>";
                    }
                },
                {
                    data: 'deposits', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                {
                    data: 'withdraws', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                {
                    data: 'transfers', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                {
                    data: 'payments', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                {
                    data: 'charges', render: function (data, type, full, meta) {
                        return data ? curr_format(Number(full.charges) * 1) : '0';
                    }
                },
                {
                    data: 'real_bal', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
            ],

            buttons: <?php if (in_array('6', $report_privilege)) { ?> getBtnConfig('Savings Accounts Periodic Report'), <?php } else {
    echo "[],";
} ?>
responsive: true
});
}
}