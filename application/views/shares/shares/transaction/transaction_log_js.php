if ($("#tblShare_transaction_log").length && tabClicked === "tab-transaction_log" ) {
                if (typeof (dTable['tblShare_transaction_log']) !== 'undefined') {
                   $(".tab-pane").removeClass("active");
                    $("#tab-transaction_log").addClass("active");
                    dTable['tblShare_transaction_log'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_transaction_log'] = $('#tblShare_transaction_log').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[6, 'dsc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Share_transaction/jsonList/') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
                                    e.state_id = '0';
									e.status_id = 3;
									 <?php if(isset($get_share_by_id)): ?>e.acc_id =<?php echo $get_share_by_id['id']; ?>; <?php endif; ?>
								}
                            },
						 
                        "columnDefs": [{
                                "targets": [9],
                                "orderable": false,
                                "searchable": false
                            }],
                        rowCallback: function (row, data) {
                          if(data.status_id == 3){
                              $(row).addClass('text-danger');
                          }
                        },
                        columns: [ 
                            {data: 'transaction_no', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='#'>" + data + "</a>";
                                }
                            },
                            {data: 'payment_mode'},
                            {data: 'type_name'},
                            {data: 'share_account_no'},
                            {data: 'narrative'},
                            {data: 'debit', render: function (data, type, full, meta) {
                                 return curr_format(data*1);
                                 }
                            },
                            {data: 'credit', render: function (data, type, full, meta) {
                                 return curr_format(data*1);
                                 }
                            },
                            {data: 'date_created',render:function( data, type, full, meta ){
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                return (data)?moment(data,'X').format('D-MMM-YYYY'):'';
                                }  
                            },
                             {data: 'reverse_msg', render: function (data, type, full, meta) {
                                    return data;
                                },
                                createdCell: function (td, cellData, rowData, row, col) {
                                   if (rowData.status_id==3) $(td).css('text-decoration','none');
                                }
                            },
                            {data: 'reversed_date', render: function (data, type, full, meta) {
                                    if (type == 'sort') {
                                        return moment(data, 'YYYY-MM-DD').format('X');
                                    }
                                    return data ? moment(data, 'YYYY-MM-DD').format('D-MMM-YYYY') : '';
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Shares Transactions'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

