   if ($("#tblJournal_transaction_line").length) {
        dTable['tblJournal_transaction_line'] = $('#tblJournal_transaction_line').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[0, 'asc'], [1, 'asc']],
                ajax: {
                    "url": "<?php echo site_url('Journal_transaction_line/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id = '1';
                        d.journal_transaction_id =<?php echo isset($detail['id'])?$detail['id']:'null'; ?>;
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                    var debit_page = api.column(4, {page: 'current'}).data().sum();
                    var debit_overall = api.column(4).data().sum();
                    var credit_page = api.column(5, {page: 'current'}).data().sum();
                    var credit_overall = api.column(5).data().sum();

                    $(api.column(4).footer()).html(curr_format(debit_page*1) + "(" + curr_format(debit_overall*1) + ") ");
                    $(api.column(5).footer()).html(curr_format(credit_page*1) + "(" + curr_format(credit_overall*1) + ") ");
                },
                columnDefs:[
                //{ "visible": false, "targets": 1 }
                ],
                columns: [
                    {data: 'id'},
                    {data: 'account_name', render:function(data, type, full, meta){ 
                    //return "["+full.account_code + "] "+data;
                    return "<a href='<?php echo site_url("accounts/view");?>/"+full.account_id+"' title='Click to view full details'>["+full.account_code+ "]  "+data+"</a>";
                    }
                    },
                     {data: 'transaction_date', render: function (data, type, full, meta) {
                            if (type == 'sort') {
                                return moment(data, 'YYYY-MM-DD').format('X');
                            }
                            return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                        }
                    },
                    {data: 'narrative'},
                    {data: 'debit_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '';
                        }},
                    {data: 'credit_amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '';
                        }}
             
                ],
                buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('Module and accounts_privilege'), <?php
    } else {
        echo "[],";
    }
    ?>
                responsive: true
            });
    }