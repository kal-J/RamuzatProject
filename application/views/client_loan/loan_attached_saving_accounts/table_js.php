if ($("#tblLoan_attached_saving_accounts").length && tabClicked === "tab-loan_attached_saving_acc") {
                if (typeof (dTable['tblLoan_attached_saving_accounts']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-loan_attached_saving_acc").addClass("active");
                    dTable['tblLoan_attached_saving_accounts'].ajax.reload(null, true);
                } else {
                    dTable['tblLoan_attached_saving_accounts'] = $('#tblLoan_attached_saving_accounts').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('loan_attached_saving_accounts/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                             d.status_id = 1,
                             d.loan_id = <?php echo $loan_detail['id']; ?>
                             <?php if (!empty($loan_detail['member_id'])) { ?>
                             ,d.member_id =<?php echo $loan_detail['member_id'];?>
                              <?php } else if(!empty($loan_detail['group_id'])){ ?>
                             ,d.group_id=<?php echo $loan_detail['group_id'];?>
                             <?php } ?>
                            }
                        },
                         "columnDefs": [{
                                "targets": [1],
                                "orderable": false,
                                "searchable": false
                            }],
            "columns": [
                      { "data": "account_no" },
                      {"data": "id", render: function (data, type, full, meta) {
                        var ret_txt ="";
                            <?php if(in_array('4', $client_loan_privilege)){ ?>
                            ret_txt += '<a href="#" data-toggle="modal"   title="delete attached account details" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-trash"  style="color:#bf0b05"></i></a>';
                            <?php } ?>
                            return ret_txt;
                       }}

                   ],
                        buttons: <?php if(in_array('6', $client_loan_privilege)){ ?> getBtnConfig('Attached savings account'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
