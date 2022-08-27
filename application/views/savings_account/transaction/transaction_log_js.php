if ($("#tblTransaction_log").length && tabClicked === "tab-transaction_log" ) {
                if (typeof (dTable['tblTransaction_log']) !== 'undefined') {
            $(".savings").removeClass("active");
                    $(".tab-pane").removeClass("active");
                    $("#tab-transaction_log").addClass("active");
                    dTable['tblTransaction_log'].ajax.reload(null, true);
                } else {
                    dTable['tblTransaction_log'] = $('#tblTransaction_log').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        "lengthMenu": [[10, 25, 50, 100], [10, 25, 50, 100]],
                        "processing": true,
                        "serverSide": true,
                        "searchable": true,
                        "deferRender": true,
                        "orderFixed": {
                            "pre": [ 0, 'desc' ]
                        },
                        responsive: true,
                        ajax: {
							"url":"<?php echo site_url('Transaction/jsonList2/') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
									e.status_id = 3;
                                    <?php if(empty($acc_id)){ } else { ?>
									e.acc_id = '<?php echo $acc_id;?>';
                                    <?php } ?>
								}
						},
                        "columnDefs": [{
                                "targets": [0],
                                "visible": false,
                                "searchable": false
                            }],
                        rowCallback: function (row, data) {
                          if(data.status_id == 3){
                              $(row).addClass('text-danger');
                          }
                        },
                        columns: [ 
                            {data: 'transaction_date'},
                            {data: 'transaction_no', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='#'>" + data + "</a>";
                                }
                            },
                            {data: 'transaction_date',render:function( data, type, full, meta ){
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                return (data)?moment(data,'YYYY-MM-DD').format('DD-MMM-YYYY'):'';
                                }  
                            },
                            {data: 'account_no'},
                            {data: 'member_name'},
                             {data: 'debit', render: function (data, type, full, meta) {
                                   return curr_format(data*1);
                              }
                            },
                            {data: 'credit', render: function (data, type, full, meta) {
                                   return curr_format(data*1);
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
                            },
                            {data: 'type_name'},
                            {data: 'payment_mode'},
                            {data: 'narrative'}
                           
                        ],
                        buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Transactions'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

