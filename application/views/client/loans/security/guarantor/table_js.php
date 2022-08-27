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
                                "targets": [3],
                                "orderable": false,
                                "searchable": false
                            }],
                "columns": [
                          { "data": "guarantor_name" },
                          { "data": "amount_locked", render:function(data, type,full ,meta ){
                            return curr_format(data*1);
                          } },
                          { "data": "account_no" },
                          { "data": "relationship_type" }
                   ],
                        buttons: getBtnConfig('Loan Guarantor'), 
                        responsive: true
                    });
                }
            }
