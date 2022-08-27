if ($("#tblInterestCalMethod").length && tabClicked === "tab-interest_cal_method") {
                if (typeof (dTable['tblInterestCalMethod']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-transaction_channel").addClass("active");
                    dTable['tblInterestCalMethod'].ajax.reload(null, true);
                } else {
                    dTable['tblInterestCalMethod'] = $('#tblInterestCalMethod').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
                            "url": "<?php echo site_url('InterestCalMethod/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function(d){
                            d.status_id ='1';
                            }
                         },
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'interest_method'},
                            {data: 'description'},
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn ="";
                                display_btn += "<div class='btn-grp'>";
                                <?php if(in_array('7', $deposit_product_privilege)){ ?>
                                 var display_btn = "<a href='#' class='btn btn-sm change_status' title='Deactivate interest calculation method details'><i class='fa fa-ban'></i></a>";
                                 <?php } if(in_array('3', $deposit_product_privilege)){ ?> 
                                 display_btn  += "<a href='#add_interest_cal_method' data-toggle='modal' class='btn btn-sm edit_me' title='Update interest calculation method details'><i class='fa fa-edit'></i></a>";
                                 <?php } if(in_array('4', $deposit_product_privilege)){ ?> 
                                 display_btn += '<a href="#" title="Delete interest calculation method"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                 <?php }  ?> 
                                    display_btn += "</div>";
                                return display_btn;
                                }
                            }
                        ],
                        "buttons": <?php if(in_array('6', $deposit_product_privilege)){ ?> getBtnConfig('interest Calculation Method'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }