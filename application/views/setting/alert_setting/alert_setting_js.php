if ($("#tblAlert_setting").length && tabClicked === "tab-alert-setting" ) {
                if (typeof (dTable['tblAlert_setting']) !== 'undefined') {
                   $(".tab-alert-setting").removeClass("active");
                    $("#tab-alert-setting").addClass("active");
                    dTable['tblAlert_setting'].ajax.reload(null, true);
                } else {
                    dTable['tblAlert_setting'] = $('#tblAlert_setting').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[3, 'dsc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Alert_setting/JsonList') ?>",
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
                            {"data": "alert_method", render: function (data, type, full, meta) {
                                    if (full.alert_method == 1) {
                                        return "Email";
                                    }
                                     
                                    return "SMS";
                                }
                            },
                             {"data": "alert_type", render: function (data, type, full, meta) {
                                    if (full.alert_type == 1) {
                                        return "General";
                                    }
                                      if (full.alert_type == 2) {
                                        return "Due loan installment";
                                    }
                                    if (full.alert_type == 3) {
                                        return "Due fees";
                                    }
                                    return "Loans in arrears";
                                }
                            },
                            {data: 'number_of_days_to_duedate'},
                          

                             {"data": "interval_of_reminder", render: function (data, type, full, meta) {
                                    if (full.interval_of_reminder == 1) {
                                        return "Day(s)";
                                    }
                                      if(full.interval_of_reminder == 2) {
                                        return "Week(s)";
                                    }
                                   
                                    return "Month(s)";
                                }
                            },
                          
                               
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Shares Transactions'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

