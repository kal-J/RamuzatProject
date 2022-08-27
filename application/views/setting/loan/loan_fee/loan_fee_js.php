if ($("#tblLoan_fee").length && tabClicked === "tab-loan_fee") {
                if (typeof (dTable['tblLoan_fee']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_fee").addClass("active");
                    dTable['tblLoan_fee'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_fee'] = $('#tblLoan_fee').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('loan_fee/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.status_id = 1
                            }
                        },
            "columns": [
                      { "data": "feename" },
                      { "data": "feetype" },
                      { "data": "amountcalculatedas"},
                      { "data": "amount", render:function ( data, type, full, meta ) {
                          if(full.amountcalculatedas_id==2){ 
                          return "UGX "+curr_format(data*1);
                          } else if(full.amountcalculatedas_id==3){
                          return "<a href='#view_loan_ranges' data-toggle='modal' class='btn btn-sm edit_me' title='View fee ranges' >View ranges</a>";
                          } else {
                          return ((data*1)+"%");
                          }}

                       },
                      {"data": 'charge_trigger_name'},
                      {"data": 'income_account'},
                      {"data": 'income_receivable_account'},
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $loan_product_privilege)){ ?>
                        ret_txt += "<a href='#add_loan_fee-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Edit Laon Fees' ><i class='fa fa-edit'></i></a>";
                        <?php }  if(in_array('4', $loan_product_privilege)){ ?>
                        ret_txt += '<a href="#" data-toggle="modal"   title="delete subscription details" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-trash"  style="color:#bf0b05"></i></a>';
                        <?php } ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $loan_product_privilege)){ ?> getBtnConfig('Loan Fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }