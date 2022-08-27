if ($("#tblInterest_Payment_points").length && tabClicked === "tab_interest_payment_point") {
                if (typeof (dTable['tblInterest_Payment_points']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab_interest_payment_point").addClass("active");
                    dTable['tblInterest_Payment_points'].ajax.reload(null, true);
                } else {
                    dTable['tblInterest_Payment_points'] = $('#tblInterest_Payment_points').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo base_url('Interest_Payment_points/jsonList') ?>",
							"dataType":"json",
							"type":"POST",
							"data":{
							funtion(){
								status_id='1';
								}
						}
						},
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [     
                            {data: 'interest_point_name'},
                            {data: 'interest_point_description'},
                            {data: 'id', render: function (data, type, full, meta) {
                                    var display_btn = "<div class='btn-grp'>";
                                    <?php if(in_array('3', $privileges)){ ?>
                                    display_btn += "<a href='#add_interest_payment_method_modal' data-toggle='modal' class='btn btn-sm edit_me' title='Savings fees details'><i class='fa fa-edit'></i></a>";
                                  <?php } if(in_array('7', $privileges)){ ?>
                                    display_btn += '<a href="#" title="Delete saving fee"><span class="fa fa-trash text-default change_status"></span></a>';
                                  <?php } ?>
                                    display_btn += "</div>";
									
                                    return display_btn;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Interest_Payment_points'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
