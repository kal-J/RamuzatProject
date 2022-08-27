//if ($("#tblAsset_payment").length && tabClicked === "tab-asset_payment") {
        if (typeof (dTable['tblAsset_payment']) !== 'undefined') {
            //$(".tab-pane").removeClass("active");
            //$("#tab-asset_payment").addClass("active");
            dTable['tblAsset_payment'].ajax.reload(null, true);
        } else {
            dTable['tblAsset_payment'] = $('#tblAsset_payment').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                ajax: {
                    "url": "<?php echo site_url('asset_payment/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = 1;
                        d.fixed_asset_id =<?php echo $fixed_asset['id']; ?>;
                    }
                },
                "columnDefs": [{
                        "targets": [4],
                        "orderable": false,
                        "searchable": false
                    }],
                "initComplete": function( settings, json ){
                    viewModel.asset_paid_amount( sumUp( json.data, 'amount' ) );
                 },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                    var total_page = api.column(2, {page: 'current'}).data();
                    var total_overall = api.column(2).data();
                    var total_page_amount = 0;
                    var total_overall_amount = 0;

                    $.each(total_page, function (key, val) {
                        total_page_amount += (val) ? (parseFloat(val)) : 0;
                    });
                    $.each(total_overall, function (key, val) {
                        total_overall_amount += (val) ? (parseFloat(val)) : 0;
                    });

                    $(api.column(2).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                },
                columns: [
                    {data: 'id'},
                    {data: 'transaction_date', render: function (data, type, full, meta) {
                            return data ? moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY') : '';
                        }},
                    {data: 'amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '0';
                        }},
                    {data: 'narrative'},
                    {"data": 'id', render: function (data, type, full, meta) {
                            var ret_txt = "";
                                ret_txt += '<a href="#add_asset_payment-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil"></i></a>';
    <?php if (in_array('7', $accounts_privilege)) { ?>
                                ret_txt += ' &nbsp;<a href="#" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
    <?php } if (in_array('4', $accounts_privilege)) { ?>
                                ret_txt += ' &nbsp;<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
    <?php } ?>
                            return ret_txt;
                        }
                    }
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('<?php echo $fixed_asset['asset_name']; ?> payment details'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
        }
    //}
