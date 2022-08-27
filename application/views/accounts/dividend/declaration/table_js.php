if ($("#tblDividend_declaration").length && tabClicked === "tab-declarations") {
    if (typeof (dTable['tblDividend_declaration']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-declarations").addClass("active");
        dTable['tblDividend_declaration'].ajax.reload(null, true);
    } else {
        dTable['tblDividend_declaration'] = $('#tblDividend_declaration').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('dividend_declaration/jsonlist') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        d.start_date = moment(start_date,'X').format('YYYY-MM-DD');
                        d.end_date = moment(end_date,'X').format('YYYY-MM-DD');
                        //d.status_id ='1';
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                    var total_page_amount = api.column(8, {page: 'current'}).data().sum();
                    var total_overall_amount = api.column(8).data().sum();
                    $(api.column(8).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                },
            "columnDefs": [{
                    "targets": [13],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                 {data: 'id', render:function(data, type,full,meta){return "<a href='<?php echo site_url("dividend_declaration/view");?>/"+data+"' title='Click to view details'>#"+data+"</a>";}},
                {data: 'cash_stock', render: function(data, type,full,meta){ 
                    return data?(data==1?'Cash':'Stock'):'';
                  }
                },
                {data: 'declaration_date', render: function(data, type,full,meta){ 
                    if(type=='sort'){
                        return data && data!=='0000-00-00'?(moment(data,'YYYY-MM-DD').format('X')):'';
                    }
                    return data && data!=='0000-00-00'?moment(data,'YYYY-MM-DD').format('DD-MMM-YYYY'):'';
                  }
                },
                {data: 'record_date', render: function(data, type,full,meta){ 
                    if(type=='sort'){
                        return data && data!=='0000-00-00'?(moment(data,'YYYY-MM-DD').format('X')):'';
                    }
                    return data && data!=='0000-00-00'?moment(data,'YYYY-MM-DD').format('DD-MMM-YYYY'):'';
                  }
                },
                {data: 'payment_date', render: function(data, type,full,meta){ 
                    if(type=='sort'){
                        return data && data!=='0000-00-00'?(moment(data,'YYYY-MM-DD').format('X')):'';
                    }
                    return data && data!=='0000-00-00'?moment(data,'YYYY-MM-DD').format('DD-MMM-YYYY'):'';
                  }
                },
                {data: 're_acc_name', render:function(data, type,full,meta){return data?("<a href='<?php echo site_url("accounts/view");?>/"+full.retained_earnings_acc_id+"' title='Click to view account details'>["+full.re_acc_code+ "]  "+data+"</a>"):'';}},
                {data: 'dp_acc_name', render:function(data, type,full,meta){return data?("<a href='<?php echo site_url("accounts/view");?>/"+full.dividends_payable_acc_id+"' title='Click to view account details'>["+full.dp_acc_code+ "]  "+data+"</a>"):'';}},
                {data: 'total_computed_share', render: function(data, type,full,meta){ if(type=='sort'){return full.total_computed_share;}return data?full.total_computed_share:'';}},
                {data: 'total_dividends', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'dividend_per_share', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'paying_ordinary_sh', render:function(data, type,full,meta){
                    let payment_details = "Preference Shareholders";
                    if(full.paying_preference_sh == 1){
                    payment_details = payment_details+"<br/>Cumulative Prefence Shareholders";
                    }
                    if(full.paying_ordinary_sh == 1){
                    payment_details =payment_details+ "<br/>Ordinary Shareholders";
                    }
                    return payment_details;
                    }
                },
                {data: 'notes'},
                {data: 'attachment_url'},
                {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Unpaid":'Paid Out'; }},
                {"data": 'id', render: function (data, type, full, meta) {                        
                var ret_txt =full.status_id==1?('<a href="#pay_dividend-modal" data-toggle="modal" class="btn btn-sm btn-default pay_dividend" title="Pay dividends"><i class="fa fa-check text-primary"></i></a>'):'';
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#add_dividend_declaration-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
                     <?php } if(in_array('7', $accounts_privilege)){ ?>
                        <!-- ret_txt += (full.status_id==1?'<a href="#" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>':''); -->
                    <?php } if(in_array('4', $accounts_privilege)){ ?>
                        ret_txt += (full.status_id==1?'<a href="#" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>':'');
                    <?php } ?>
                        return ret_txt;
                    }
                }
            ],
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Dividend Declarations'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
}