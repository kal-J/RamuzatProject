if ($("#tblRepayment_schedule").length && tabClicked === "tab-repayment_schedule") {
                if (typeof (dTable['tblRepayment_schedule']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-repayment_schedule").addClass("active");
                    dTable['tblRepayment_schedule'].ajax.reload(null, true);
                } else {
                    dTable['tblRepayment_schedule'] = $('#tblRepayment_schedule').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        "ajax":{
                            "url": "<?php echo base_url('repayment_schedule/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.client_loan_id = <?php echo $loan_detail['id']; ?>,
                             d.status_id =1
                            }
                        },
            columnDefs: [{
                  "targets": [5],
                  "orderable": false,
                  "searchable": false
              }],
              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();

                $.each([1,2,3,4], function(key,val){
                  if(val==4){
                    var total_overall_amount = parseFloat(api.column(4).data().sum()) + parseFloat(api.column(3).data().sum()) ;
                    $(api.column(4).footer()).html(curr_format(round(total_overall_amount,2)));
                    
                  }else{
                    var total_overall_amount = api.column(val).data().sum();
                    $(api.column(val).footer()).html(curr_format(round(total_overall_amount,2)));
                  }
                });
                },

              rowCallback: function (row, data) {
                  <!-- paid payment -->
                  if ( data.payment_status==1 || data.payment_status==3) {
                      $(row).addClass('text-success');
                  }
                  <!-- partial payment -->
                  if ( data.payment_status ==2) {
                      $(row).addClass('text-info');
                  }
                  <!-- defaulting installment -->
                  if(data.payment_status !=1 && data.installment_status == 1 && data.payment_status !=3){
                      $(row).addClass('text-danger');
                  }
                   <!-- payment is today(almost defaulting) installments -->
                  if(data.payment_status !=1 && data.installment_status == 2){
                      $(row).addClass('text-warning');
                  }

              },
            "columns": [
                      { data: "repayment_date", render:function( data, type, full, meta ){
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                        return (!(data=='0000-00-00'))?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
                          }  },
                      { data: "interest_amount", render:function( data, type, full, meta ){
                          return curr_format(data*1);} 
                      },
                      { data: "principal_amount", render:function( data, type, full, meta ){
                        return curr_format(data*1);} 
                      },
                      { data: "penalty_value", render:function( data, type, full, meta ){
                        return curr_format(data*1);} 
                      },
                      { data: "total_amount", render:function( data, type, full, meta ){
                        return (full.penalty_value != '')? curr_format( round((parseFloat(data)+parseFloat(full.penalty_value)),2) ):curr_format(data*1);} 
                      },
                      { data: "actual_payment_date", render:function( data, type, full, meta ){
                        return (!(data=='0000-00-00'))?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
                          }  },
                      { data: "payment_name" }
                   ],
                        buttons:getBtnConfig('Loan Schedule'),
                        responsive: true
                    });
               }
            }
