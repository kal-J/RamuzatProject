if ($("#tbl_savings_report").length && tabClicked === "tab-savings_report") {
    if (typeof (dTable['tbl_savings_report']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-savings_report").addClass("active");
        dTable['tbl_savings_report'].ajax.reload(null, true);
    } else {
        dTable['tbl_savings_report'] = $('#tbl_savings_report').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "processing": true,
            "deferRender": true,
            responsive: true,
            "order": [[0, "asc"]],
            ajax: {
                "url": "<?php echo site_url('reports/report_savings_accounts') ?>",
                "dataType": "json",
                "type": "POST",
                "data": function (d) {
                    d.status_id = '1';
                    d.state_id = '7';
                    d.end_date = $("#end").val();
                    d.start_date = $("#start").val();
                    d.deposit_Product_id = $("#product_id").val();
                }
            },
            "columnDefs": [{
                "orderable": false,
                "searchable": false
            }],
            "footerCallback": function (tfoot, data, start, end, display) {
                var api = this.api();
                //display_footer_sum(api,[5]);
                let total_savings = 0;
                $.each(data, function (key, value) {
                    const savings = (value.real_bal) ? value.real_bal : 0;
                    total_savings += savings * 1;
                });
                $(api.column(5).footer()).html(curr_format(round(total_savings, 2)));
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
                {data: 'productname'},
                {
                    data: 'min_balance', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                {
                    data: 'locked_amount', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                {
                    data: 'real_bal', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
            ],

            buttons: <?php if (in_array('6', $report_privilege)) { ?> getBtnConfig('Savings Accounts Report'), <?php } else {
    echo "[],";
} ?>
responsive: true
});
}
}