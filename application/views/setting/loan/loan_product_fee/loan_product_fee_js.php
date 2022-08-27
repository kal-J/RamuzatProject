if ($("#tblLoan_product_fee").length) {
                    dTable['tblLoan_product_fee'] = $('#tblLoan_product_fee').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[0, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('loan_product_fee/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.status_id = 1,
                            d.loanproduct_id = "<?php echo $loan_product['id']?>"
                            }
                        },
            "columns": [
                      
                      { "data": "feename" },
                      { "data": "feetype" },
                      { "data": "amountcalculatedas" },
                      { "data": "amount" , render:function ( data, type, full, meta ) {
                        return (full.amountcalculatedas_id==2)?"UGX "+curr_format(data*1):((data*1)+"%"); 
                      }},
                      {"data": 'income_account'},
                      {"data": 'income_receivable_account'},
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                       <?php if(in_array('3', $loan_product_privilege)){ ?>
                        ret_txt +="<a href='#add_loan_product_fee-modal' data-toggle='modal' class='btn btn-sm edit_me'><i class='fa fa-edit'></i></a>";
                       <?php } if(in_array('7', $loan_product_privilege)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm change_status' data-toggle='tooltip' title='Delete record'><i class='text-danger fa fa-trash'></i></a>";
                       <?php } ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $loan_product_privilege)){ ?> getBtnConfig('Loan Product Fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
