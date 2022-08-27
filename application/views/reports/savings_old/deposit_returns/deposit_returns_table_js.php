if ($("#tbl_deposit_return").length && tabClicked === "tab-deposit-returns") {
    if (typeof (dTable['tbl_deposit_return']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-report").addClass("active");
        dTable['tbl_deposit_return'].ajax.reload(null, true);
    } else {
        dTable['tbl_deposit_return'] = $('#tbl_deposit_return').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            "lengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]],
            "processing": true,
            "deferRender": true,
            responsive: true,
            "order": [[0, "asc"]],
            ajax: {
                    "url": "<?php  
                    if(isset($user['id'])){
                        echo site_url('Savings_account/jsonList_member');
                     }else if(isset($group['id'])){
                        echo site_url('Savings_account/jsonList_group');
                     }else{
                       echo site_url('Savings_account/jsonList');
                     }
                    ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                
                                e.state_id = '7';
                                e.balance_end_date = $('#balance_end_date').val();
                                e.producttype = $('#producttype').val();
                                <?php if(isset($user['id'])){ ?>
                                e.client_id = <?php echo $user['id'] ?>;
                                e.client_type = 1;  
                                <?php } ?>
                                <?php if(isset($group['id'])){ ?>
                                e.client_id = <?php echo $group['id'] ?>; 
                                e.client_type = 2; 
                                <?php } ?>
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