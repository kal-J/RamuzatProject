if ($("#tbl_shares_report").length && tabClicked === "tab-shares_report") {
    if (typeof (dTable['tbl_shares_report']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-shares_report").addClass("active");
        dTable['tbl_shares_report'].ajax.reload(null, true);
    } else {
        dTable['tbl_shares_report'] = $('#tbl_shares_report').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
            "processing": true,
            "deferRender": true,
            responsive: true,
            "order": [[0, "asc"]],
            ajax: {
                "url": "<?php echo site_url('reports/report_shares_accounts') ?>",
                "dataType": "json",
                "type": "POST",
                "data": function (d) {
                    d.status_id = '1';
                    d.state_id ='7';
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
                        var amount_page = api.column(5, {page: 'current'}).data().sum();
                        var amount_overall = api.column(5).data().sum();                        
                        $(api.column(5).footer()).html(curr_format(round(amount_overall,2)) );
                    },
            columns: [
                {
                    data: 'share_account_no', render: function (data, type, full, meta) {
                        return "<a href='<?php echo site_url('Shares/view'); ?>/" + full.id + "' title='View  share account details'>" + data + "</a>";
                    }
                },
                {data: 'salutation', render: function (data, type, full, meta) {
                        if(full.group_name) {
                            return full.group_name;
                        }
                            
                             return full.firstname+' '+full.lastname+' '+full.othernames;
                        }},
                {
                    data: "date_opened"
                    
                },
                {
                    data: 'price_per_share', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '';
                    }
                },
                {
                    data: 'num_of_shares', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '0';
                    }
                },
                {
                    data: 'total_amount', render: function (data, type, full, meta) {
                        return data ? curr_format(data * 1) : '0';
                    }
                },
            ],

            buttons: <?php if (in_array('6', $report_privilege)) { ?> getBtnConfig('Shares Accounts Report'), <?php } else {
    echo "[],";
} ?>
responsive: true
});
}
}