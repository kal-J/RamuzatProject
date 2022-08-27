if ($("#tblShares").length && tabClicked === "tab-shares") {
        if (typeof (dTable['tblShares']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-shares").addClass("active");
            dTable['tblShares'].ajax.reload(null, true);
        } else {
            dTable['tblShares'] = $('#tblShares').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                //order: [[1, 'asc']],
                deferRender: true,
                ajax: {
                    "url": "<?php echo site_url('Shares/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.acc_id = '<?php echo $acc_id; ?>';
                            }
                },
                "columnDefs": [{
                        "targets": [4],
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'product_name'},
                    {data: 'share_price', render: function (data, type, full, meta) {
                            return data?curr_format(data * 1):'';
                        }
                    },
                    {data: 'shares'},
                    {data: 'application_date', render: function (data, type, full, meta) { return data?moment(data,'YYYY-MM-DD').format('DD-MMM-YYYY'):'';}},
                    {data: 'id', render: function (data, type, full, meta) {
                    var display_btn = '';
    <?php if (in_array('7', $share_privilege)) { ?>
                                display_btn = '<a href="#" title="Revoke shares"><span class="fa fa-trash text-danger change_status"></span></a>';
    <?php } ?>
                                return display_btn;
                        }
                    }
                ],
                buttons: <?php if (in_array('6', $share_privilege)) { ?> getBtnConfig('Shares'), <?php } else {
    echo "[],";
} ?>
                responsive: true
            });
        }
    }


