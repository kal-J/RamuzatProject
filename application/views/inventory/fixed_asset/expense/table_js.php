if ($("#tblAsset_expense").length && tabClicked === "tab-asset_expense") {
        if (typeof (dTable['tblAsset_expense']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-asset_expense").addClass("active");
            dTable['tblAsset_expense'].ajax.reload(null, true);
        } else {
            dTable['tblAsset_expense'] = $('#tblAsset_expense').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                ajax: {
                    "url": "<?php echo site_url('Asset_expense/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = 1;
                        d.asset_id =<?php echo $fixed_asset['id']; ?>;
                    }
                },
                "columnDefs": [{
                        "targets": [6],
                        "orderable": false,
                        "searchable": false
                    }],
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                    var total_page = api.column(4, {page: 'current'}).data();
                    var total_overall = api.column(4).data();
                    var total_page_amount = 0;
                    var total_overall_amount = 0;

                    $.each(total_page, function (key, val) {
                        total_page_amount += (val) ? (parseFloat(val)) : 0;
                    });
                    $.each(total_overall, function (key, val) {
                        total_overall_amount += (val) ? (parseFloat(val)) : 0;
                    });

                    $(api.column(4).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                },
                columns: [
                    {data: 'transaction_no'},
                    {data: 'expense_type'},
                    {data: 'transaction_date', render: function (data, type, full, meta) {
                            return data ? moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY') : '';
                        }},
                   
                    {data: 'transaction_type_id', render: function (data, type, full, meta) {
                            return (parseInt(data)===1)?"CREDIT":"DEBIT";
                        }},
                    {data: 'amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '0';
                        }},
                    {data: 'payment_mode'},
                    {data: 'account_name', render: function (data, type, full, meta) {
                    var ret_txt = '<a href="<?php echo site_url("accounts/view"); ?>/'+full.fund_source_account_id+'" title="View account transactions">'+"["+full.account_code+ "]  "+data+'</a>';
                            return ret_txt;
                        }
                    },
                    {data: 'exp_account_name', render: function (data, type, full, meta) {
                    var ret_txt = '<a href="<?php echo site_url("accounts/view"); ?>/'+full.expense_account_id+'" title="View account transactions">'+"["+full.exp_account_code+ "]  "+data+'</a>';
                            return ret_txt;
                        }
                    },
                    {data: 'narrative'},
                    {"data": 'id', render: function (data, type, full, meta) {
                       var ret_txt = "";
                            ret_txt += ' &nbsp;<a href="#edit_transaction_expense-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil-square-o text-warning"></i></a>';
                            ret_txt += ' &nbsp;<a href="#" data-toggle="modal" title=" Reverse Transaction" class="btn btn-sm btn-warning "><i class="fa fa-arrow-circle-o-left text-danger"></i></a>';
                        return ret_txt;
                        }
                    }
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('<?php echo $fixed_asset['asset_name']; ?> EXPENSE DETAILS'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
        }
}
