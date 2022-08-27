if ($("#tblTransaction").length && tabClicked === "tab-transactions" ) {
                if (typeof (dTable['tblTransaction']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-transactions").addClass("active");
                    dTable['tblTransaction'].ajax.reload(null, true);
                } else {
                    dTable['tblTransaction'] = $('#tblTransaction').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Transaction/jsonList') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
									e.state_id = '0';
								}
						},
                        "columnDefs": [{
                                "targets": [8],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [ 
                            {data: 'transaction_no'},
                            {data: 'account_no', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('Transaction/view'); ?>/" + full.id + "' title='View transactions details'>" + data + "</a>";
                                }
                            },
                            {data: 'amount', render: function (data, type, full, meta) {
                                 return curr_format(data*1);
                                 }
                            },
                            {data: 'type_name'},
                            {data: 'channel_name'},
                            {data: 'charge'},
                            {data: 'narrative'},
                            {data: 'transaction_date'},
                            {data: 'id', render: function (data, type, full, meta) {
                                    display_btn = "<a href='<?php echo site_url('Transaction/view'); ?>/" + full.id + "' title='View transaction details'>" + "<span class='fa fa-edit'></span>" + "</a>";
                                    <?php if(in_array('7', $savings_privilege)){ ?>
                                    display_btn += '<a href="#" title="Revoke Transaction"><span class="fa fa-trash text-danger change_status"></span></a>';
                                    <?php } ?>
                                    display_btn += "</div>";
                                    return display_btn;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Deposits'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }


