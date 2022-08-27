if ($("#tblIncome").length && tabClicked === "tab-income") {
    if (typeof (dTable['tblIncome']) !== 'undefined') {
        $(".tab-pane").removeClass("active");
        $("#tab-income").addClass("active");
        dTable['tblIncome'].ajax.reload(null, true);
    } else {
        dTable['tblIncome'] = $('#tblIncome').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('income/jsonList') ?>",
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
                    var total_page_amount = api.column(6, {page: 'current'}).data().sum();
                    var total_overall_amount = api.column(6).data().sum();
                    $(api.column(6).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                },
            "columnDefs": [{
                    "targets": [9],
                    "orderable": false,
                    "searchable": false
                }],
            columns: [
                {data: 'receipt_no', render:function(data, type,full,meta){return "<a href='<?php echo site_url("income/view"); ?>/"+full.id+"'>"+data+"</a>";}},
                {data: 'client_names', render:function(data, type,full,meta){return "<a href='<?php echo site_url("member/member_personal_info"); ?>/"+full.client_id+"'>"+data+"</a>";}},
                {data: 'receipt_date', render: function(data, type,full,meta){ 
                    if(type=='sort'){
                        return data?(moment(data,'YYYY-MM-DD').format('X')):'';
                    }
                    return data?moment(data,'YYYY-MM-DD').format('DD-MM-YYYY'):'';
                  }
                },
                {data: 'account_name', render:function(data, type,full,meta){return data?("<a href='<?php echo site_url("accounts/view");?>/"+full.cash_account_id+"' title='Click to view account details'>["+full.account_code+ "]  "+data+"</a>"):'';}},
                {data: 'tax_rate_source_name', render: function(data, type,full,meta){ return data?data:'';}},
                {data: 'description'},
                {data: 'total_amount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},
                {data: 'attachment_url'},
                {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active":'Deactivated'; }},
                {"data": 'id', render: function (data, type, full, meta) {
                    var ret_txt ="";
                    <?php if(in_array('3', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#add_income-modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil text-warning"></i></a>';
                     <?php } if(in_array('7', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status"><i class="fa fa-ban text-warning"></i></a>';
                    <?php } if(in_array('4', $accounts_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                    <?php } ?>
                        return ret_txt;
                    }
                }
            ],
            buttons: <?php if(in_array('6', $accounts_privilege)){ ?> getBtnConfig('Other Income'), <?php } else { echo "[],"; } ?>
            responsive: true
        });
    }
}