if ($("#tblSms_settings").length && tabClicked === "tab-sms_settings") {
                if (typeof (dTable['tblSms_settings']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-sms_settings").addClass("active");
                    dTable['tblSms_settings'].ajax.reload(null, true);
                } else {
                    dTable['tblSms_settings'] = $('#tblSms_settings').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('payment_engine/smsjsonList') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
								e.organisation_id = <?php echo $organisation['id']; ?>;
								}
						},
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                        {data: 'name'},
                        {data: 'api_key'},
                       
                        {data: 'status_id', render:function ( data, type, full, meta ) {
                            if(parseInt(data)===1){
                                var stat="Active";
                                return '<a href="#" role="button" class="btn btn-flat btn-success btn-xs" >'+ stat +'</a>';
                            }else if(parseInt(data)===2){
                                var stat="Inactive";
                                return '<a href="#" role="button" class="btn btn-flat btn-warning btn-xs" >'+ stat +'</a>';
                            }else{
                                var stat ="Deactivated";
                                return '<a href="#" role="button" class="btn btn-flat btn-danger btn-xs" >'+ stat +'</a>';
                            }
                           
                        }},
                        { data: 'id', render: function(data, type, full, meta) {
                            var display_btn = "<div class='btn-grp'>";
                            <?php if(in_array('3', $privileges)){ ?>
                                display_btn += "<a href='#sms_settings-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Edit SMS settings'><i class='fa fa-edit'></i></a>";
                            <?php } ?>
                                display_btn += "</div>";
                                return display_btn; 
                            }
                        }
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Payment Engines'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }