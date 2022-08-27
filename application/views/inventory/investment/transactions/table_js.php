if ($("#tblInvestment_trans").length && tabClicked === "tab-investment_trans") {
        if (typeof (dTable['tblInvestment_trans']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-investment_trans").addClass("active");
            dTable['tblInvestment_trans'].ajax.reload(null, true);
        } else {
            dTable['tblInvestment_trans'] = $('#tblInvestment_trans').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                ajax: {
                    "url":  "<?php echo site_url('investiment/jsonList2') ?>",
                    "dataType": "json",
                    "type": "POST",
                    "data": function (d) {
                        d.status_id=1;
                        d.id =<?php echo $id; ?>;
                    }
                     
                },
                "columnDefs": [{
                        "targets": [6],
                        "orderable": false,
                        "searchable": false
                    }],
                      "footerCallback": function (tfoot, data, start, end, display) {

                    var api = this.api();

                    var total_page4 = api.column(4, {page: 'current'}).data();
                    var total_page5 = api.column(5, {page: 'current'}).data();

                     var total_overall4 = api.column(5).data();
                      var total_page_amount4 = 0;
                      var total_overall_amount4 = 0;

                      var total_overall5 = api.column(5).data();
                      var total_page_amount5 = 0;
                      var total_overall_amount5 = 0;

                     $.each(total_page4, function (key, val) {
                        total_page_amount4 += (val) ? (parseFloat(val)) : 0;
                    });
                    $.each(total_overall4, function (key, val) {
                        total_overall_amount4 += (val) ? (parseFloat(val)) : 0;
                    });

                    $.each(total_page5, function (key, val) {
                        total_page_amount5 += (val) ? (parseFloat(val)) : 0;
                    });
                    $.each(total_overall5, function (key, val) {
                        total_overall_amount5 += (val) ? (parseFloat(val)) : 0;
                    });
                     $(api.column(4).footer()).html(curr_format(total_page_amount4));
                     $(api.column(5).footer()).html(curr_format(total_page_amount5));
                },
             
                columns: [
           
                    {data: 'transaction_type_id',render:function(data,type,full,meta){
                     var trans_id=full.transaction_type_id;
                     if(trans_id==1){
                     return 'Deposit';
                      }
                     if(trans_id==2){
                     return 'Gain';
                      }
                     if(trans_id==3){
                     return 'Loss';
                      }
                       if(trans_id==4){
                     return 'Withdrawal';
                      }
                }},
                     {data: 'transaction_no'},

                   {data: 'transaction_date', render: function (data, type, full, meta) {
                            return data ? moment(data, 'YYYY-MM-DD HH:mm:ss').format('DD-MMM-YYYY  HH:mm:ss') : '';
                        }},
                   {data: 'payment_mode',render:function(data,type,full,meta){
                      var payment_mode=full.payment_mode;
                      if(payment_mode==1){
                      return 'Cash';
                    }
                    else if(payment_mode==2){
                      return 'Bank';
                    }
                     else if(payment_mode==3){
                      return 'Credit';
                    }
                     else if(payment_mode==4){
                      return 'Mobile Money';
                    }

                }},
                 {data: 'debit', render:function(data,type,full,meta){return data?curr_format(data*1):curr_format(data*1);}},
                    
                 {data: 'credit', render:function(data,type,full,meta){return data?curr_format(data*1):curr_format(data*1);}},
                    
                    {data: 'description'},

                    {data: 'status_id', render: function (data, type, full, meta) {
                           var status_id=full.status_id;
                           if(parseInt(status_id)==1){
                           return 'Active';
                        }
                         else if(parseInt(status_id)==2){
                           return 'Withdrawn';
                        }
                         else if(parseInt(status_id)==3){
                           return 'Reversed';
                        }
                    }},

                    
                    {"data": 'id', render: function (data, type, full, meta) {
                       var ret_txt = "";
                            ret_txt += ' &nbsp;<a href="#reverse-modal" data-toggle="modal" title="Reverse Transaction" class="btn btn-sm btn-warning gain_or_loss "><i class="fa fa-undo text-white"></i></a>';
                        return ret_txt;
                        }
                    }
                ],
            
               buttons: <?php if (in_array('6', $accounts_privilege)) { ?> getBtnConfig('Module and accounts_privilege'), <?php } else {
      echo "[],";
        } ?>
                responsive: true
            });
        }
      }
    
    $('table tbody').on('click', 'tr .gain_or_loss', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
             viewModel.investment_data( null ); 
             viewModel.investment_data(data);
              
        });

