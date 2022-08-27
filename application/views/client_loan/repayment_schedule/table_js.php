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
                  "targets": [7],
                  "orderable": false,
                  "searchable": false
              }],
              "footerCallback": function (tfoot, data, start, end, display) {
                    var api = this.api();

                $.each([2,3,4,5], function(key,val){
                  if(val==5){
                    var total_overall_amount = parseFloat(api.column(5).data().sum()) + parseFloat(api.column(4).data().sum()) ;
                    $(api.column(5).footer()).html(curr_format(round(total_overall_amount,2)));
                    
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
                  if(data.payment_status !=1 && data.installment_status == 1 && data.payment_status !=3 && data.payment_status !=5){
                      $(row).addClass('text-danger');
                  }
                   <!-- payment is today(almost defaulting) installments -->
                  if(data.payment_status !=1 && data.installment_status == 2){
                      $(row).addClass('text-warning');
                  }

              },
            "columns": [
                      { data: "installment_number" },
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
                      { data: "payment_name" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt1 ="";
                        <?php if(in_array('13', $client_loan_privilege)){ ?>
                       <!--  ret_txt1 += "<a href='#installment_payment-modal' data-toggle='modal' class='pay_for_installment btn btn-sm' data-toggle='tooltip' title='Pay for this Installment'><i class='text-danger fa fa-money fa-fw'></i></a>"; -->
                      <?php } if(in_array('17', $client_loan_privilege)){ ?>
                        ret_txt1 += "<a href='#reschedule_payment-modal' data-toggle='modal' class='reschedule_loan btn btn-sm' data-toggle='tooltip' title='Reschedule Installment'><i class='text-danger fa fa-calendar fa-fw'></i></a>";
                        <?php } ?>
                        var ret_txt2 = "<a href='#' data-toggle='tooltip' title='Settled Installment'><i class=' text-success fa fa-check fa-fw'></i></a>";

                        return (full.payment_status==1 || full.payment_status==3)?ret_txt2:(full.payment_status==4)?ret_txt1:'';
                      }}
                   ],
                        buttons: <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('Loan Schedule'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
               }
            }
