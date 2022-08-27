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
                    "url": "<?php echo site_url('u/shares/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data":
                            function (e) {
                                e.status_id = '1';
                                e.acc_id = '<?php echo $acc_id; ?>';
                            }
                },
                "columnDefs": [{
                        "targets": [3],
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
                    {data: 'application_date', render: function (data, type, full, meta) { return data?moment(data,'YYYY-MM-DD').format('DD-MMM-YYYY'):'';}}
                ],
                buttons:  getBtnConfig('Shares'),
                responsive: true
            });
        }
    }


