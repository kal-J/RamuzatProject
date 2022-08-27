if ($("#Portfolio_aging").length && tabClicked === "tab-loan-provision" ) {
                if (typeof (dTable['Portfolio_aging']) !== 'undefined') {
                   $(".tab-loan-provision").removeClass("active");
                    $("#tab-loan-provision").addClass("active");
                    dTable['Portfolio_aging'].ajax.reload(null, true);
                } else {
                    dTable['Portfolio_aging'] = $('#Portfolio_aging').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[3, 'dsc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Portfolio_aging/jsonList') ?>",
							"dataType":"json",
							"type":"POST",
                            "data": function(d){
                             d.status_id=1;
                               
						     } 
                    },
                        "columnDefs": [{
                                
                                "orderable": false,
                                "searchable": true
                            }],
                       
                       columns: [ 
                            {"data": "start_range_in_days"},
                            {"data": "end_range_in_days"},
                            {"data": "name"},
                            {"data": "description"},
                            {"data": "provision_percentage"},
                            {"data": "provision_loan_loss_account_id"},

                            {"data": 'status_id', render: function (data, type, full, meta) {
                                var ret_txt ="";
                               
                                <?php if(in_array('3', $role_privilege)){ ?>
                                ret_txt += "<a href='#loan_provision_setting-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me' title='Update Loan Provision details'><i class='fa fa-edit '></i></a>";
                              <?php } if(in_array('7', $role_privilege)){ ?>
                                    var title_text = parseInt(data)===1?"De":"A";
                                    var fa_class = parseInt(data)===1?"ban":"undo";
                                    var icon_color = parseInt(data)===1?"warning":"default";
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status" title="'+title_text+'ctivate role"><i class="fa fa-'+fa_class+' text-'+icon_color+'"></i></a>';
                               <?php } if(in_array('4', $role_privilege)){ ?>
                                   ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                                <?php } ?>
                              
                                   return ret_txt;
                                }
                            }
                           
                          
                               
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Shares Transactions'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

