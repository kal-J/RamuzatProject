if ($("#tblLoan_collateral").length && tabClicked === "tab-collateral") {
                if (typeof (dTable['tblLoan_collateral']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-collateral").addClass("active");
                    dTable['tblLoan_collateral'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_collateral'] = $('#tblLoan_collateral').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "initComplete": function( settings, json ){
                            loanDetailModel.guarantor_amount( sumUp( json.data, 'amount_locked' ) );
                         },
                        "ajax":{
                            "url": "<?php echo base_url('loan_collateral/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                              d.status_id = 1,
                              d.client_loan_id = <?php echo $loan_detail['id']; ?>
                            }
                        },
                        "columnDefs": [{
                                "targets": [4],
                                "orderable": false,
                                "searchable": false
                            }],
                "columns": [
                      { "data": "collateral_type_name" },
                      { "data": "description" },
                      { "data": "item_value",render:function(data, type,full ,meta ){
                            return curr_format(data*1);
                          } },
                      { "data": "file_name" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $client_loan_privilege)){ ?>
                        ret_txt +="<a href='#add_collateral-modal' data-toggle='modal' class='btn btn-sm edit_me'><i class='fa fa-edit'></i></a>";
                        <?php } if(in_array('7', $client_loan_privilege)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm text-danger  change_status' data-toggle='tooltip' title='Deactivate record'><i class='fa fa-ban'></i></a>";
                        <?php } ?>
                        return ret_txt;
                      }}
                   ],
                        buttons: <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('Loan Collateral'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
