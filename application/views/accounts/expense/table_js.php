if ($("#tblExpense").length && tabClicked === "tab-expense") {
    if (typeof (dTable['tblExpense']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-expense").addClass("active");
        dTable['tblExpense'].ajax.reload(null, true);
    } else {
        dTable['tblExpense'] = $('#tblExpense').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('expense/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.start_date = moment(start_date,'X').format('YYYY-MM-DD');
                        d.end_date = moment(end_date,'X').format('YYYY-MM-DD');
                        d.status_id ='1';
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([6], function(key,val){
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                    var total_overall_amount = api.column(val).data().sum();
                    $(api.column(val).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                    });
                },
            "columnDefs": [{
                    "targets": [9],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'receipt_no', render:function(data, type,full,meta){return "<a href='<?php echo site_url("expense/view"); ?>/"+full.id+"'>"+data+"</a>";}},
                {data: 'supplier_names', render:function(data, type,full,meta){return "<a href='<?php echo site_url("supplier/view"); ?>/"+full.supplier_id+"'>"+data+"</a>";}},
                //{data: 'id', render:function(data, type,full,meta){return "<a href='<?php echo site_url("expense/view"); ?>/"+data+"'>"+data+"</a>";}},
                {data: 'payment_date', render: function(data, type,full,meta){ if(type=='sort'){return data?(moment(data,'YYYY-MM-DD').format('X')):0;} return data?moment(data,'YYYY-MM-DD').format('D-M-YYYY'):'';}},
                {data: 'account_name', render: function(data, type,full,meta){ return data?("<a href='<?php echo site_url("accounts/view");?>/"+full.cash_account_id+"' title='Click to view account details'>["+full.account_code+ "]  "+data+"</a>"):'';}},
                {data: 'tax_rate_source_name', render: function(data, type,full,meta){ return data?data:'';}},
                {data: 'description'},
                {data: 'total_amount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'expense_attachment_url'},
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
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Expenses'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
}