  if ($("#tblJournal_transaction_line").length && tabClicked === "tab-cash_register") {
        if (typeof (dTable['tblJournal_transaction_line']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-cash_register").addClass("active");
            dTable['tblJournal_transaction_line'].ajax.reload(null, true);
           // get_cash_register();
        } else {    
        dTable['tblJournal_transaction_line'] = $('#tblJournal_transaction_line').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[100, 250, 500, 1000, -1], [100, 250, 500, 1000, "All"]],
                order: [[1, 'asc']],
                 "processing": true,
            "language": {
              processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
            },
                "serverSide": true,
                "orderable": false,
                "bInfo" : false,
                "searchable": false,
                "deferRender": true,
                responsive: true,
                ajax: {
                    "url": "<?php echo site_url('Journal_transaction_line/jsonList2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = '1';
                        d.start_date = $('#start_date').val();
                        d.end_date = $('#end_date').val();
                        d.account_id = $('#account_id').val();
                        d.created_by = $('#created_by').val();
                        d.all = $('#all').val();
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                   var debit_page = api.column(7, {page: 'current'}).data().sum();
                    var debit_overall = api.column(7).data().sum();
                    var credit_page = api.column(8, {page: 'current'}).data().sum();
                    var credit_overall = api.column(8).data().sum();

                    //$(api.column(6).footer()).html(curr_format(debit_page*1) + "(" + curr_format(debit_overall*1) + ") ");
                    $(api.column(7).footer()).html(curr_format(debit_overall*1));
                    $(api.column(8).footer()).html(curr_format(credit_overall*1));

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
                    {data: 'ref_no'},
                    {data: 'reference_key'},
                    {data: 'member_name'},
                    
                     {data: 'account_name', render:function(data, type, full, meta){ 
                    return "["+full.account_code + "] "+data+ "  -";
                  
                    }
                    },
                    {data: 'type_name'},

                    {data: 'debit_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : 0;
                        }},
                    {data: 'credit_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : 0;
                        }},
                    {data: 'description', render: function (data, type, full, meta) {
                     
                            return data;
                        }
                    },
                   
                    {data: 'staff_name'},

                    {data: 'reversed_date', render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                        }
                    }
                ],
                buttons: <?php if (in_array('6', $till_privilege)) { ?> getBtnConfig('Cash Register'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
    }
    }   