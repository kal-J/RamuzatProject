if ($("#tblPayment_engine").length && tabClicked === "tab-payment_engine") {
                if (typeof (dTable['tblPayment_engine']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-payment_engine").addClass("active");
                    dTable['tblPayment_engine'].ajax.reload(null, true);
                } else {
                    dTable['tblPayment_engine'] = $('#tblPayment_engine').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('payment_engine/jsonList') ?>",
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
                        {data: 'link', render: function (data, type, full, meta) {
                            
                          var link="<a href='"+ data +"' target='_blank' title='View this api details'>Api\'s home page</a>";
                          return link;
                        }},
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
                                display_btn += "<a href='#payment_engine-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Edit payment selection'><i class='fa fa-edit'></i></a>";
                            <?php } if(in_array('7', $privileges)){ ?>
                                if(parseInt(full.status_id) ===2){
                                display_btn += '<a href="#" title="Activate Payment engine"><span class="fa fa-check-circle text-warning change_status"></span></a>';
                                } else if (parseInt(full.status_id) ===1){
                                display_btn += '<a href="#" style="padding-left:15px;"title="Deactivate Fiscal Year"><span class="fa fa-ban text-warning change_status"></span></a>';
                                } 
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