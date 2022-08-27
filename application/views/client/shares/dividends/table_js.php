if ($('#tblDividend_payment').length && tabClicked === "tab-dividends") {
                if(typeof dTable['tblDividend_payment'] !=='undefined'){
                //$("#tab-dividends").addClass("active");
                    dTable['tblDividend_payment'].ajax.reload(null,true);
                }else{
                    dTable['tblDividend_payment'] = $('#tblDividend_payment').DataTable({
            "dom": '<"html5buttons"B>lTfgitp',
            order: false,
            deferRender: true,
            ajax:{
                    "url": "<?php echo site_url('u/Dividend_payment/get_member_dividends') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function(d){
                        <?php if(isset($user)): ?>d.member_id =<?php echo $user['id']; ?>; <?php endif; ?>
                        <?php if(isset($get_share_by_id)): ?>d.share_account_id =<?php echo $get_share_by_id['id']; ?>; <?php endif; ?>
                    }
                },
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                $.each([7], function(key,val){
                    var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                    var total_overall_amount = api.column(val).data().sum();
                    $(api.column(val).footer()).html(curr_format(total_page_amount) + " (" + curr_format(total_overall_amount) + ") ");
                    });
                },
            /*"columnDefs": [{
                    "targets": [3],
                    "orderable": false,
                    "searchable": false
                }],*/
            columns: [
                {data: 'declaration_id', render:function(data, type,full,meta){return "# "+data;}},

                {data: 'start_date', render: function(data, type,full,meta){ return moment(data, 'YYYY-MM-DD').format('MMM-YYYY')+" - "+moment(full.end_date, 'YYYY-MM-DD').format('MMM-YYYY');}},

                {data: 'record_date', render: function(data, type,full,meta){ return data?moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY'):'';}},

                {data: 'date_paid', render: function(data, type,full,meta){ return data?moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY'):'';}},

                {data: 'record_share_amount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},

                {data: 'no_of_shares', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},

                {data: 'dividend_per_share', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},

                {data: 'amount', render: function(data, type,full,meta){ if(type=='sort'){return data;}return data?curr_format(data*1):'';}},

                {data: 'cash_stock', render: function(data, type,full,meta){ 
                    return data?(data==1?'Cash':'Stock'):'';
                  }
                }
            ],
            buttons: getBtnConfig('Dividend payments'),
            responsive: true
        });
}
}