if ($("#tblClient_loan_guarantor").length && tabClicked === "tab-guarantor") {
                if (typeof (dTable['tblClient_loan_guarantor']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-guarantor").addClass("active");
                    dTable['tblClient_loan_guarantor'].ajax.reload(null, true);
                } else {
                    dTable['tblClient_loan_guarantor'] = $('#tblClient_loan_guarantor').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "initComplete": function( settings, json ){
                            loanDetailModel.guarantor_amount( sumUp( json.data, 'amount_locked' ) );
                         },
                        "ajax":{
                            "url": "<?php echo base_url('client_loan_guarantor/jsonList/'); ?>",
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
                          { "data": "guarantor_name" },
                          { "data": "amount_locked", render:function(data, type,full ,meta ){
                            return curr_format(data*1);
                          } },
                          { "data": "account_no" },
                          { "data": "relationship_type" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $client_loan_privilege)){ ?>
                         ret_txt +="<a href='#add_guarantor-modal' data-toggle='modal' class='btn btn-sm edit_me'><i class='fa fa-edit'></i></a>";
                         <?php } if(in_array('4', $client_loan_privilege)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm change_status' data-toggle='tooltip' title='Delete record'><i class='text-danger fa fa-trash'></i></a>";
                        <?php } ?>
                        return ret_txt;
                      }}
                   ],
                        buttons: <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('Loan Guarantor'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
