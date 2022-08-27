if ($("#tblExpense_line").length){
        dTable['tblExpense_line'] = $('#tblExpense_line').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('expense_line/jsonlist') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.status_id ='1';
                        d.expense_id =<?php echo $expense['id']; ?>;
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([3], function(key,val){
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                    var total_overall_amount = api.column(val).data().sum();
                    $(api.column(val).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                    });
                },
            "columnDefs": [{
                    "targets": [5],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'expense_id', render:function(data, type,full,meta){return "<a href='<?php echo site_url("expense/view"); ?>/"+data+"'>"+data+"</a>";}},
                {data: 'account_name', render: function(data, type,full,meta){ return data?("<a href='<?php echo site_url("accounts/view");?>/"+full.account_id+"' title='Click to view account details'>["+full.account_code+ "]  "+data+"</a>"):'';}},
                {data: 'narrative'},
                {data: 'amount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active":'Deactivated'; }},
                {"data": 'id', render: function (data, type, full, meta) {
                    var ret_txt ="";
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#add_expense-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
                     <?php } if(in_array('7', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
                    <?php } if(in_array('4', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                    <?php } ?>
                        return ret_txt;
                    }
                }
            ],
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Expense ID#<?php echo $expense['id']; ?>'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
}