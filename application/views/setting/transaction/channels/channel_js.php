if ($("#tblTransactionChannel").length && tabClicked === "tab-transaction_channel") {
                if (typeof (dTable['tblTransactionChannel']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-transaction_channel").addClass("active");
                    dTable['tblTransactionChannel'].ajax.reload(null, true);
                } else {
                    dTable['tblTransactionChannel'] = $('#tblTransactionChannel').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[0, 'asc']],
                        deferRender: true,   
                        ajax:{
                                "url":  "<?php echo site_url('TransactionChannel/jsonList'); ?>",
                                "dataType": "json",
                                "type": "POST",
                                "data": function(d){
                                d.status_id ='1';
                                }
                            },
                        "columnDefs": [{
                                "targets": [4],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'channel_name'},
                            {data: 'staff_name'},
                            {data: 'account_name', render: function (data, type, full, meta) {
                                    var ret_txt = '<a href="<?php echo site_url("accounts/view"); ?>/'+full.linked_account_id+'" title="View account transactions">'+"["+full.account_code+ "]  "+data+'</a>';
                                    return ret_txt;
                                }
                            },
                            {data: 'description'},
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn ="";
                                display_btn += "<div class='btn-grp'>";
                              <?php if(in_array('7', $privileges)){ ?>
                                 display_btn += "<a href='#' class='btn btn-sm change_status' title='Deactivate Transaction Channel details'><i class='fa fa-ban'></i></a>";
                              <?php } if(in_array('3', $privileges)){ ?>
                                 display_btn  += "<a href='#add_transaction_channel' data-toggle='modal' class='btn btn-sm edit_me' title='Update Transaction Channel details'><i class='fa fa-edit'></i></a>";
                              <?php } if(in_array('4', $privileges)){ ?>
                                 display_btn += '<a href="#" title="Delete Transaction Channel"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                 <?php } ?>
                                    display_btn += "</div>";
                                return display_btn;
                                }
                            }
                        ],
                        "buttons": <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Transaction Channel'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }