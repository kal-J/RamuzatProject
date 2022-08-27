if ($("#tblInvoice_payment").length){
    <?php if(isset($invoice_payment_detail)): ?>
    var buttons_title = 'Invoice payment transaction (<?php echo $invoice_payment_detail['id']; ?>)';
    var non_sort_idx = 5;
 <?php endif; ?>
    <?php if(isset($invoice)): ?>
    var buttons_title = 'Invoice (<?php echo $invoice['id']; ?>) payments';
    var non_sort_idx = 4;
 <?php endif; ?>
        dTable['tblInvoice_payment'] = $('#tblInvoice_payment').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('invoice_payment_line/jsonlist2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.status_id ='1';
                        <?php if(isset($invoice)): ?>
                        d.invoice_id =<?php echo $invoice['id']; ?>;
                        <?php endif; ?>
                        <?php if(isset($invoice_payment_detail)): ?>
                        d.invoice_payment_id =<?php echo $invoice_payment_detail['id']; ?>;
                        <?php endif; ?>
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([non_sort_idx-1], function(key,val){
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                    var total_overall_amount = api.column(val).data().sum();
                    $(api.column(val).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                    });
                },
            "columnDefs": [{
                    "targets": [non_sort_idx],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                <?php if(isset($invoice_payment_detail)): ?>
                    {data: 'ref_no', render:function(data, type,full,meta){return "<a href='<?php echo site_url("invoice/view"); ?>/"+full.invoice_id+"' title='View invoice transaction details'>"+data+"</a>";}},
                    {data: 'account_name', render: function(data, type,full,meta){ return data?("<a href='<?php echo site_url("accounts/view");?>/"+full.cash_account_id+"' title='Click to view account details'>["+full.account_code+ "]  "+data+"</a>"):'';}},
                    {data: 'invoiceing_date', render: function(data, type,full,meta){ if(type=='sort'){return data?(moment(data,'YYYY-MM-DD').format('X')):0;} return data?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';}},
                <?php else: ?>
                    {data: 'ref_no', render:function(data, type,full,meta){return "<a href='<?php echo site_url("invoice_payment/view"); ?>/"+full.invoice_payment_id+"' title='View invoice payment transaction details'>"+data+"</a>";}},
                    {data: 'payment_date', render: function(data, type,full,meta){ if(type=='sort'){return data?(moment(data,'YYYY-MM-DD').format('X')):0;} return data?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';}},
                <?php endif; ?>
                {data: 'narrative'},
                {data: 'amount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                //{data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active":'Deactivated'; }},
                {"data": 'id', render: function (data, type, full, meta) {
                    var ret_txt ="";
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#add_invoice-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
                     <?php } if(in_array('7', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
                    <?php } if(in_array('4', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                    <?php } ?>
                        return ret_txt;
                    }
                }
            ],
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig(buttons_title), <?php } else { echo "[],"; } ?>
            responsive: true
        });
}