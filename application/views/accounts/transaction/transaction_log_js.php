   if ($("#tblJournal_transaction_log").length && tabClicked === "tab-transactions_log") {
        if (typeof (dTable['tblJournal_transaction_log']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-transactions_log").addClass("active");
            dTable['tblJournal_transaction_log'].ajax.reload(null, true);
        } else {
            dTable['tblJournal_transaction_log'] = $('#tblJournal_transaction_log').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'asc'], [1, 'asc']],
                "processing": true,
                "serverSide": true,
                "orderable": false,
                "searchable": false,
                "deferRender": true,
                responsive: true,
                ajax: {
                    "url": "<?php echo site_url('journal_transaction/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.start_date = start_date ? moment(start_date,'X').format('YYYY-MM-DD') : '';
                        d.end_date = end_date ? moment(end_date,'X').format('YYYY-MM-DD') : '';
                        d.status_id = '3';
                    }
                },
                rowCallback: function (row, data) {
                  if(data.status_id == 3){
                      $(row).addClass('text-danger');
                  }
                },
                "columnDefs": [{
                        "targets": [7],
                        "orderable": false,
                        "searchable": false
                    }],
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                 
                    var tt_amount_page = api.column(4, {page: 'current'}).data().sum();
                    var tt_amount_overall = api.column(4).data().sum();

                   $(api.column(4).footer()).html(curr_format(tt_amount_page) + "(" + curr_format(tt_amount_overall) + ") ");
                },
                columns: [
                {data: 'id', render: function (data, type, full, meta) { return "<a href='<?php echo site_url("journal_transaction/view"); ?>/"+data+"'>#"+data+"</a>";}},
                    {data: 'ref_no'},
                    {data: 'transaction_date', render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                        }
                    },
                    {data: 'type_name'},
                   
                    {data: 't_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '';
                        }},
                  
                    {data: 'reverse_msg', render: function (data, type, full, meta) {
                     
                            return data;
                        },
                        createdCell: function (td, cellData, rowData, row, col) {
                           if (rowData.status_id==3) $(td).css('text-decoration','none');
                        }
                    },
                    {data: 'reversed_date', render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                        }
                    },
                    {data: 'description', render: function (data, type, full, meta) {
                     
                            return data;
                        }
                    },
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