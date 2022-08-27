if ($("#tblTransaction_charges").length && tabClicked === "tab-charges" ) {
                if (typeof (dTable['tblTransaction_charges']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-charges").addClass("active");
                    dTable['tblTransaction_charges'].ajax.reload(null, true);
                } else {
                    dTable['tblTransaction_charges'] = $('#tblTransaction_charges').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Transaction_charges/jsonList') ?>",
							"dataType":"json",
							"type":"POST",
							"data":
							function(e){
									e.acc_id = '<?php echo $acc_id;?>';
                                    
								}
						},
                        "columnDefs": [{
                                "targets": [4],
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
                            {data: 'type_name'},
                            {data: 'charge_amount', render: function (data, type, full, meta) {
                                    return curr_format(data*1);
                              
                                }},
                            {data: 'cal_method_id', render: function (data, type, full, meta) {
                                if(data==1){
                                    return "Percentage";
                                }else{
                                    return "Fixed Amount";
                                }
                            }
                            },
                            {data: 'date_created',render:function( data, type, full, meta ){
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                return (data)?moment(data,'X').format('D-MMM-YYYY'):'';
                                } }
                        ],
                        buttons:  getBtnConfig('Charges'),
                        responsive: true
                    });
                }
            }


