if ($("#tblSales_transaction").length && tabClicked === "tab-sales") {
        if (typeof (dTable['tblSales_transaction']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-sales").addClass("active");
            dTable['tblSales_transaction'].ajax.reload(null, true);
        } else {
            dTable['tblSales_transaction'] = $('#tblSales_transaction').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'asc'], [1, 'asc']],
                "processing": true,
                "deferRender": true,
                responsive: true,
                ajax: {
                    "url": "<?php echo site_url('Sales/jsonList'); ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.start_date = start_date ? moment(start_date,'X').format('YYYY-MM-DD') : '';
                        d.end_date = end_date ? moment(end_date,'X').format('YYYY-MM-DD') : '';
                        d.status_id = '3';
                    }
                },
                "columnDefs": [{
                        "targets": [5],
                        "orderable": false,
                        "searchable": false
                    }],
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                 
                    var tt_amount_page = api.column(5, {page: 'current'}).data().sum();
                    var tt_amount_overall = api.column(5).data().sum();

                   $(api.column(5).footer()).html(curr_format(tt_amount_page) + "(" + curr_format(tt_amount_overall) + ") ");
                },
                columns: [
                {data: 'item_name', render: function (data, type, full, meta) { return data; }},
                    {data: 'ref_no'},
                    {data: 'member_name'},
                    {data: 'transaction_date', render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                        }
                    },
                    {data: 'narrative', render: function (data, type, full, meta) {
                     
                        return data;
                        },
                        
                    },
                   
                    {data: 'amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '';
                     }},
                        
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('Module and accounts_privilege'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
        }
    }