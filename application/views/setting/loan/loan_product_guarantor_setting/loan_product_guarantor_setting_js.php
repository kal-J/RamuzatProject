if ($("#tblLoan_product_guarantor_setting").length) {
                    dTable['tblLoan_product_guarantor_setting'] = $('#tblLoan_product_guarantor_setting').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[0, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('loan_product_guarantor_setting/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.status_id='1';
                            d.loan_product_id = "<?php echo $loan_product['id']?>"
                            }
                        },
            "columns": [
                      { "data": "setting" },
                      { "data": "description" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                    <?php if(in_array('3', $guarantor_privilege)){ ?>
                        ret_txt +="<a href='#add_loan_product_guarantor_setting-modal' data-toggle='modal' class='btn btn-sm edit_me'><i class='fa fa-edit'></i></a>";
                        <?php } if(in_array('7', $guarantor_privilege)){ ?> 
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm change_status' data-toggle='tooltip' title='Delete record'><i class='text-danger fa fa-trash'></i></a>";
                        <?php }  ?> 
                        return ret_txt;
                      }}
                   ],
                        buttons: <?php if(in_array('6', $guarantor_privilege)){ ?> getBtnConfig('Loan Product Guarantor Setting'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }