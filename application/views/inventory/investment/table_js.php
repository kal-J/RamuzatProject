 if($("#tblInvestment").length && tabClicked === "tab-investment") {
        if (typeof (dTable['tblInvestment']) !== 'undefined') {
            $(".tab-pane").removeClass("active");
            $("#tab-investment").addClass("active");
            dTable['tblInvestment'].ajax.reload(null, true);
        } else {
            dTable['tblInvestment'] = $('#tblInvestment').DataTable({
                "dom": '<"html5buttons"B>lTfgitp',
                "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'asc']],
                ajax: {
                    "url": "<?php echo site_url('investiment/jsonList') ?>",
                    "dataType": "json",
                    "type": "POST"
                },
                "columnDefs": [{
                        "targets": [6],
                        "orderable": false,
                        "searchable": false
                    }],
                "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                    var total_page3 = api.column(3, {page: 'current'}).data();
                    var total_page = api.column(4, {page: 'current'}).data();
                    var total_page5 = api.column(5, {page: 'current'}).data();

                    var total_overall3 = api.column(4).data();
                     var total_overall5 = api.column(5).data();
                    var total_overall = api.column(4).data();
                    var total_page_amount = 0;
                    var total_overall_amount = 0;

                     var total_page_amount3 = 0;
                     var total_overall_amount3 = 0;

                      var total_page_amount5 = 0;
                     var total_overall_amount5 = 0;

                    $.each(total_page, function (key, val) {
                        total_page_amount += (val) ? (parseFloat(val)) : 0;
                    });
                    $.each(total_overall, function (key, val) {
                        total_overall_amount += (val) ? (parseFloat(val)) : 0;
                    });

                      $.each(total_page3, function (key, val) {
                        total_page_amount3 += (val) ? (parseFloat(val)) : 0;
                    });
                    $.each(total_overall3, function (key, val) {
                        total_overall_amount3 += (val) ? (parseFloat(val)) : 0;
                    });

                    $.each(total_page5, function (key, val) {
                        total_page_amount5 += (val) ? (parseFloat(val)) : 0;
                    });
                    $.each(total_overall5, function (key, val) {
                        total_overall_amount5 += (val) ? (parseFloat(val)) : 0;
                    });

                    $(api.column(3).footer()).html(curr_format(total_page_amount3));
                    $(api.column(4).footer()).html(curr_format(total_page_amount));
                     $(api.column(5).footer()).html(curr_format(total_page_amount5));
                },
                columns: [

           
                     {data: 'type', render: function (data, type, full, meta) {
                             var type=full.type;
                             if(type==1){
                             var type='Fixed Deposit';
                              var padding=0;  
                             return "<a style='padding-left:"+padding+"px;' href='<?php echo site_url("investiment/transactions");?>/"+full.id+"' title='Click to view full investment details'>"+type+"</a>";
                            
                         }
                          else if(type==2){
                          var type='Bond';
                            var padding=0;  
                             return "<a style='padding-left:"+padding+"px;' href='<?php echo site_url("investiment/transactions");?>/"+full.id+"' title='Click to view full investment details'>"+type+"</a>";
                         }
                          else if(type==3){
                           var type='Stock';
                             var padding=0;  
                             return "<a style='padding-left:"+padding+"px;' href='<?php echo site_url("investiment/transactions");?>/"+full.id+"' title='Click to view full investment details'>"+type+"</a>";
                         }
                           }},
                     {data: 'account_name', render: function (data, type, full, meta) {
                    var ret_txt = '<a href="<?php echo site_url("investiment/transactions"); ?>/'+full.id+'" title="View account transactions">'+"["+full.account_code+ "]  "+data+'</a>';
                            return ret_txt;
                        }
                    },

                    {data: 'date_created', render: function (data, type, full, meta) {
                            return data ? moment.unix(data, 'DD-MMM-YYYY').format('DD-MMM-YYYY') : '';
                        }},
                         /*
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

                }},*/
                    {data: 'amount', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '0';
                        }},
                  
                     {data: 'gain', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '0';
                        }},
                        
                   {data: 'loss', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '0';
                        }},
                     /* {data: 'withdraw', render: function (data, type, full, meta) {
                            return data ? curr_format(data*1) : '0';
                        }},*/
                  

                    {data: 'status_id', render: function (data, type, full, meta) {
                           var status_id=full.status_id;
                           if(parseInt(status_id)==1){
                           return 'Active';
                        }
                         else if(parseInt(status_id)==2){
                           return 'Withdrawn';
                        }
                    }},
                  {"data": 'id', render: function (data, type, full, meta) {
                       var ret_txt = "";
                            ret_txt += ' &nbsp;<a href="#add_investment_modal" data-toggle="modal" class="btn btn-sm btn-default edit_me"><i class="fa fa-pencil-square-o text-warning"></i></a>';
                            ret_txt += ' &nbsp;<a href="#add_gain_loss_modal" data-toggle="modal" title="Add Transaction" class="btn btn-sm btn-warning gain_or_loss "><i class="fa fa-arrow-up text-white"></i></a>';
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
             inventoryModel.investment_details( null ); 
             inventoryModel.investment_details(data);
        });