if ($("#tblTransactionDateControl").length && tabClicked === "tab-transaction-date-control") {
                if (typeof (dTable['tblTransactionDateControl']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-transaction-date-control").addClass("active");
                    dTable['tblTransactionDateControl'].ajax.reload(null, true);
                } else {
                    dTable['tblTransactionDateControl'] = $('#tblTransactionDateControl').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[0, 'asc']],
                        deferRender: true,   
                        ajax:{
                                "url":  "<?php echo site_url('Transaction_date_control/jsonList'); ?>",
                                "dataType": "json",
                                "type": "POST",
                                "data": function(d){
                                d.status_id ='1';
                                }
                            },
                        columns: [
                            {data: 'control_name'},
                            {data: 'staff_name'},
                            {data: 'description'},
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn ="";
                                display_btn += "<div class='btn-grp'>";
                              <?php if(in_array('7', $privileges)){ ?>
                                 display_btn += "<a href='#' class='btn btn-sm change_status' title='Deactivate Transaction Date control'><i class='fa fa-ban'></i></a>";
                              <?php } if(in_array('3', $privileges)){ ?>
                                 display_btn  += "<a href='#add_transaction_date_control' data-toggle='modal' class='btn btn-sm edit_me' title='Update Transaction Date'><i class='fa fa-edit'></i></a>";
                              <?php } if(in_array('4', $privileges)){ ?>
                                 display_btn += '<a href="#" title="Delete Transaction Date Control"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                 <?php } ?>
                                    display_btn += "</div>";
                                return display_btn;
                                }
                            }
                        ],
                        "buttons": <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Transaction Date Control'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }