if ($("#tblLoan_payments").length && tabClicked === "tab-loan_payments") {
                if (typeof (dTable['tblLoan_payments']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_payments").addClass("active");
                    dTable['tblLoan_payments'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_payments'] = $('#tblLoan_payments').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        "ajax":{
                            "url": "<?php echo base_url('loan_installment_payment/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.status_id = 1;
                             d.start_date = $('#start_date').val() ? moment($('#start_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                             d.end_date = $('#end_date').val() ? moment($('#end_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                            }
                        },
              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();
                    $.each([3,4,5,6,7], function(key,val){
                      if(val == 7){
                        var total_page_amount = (api.column(2, {page: 'current'}).data().sum() + api.column(3, {page: 'current'}).data().sum() + api.column(4, {page: 'current'}).data().sum());
                        var total_overall_amount = (api.column(2).data().sum() + api.column(3).data().sum() + api.column(4).data().sum());
                        $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)) + " (" + curr_format(round(total_overall_amount,2)) + ") ");
                      }else{
                        var total_page_amount = api.column(val, {page: 'current'}).data().sum();
                        var total_overall_amount = api.column(val).data().sum();
                        $(api.column(val).footer()).html(curr_format(round(total_page_amount,2)) + " (" + curr_format(round(total_overall_amount,2)) + ") ");

                      }
                    });
                },
            "columns": [
                      { data: "loan_no"},
                      { data: "member_name"},
                      { data: "installment_number", render:function( data, type, full, meta ){
                          return (full.installment_number !='')?data:'Pay off';}},
                      { data: "paid_interest", render:function( data, type, full, meta ){
                          return curr_format(data*1);}
                      },
                      { data: "paid_principal", render:function( data, type, full, meta ){
                          return curr_format(data*1);}
                      },
                      { data: "paid_penalty", render:function( data, type, full, meta ){
                          return curr_format(data*1);}
                      },
                      { data: "forgiven_interest", render:function( data, type, full, meta ){
                          return curr_format(data*1);}
                      },
                      {"data": "paid_principal", render: function (data, type, full, meta) {
                            return curr_format( round((parseFloat(full.paid_principal) + parseFloat(full.paid_interest) +parseFloat(full.paid_penalty)),2));
                        }
                      },
                      { data: "end_balance", render:function( data, type, full, meta ){
                          return curr_format(data*1);}
                      },
                      { data: "payment_date", render:function( data, type, full, meta ){
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                        return (!(data=='0000-00-00'))?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
                          }  
                      },
                      { data: "firstname", render:function( data, type, full, meta ){
                          return full.staff_no+'-'+full.firstname+' '+full.lastname+' '+full.othernames;}
                      },                      
                      { data: "comment"}
                   ],
                        buttons:<?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('Loan Payment Transactions'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
               }
            }