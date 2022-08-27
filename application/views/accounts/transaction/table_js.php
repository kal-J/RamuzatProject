   if ($("#tblJournal_transaction_line").length) {      
        dTable['tblJournal_transaction_line'] = $('#tblJournal_transaction_line').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc'], [1, 'asc']],
                 "processing": true,
                "serverSide": true,
                "orderable": false,
                "searchable": false,
                "deferRender": true,
                responsive: true,
                ajax: {
                    "url": "<?php echo site_url('Journal_transaction_line/jsonList2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = '1';
                        d.start_date = start_date ? moment(start_date,'X').format('YYYY-MM-DD') : '';
                        d.end_date = end_date ? moment(end_date,'X').format('YYYY-MM-DD') : '';
                        d.account_id =<?php echo isset($ledger_account['id'])?$ledger_account['id']:'null'; ?>;
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                   var debit_page = api.column(6, {page: 'current'}).data().sum();
                    var debit_overall = api.column(6).data().sum();
                    var credit_page = api.column(7, {page: 'current'}).data().sum();
                    var credit_overall = api.column(7).data().sum();

                    $(api.column(6).footer()).html(curr_format(debit_page*1) + "(" + curr_format(debit_overall*1) + ") ");
                    $(api.column(7).footer()).html(curr_format(credit_page*1) + "(" + curr_format(credit_overall*1) + ") ");

                },
                rowCallback: function (row, data) {
                  if(data.status_id == 3){
                      $(row).addClass('strikethrough');
                  }
                },
                columns: [
                    {data: 'journal_transaction_id', render:function(data, type, full, meta){ return "<a href='<?php echo site_url("journal_transaction/view"); ?>/"+data+"'>#"+data+"</a>"; }},
                    {data: 'transaction_date', render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                        }
                    },
                    {data: 'ref_id'},
                    {data: 'ref_no'},
                    {data: 'type_name'},
                     {data: 'description', render: function (data, type, full, meta) {
                     
                            return data;
                        }
                    },
                    {data: 'debit_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : 0;
                        }},
                    {data: 'credit_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : 0;
                        }},
                    /*{data: 'status_id', render:function ( data, type, full, meta ) {return (data===1)?"Deactivated ":'Active'; }},*/
                    {"data": 'id', render: function (data, type, full, meta) {
                            var ret_txt = "";
    <?php if (in_array('3', $accounts_privilege)) { ?>
                              <!--   ret_txt += '<a href="#post_entry-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>'; -->
    <?php } if (in_array('7', $accounts_privilege)) { ?>
                             <!--    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>'; -->
    <?php } if (in_array('4', $accounts_privilege)) { ?>
                              <!--   ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>'; -->
    <?php } ?>
                            return ret_txt;
                        }
                    },
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
                    }
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('<?php echo $ledger_account['account_name']; ?> transactions list'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
    }   