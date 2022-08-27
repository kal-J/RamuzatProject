if ($("#tblLoan_approvals").length && tabClicked === "tab-loan_approvals") {
                if (typeof (dTable['tblLoan_approvals']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_approvals").addClass("active");
                    dTable['tblLoan_approvals'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_approvals'] = $('#tblLoan_approvals').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        "ajax":{
                            "url": "<?php echo base_url('loan_approval/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.status_id=1,
                             d.client_loan_id = <?php echo $loan_detail['id']; ?>,
                             d.loan_state_id = <?php echo $loan_detail['id']; ?>
                            }
                        },
            "columns": [
                      //{ data: "loan_approved_id"},
                      { data: "action_date", render:function( data, type, full, meta ){
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                        return (!(data=='0000-00-00'))?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
                          }  },
                      { data: "staff_name"},
                      { data: "suggested_disbursement_date", render:function( data, type, full, meta ){
                          if (type === "sort" || type === "filter") {
                              return data;
                          }
                        return (!(data=='0000-00-00'))?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
                          }  },
                      { data: "amount_approved", render:function( data, type, full, meta ){
                          return curr_format(data*1);}
                      },
                      { data: "comment"},
                      {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt ="";
                                <?php if(in_array('6', $client_loan_privilege)){ ?>
                                    ret_txt += "<a href='<?php echo base_url(); ?>Loan_approval/pdf_approval/<?php echo $loan_detail['id']; ?>'  target = '_blank' class='btn btn-primary aquaBtn' >Approval docs</a>&nbsp;";
                                <?php //}if(in_array('4', $client_loan_privilege)){ ?>
                                    <!-- ret_txt += '<a href="#" data-toggle="modal"   title="delete loan approved" class="delete_me"><i class="fa fa-trash"  style="color:#bf0b05"></i></a>'; -->
                                <?php  } ?>
                                return ret_txt;
                            }
                      }
                   ],
                        buttons: getBtnConfig('Loan Approvals'),
                        responsive: true
                    });
               }
            }