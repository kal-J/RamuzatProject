if ($("#tblTransaction").length && tabClicked === "tab-transaction" ) {
                if (typeof (dTable['tblTransaction']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-transaction").addClass("active");
                    dTable['tblTransaction'].ajax.reload(null, true);
                } else {
                    dTable['tblTransaction'] = $('#tblTransaction').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[6, 'dsc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('u/Transaction/jsonList/') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
									e.state_id = '0';
                                    <?php if(empty($acc_id)){ } else { ?>
									e.acc_id = '<?php echo $acc_id;?>';
                                    <?php } ?>
								}
						},
                        "columnDefs": [{
                                "targets": [7],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [ 
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
                            {data: 'type_name'},
                            {data: 'account_no'},
                            {data: 'payment_mode'},
                            {data: 'narrative'},
                            {data: 'debit', render: function (data, type, full, meta) {
                                   return curr_format(data*1);
                              }
                            },
                            {data: 'credit', render: function (data, type, full, meta) {
                                   return curr_format(data*1);
                              }
                            },
                            {data: 'id', render: function (data, type, full, meta) {
                               var display_btn = "<div>";
                                if(full.transaction_type_id !== null && full.transaction_type_id !== ''){ 
                                display_btn += "<a href='<?php echo site_url(); ?>transaction/print_receipt/"+data+"' title='Print Receipt'><span class='fa fa-print text-danger'></span></a>";
                                } else { }
                                display_btn += "</div>";
                                return display_btn;
                                }
                            }
                        ],
                        buttons: getBtnConfig('Transactions'), 
                        responsive: true
                    });
                }
            }


