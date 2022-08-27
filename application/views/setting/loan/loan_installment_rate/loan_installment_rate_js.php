if ($("#tblLoan_installment_rate").length && tabClicked === "tab-loan_installment_rate") {
                if (typeof (dTable['tblLoan_installment_rate']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_installment_rate").addClass("active");
                    dTable['tblLoan_installment_rate'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_installment_rate'] = $('#tblLoan_installment_rate').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('loan_installment_rate/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.user_id = 1,
                            d.active_id = 1
                            }
                        },
            "columns": [
                      { "data": "loan_installment_rate" },
                      { "data": "loan_installment_unit" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $privileges)){ ?>
                        ret_txt +="<a href='#add_loan_installment_rate-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me'><i class='fa fa-edit'></i></a>";
                        <?php } if(in_array('7', $privileges)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm btn-default change_status' data-toggle='tooltip' title='Deactivate record'><i class='fa fa-ban'></i></a>";
                        <?php } if(in_array('4', $privileges)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm btn-default delete_me' data-toggle='tooltip' title='Delete record'><i class='fa fa-trash'></i></a>";
                        <?php }  ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Loan Installment Rates'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }