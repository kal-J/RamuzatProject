if ($("#tblClient_loan_monthly_income").length && tabClicked === "tab-monthly_income") {
                if (typeof (dTable['tblClient_loan_monthly_income']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-monthly_income").addClass("active");                    
                    dTable['tblClient_loan_monthly_income'].ajax.reload(null, true);
                } else {
                    dTable['tblClient_loan_monthly_income'] = $('#tblClient_loan_monthly_income').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('client_loan_monthly_income/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                              d.status_id = 1,
                              d.client_loan_id = <?php echo $loan_detail['id']; ?>
                            }
                        },
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                "columns": [
                      { "data": "income_type" },
                      { data: "amount", render:function( data, type, full, meta ){
                          return curr_format(data*1);} 
                      },
                      { "data": "description" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $client_loan_privilege)){ ?>
                         ret_txt +="<a href='#add_monthly_income-modal' data-toggle='modal' class='btn btn-sm edit_me'><i class='fa fa-edit'></i></a>";
                         <?php  } if(in_array('4', $client_loan_privilege)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm change_status' data-toggle='tooltip' title='Delete record'><i class='text-danger fa fa-trash'></i></a>";
                        <?php } ?>
                        return ret_txt;
                      }}
                   ],
                        buttons: <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('Monthly income'), <?php  } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
