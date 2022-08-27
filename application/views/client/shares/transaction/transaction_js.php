if ($("#tblShare_transaction").length && tabClicked === "tab-share_transaction" ) {
                if (typeof (dTable['tblShare_transaction']) !== 'undefined') {
                    $(".applications").removeClass("active");
                    $(".accounts").removeClass("active");
                    $("#tab-transaction").addClass("active");
                    dTable['tblShare_transaction'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_transaction'] = $('#tblShare_transaction').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[6, 'dsc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Share_transaction/jsonList/') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
                                    e.start_date = $('#start_date').val() ? moment($('#start_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                                    e.end_date = $('#end_date').val() ? moment($('#end_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                                    e.state_id = '0';
									e.status_id = 1;
									 <?php if(isset($get_share_by_id)): ?>e.acc_id =<?php echo $get_share_by_id['id']; ?>; <?php endif; ?>
								}
						},
                        "columnDefs": [{
                                "targets": [6],
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
                            {data: 'payment_mode'},
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
                            
                        ],
                        buttons: getBtnConfig('Shares Transactions'),
                        responsive: true
                    });
                }
            }


