if ($("#tblBill").length && tabClicked === "tab-bill") {
var dr_options = {
            'Today': [moment(), moment()],
            'Tomorrow': [moment().add(1, 'days'), moment().add(1, 'days')],
            'Next 7 Days': [moment(), moment().add(6, 'days')],
            'Next 30 Days': [moment(), moment().add(30, 'days')],
            'Next 60 Days': [moment(), moment().add(60, 'days')],
            'Next 90 Days': [moment(), moment().add(90, 'days')]
        };
daterangepicker_initializer(dr_options,  moment().format("DD-MM-YYYY"),moment().add(180, 'days').format("DD-MM-YYYY"));
    if (typeof (dTable['tblBill']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-bill").addClass("active");
        dTable['tblBill'].ajax.reload(null, true);
    } else {
        dTable['tblBill'] = $('#tblBill').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('bill/jsonlist') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.start_date = moment(start_date,'X').format('YYYY-MM-DD');
                        d.end_date = moment(end_date,'X').format('YYYY-MM-DD');
                        d.date_option ='due_date';
                        d.status_id ='1';
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                     var page_invoice_amount = api.column(6, {page: 'current'}).data().sum();
                     var all_pages_invoice_amount = api.column(6).data().sum();
                     var page_discount_amount = api.column(7, {page: 'current'}).data().sum();
                     var all_pages_discount_amount = api.column(7).data().sum();
                     var page_paid_amount = api.column(8, {page: 'current'}).data().sum();
                     var all_pages_paid_amount = api.column(8).data().sum();
                     //balance
                     var page_due_amount = page_invoice_amount - page_discount_amount -page_paid_amount;
                     var all_pages_due_amount = all_pages_invoice_amount - all_pages_discount_amount - all_pages_paid_amount;
                     
                    $(api.column(6).footer()).html(curr_format(page_invoice_amount) + " (" + curr_format(all_pages_invoice_amount) + ") ");
                    $(api.column(7).footer()).html(curr_format(page_discount_amount) + " (" + curr_format(all_pages_discount_amount) + ") ");
                    $(api.column(8).footer()).html(curr_format(page_paid_amount) + " (" + curr_format(all_pages_paid_amount) + ") ");
                    $(api.column(9).footer()).html(curr_format(page_due_amount) + " (" + curr_format(all_pages_due_amount) + ") ");
                },
            "columnDefs": [{
                    "targets": [12],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'ref_no', render:function(data, type,full,meta){return "<a href='<?php echo site_url("bill/view"); ?>/"+full.id+"'>"+data+"</a>";}},
                {data: 'supplier_names', render:function(data, type,full,meta){return "<a href='<?php echo site_url("supplier/view"); ?>/"+full.supplier_id+"'>"+data+"</a>";}},
                //{data: 'supplier_short_name', render:function(data, type,full,meta){return "<a href='<?php echo site_url("supplier/view"); ?>/"+full.supplier_id+"'>"+data+"</a>";}},
                {data: 'billing_date', render: function(data, type,full,meta){ if(type=='sort'){return data?(moment(data,'YYYY-MM-DD').format('X')):0;} return data?moment(data,'YYYY-MM-DD').format('D-M-YYYY'):'';}},
                {data: 'due_date', render: function(data, type,full,meta){ if(type=='sort'){return data?(moment(data,'YYYY-MM-DD').format('X')):0;} return data?moment(data,'YYYY-MM-DD').format('D-M-YYYY'):'';}},
                {data: 'term_name', render: function(data, type,full,meta){ return data?data:'';}},
                {data: 'description'},
                {data: 'total_amount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'discount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'amount_paid', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'total_amount', render: function(data, type,full,meta){ 
                        var paid_amount = full.amount_paid?parseFloat(full.amount_paid):0;
                        var total_amount = data?parseFloat(data):0;
                        var discount = full.discount?parseFloat(full.discount):0;
                        var due_amount = ((total_amount-discount-paid_amount)*1);
                    if(type=='sort'){
                        return due_amount;
                    }
                        return curr_format(due_amount);
                    }
                },
                {data: 'status_id', render:function ( data, type, full, meta ) {
                        var paid_amount = full.amount_paid?parseFloat(full.amount_paid):0;
                        var total_amount = full.total_amount?parseFloat(full.total_amount):0;
                        var discount = full.discount?parseFloat(full.discount):0;
                        var due_amount = ((total_amount-discount-paid_amount)*1);

                        var cur_moment = moment();
                        var due_date = moment(full.due_date,'YYYY-MM-DD');
                        var due_days = cur_moment.diff(due_date,'days');
                        
                        var status = "Unpaid";
                        if(due_amount >0 && due_days>0){
                          status = "<span class='text-danger'>Overdue by " + due_days + " days</span>";
                        }else{ 
                            if(due_amount>0 && due_days > -10 && due_days < 0 ){
                            status = "<span class='text-warning'>Due in " + Math.abs(due_days) + " days</span>";
                            }else {
                                if( due_amount>0 && due_days == 0){
                                status = "<span class='text-danger'> Overdue " + " </span>";
                                }
                            }
                        }
                        return status; 
                    }
                },
                {data: 'attachment_url'},
                {"data": 'id', render: function (data, type, full, meta) {
                        var paid_amount = full.amount_paid?parseFloat(full.amount_paid):0;
                        var total_amount = full.total_amount?parseFloat(full.total_amount):0;
                        var due_amount = ((total_amount-paid_amount)*1);
                        
                var ret_txt =due_amount>0?('<a href="#pay_bill-modal" data-toggle="modal" class="btn btn-sm btn-default pay_bill" title="Pay the bill"><i class="fa fa-check text-primary"></i></a>'):'';
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#add_bill-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
                     <?php } if(in_array('7', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
                    <?php } if(in_array('4', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                    <?php } ?>
                        return ret_txt;
                    }
                }
            ],
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Bill'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
}