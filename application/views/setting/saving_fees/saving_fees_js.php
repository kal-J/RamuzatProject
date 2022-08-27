if ($("#tblSaving_fees").length && tabClicked === "tab_saving_fees") {
                if (typeof (dTable['tblSaving_fees']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab_saving_fees").addClass("active");
                    dTable['tblSaving_fees'].ajax.reload(null, true);
                } else {
                    dTable['tblSaving_fees'] = $('#tblSaving_fees').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[0, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo base_url('Saving_fees/jsonList') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
								e.status_id = '1';
								}
						    
						},
                        "columnDefs": [{
                                "targets": [5],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'feename'},
                            {data: "amountcalculatedas"},
                            {data: 'amount', render: function (data, type, full, meta) {
                            if(full.cal_method_id==2){ 
                              return "UGX "+curr_format(data*1);
                              } else if(full.cal_method_id==3){
                              return "<a href='#view_saving_ranges' data-toggle='modal' class='btn btn-sm edit_me' title='View fee ranges' >View ranges</a>";
                              } else {
                              return ((data*1)+"%");
                            }}

                            }, 
                            {data: 'fee_type', render: function (data, type, full, meta) {
                                if(data=='M'){
                                    return "Mandatory";
                                }else if(data =='O'){
                                    return "Optional";
                                }else{
                                    return "";
                                }
                                }
                            },
                            {data: 'charge_trigger_name'},
                          
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn ="";
                                display_btn += "<div class='btn-grp'>";
                                <?php if(in_array('3', $deposit_product_privilege)){ ?>
                                    display_btn += "<a href='#add_deposit_product_fee-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Savings fees details'><i class='fa fa-edit'></i></a>";
                                    <?php } if(in_array('4', $deposit_product_privilege)){ ?>
                                    display_btn += '<a href="#" title="Delete saving fee"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                    <?php } ?>
                                    display_btn += "</div>";
                                    return display_btn;
                                }
                            }
                        ],
                        "buttons": <?php if(in_array('6', $deposit_product_privilege)){ ?> getBtnConfig('Saving fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
