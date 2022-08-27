if ($("#tblTransaction").length && tabClicked === "tab-transaction" ) {
                if (typeof (dTable['tblTransaction']) !== 'undefined') {
            $(".savings").removeClass("active");
                    $(".tab-pane").removeClass("active");
                    $("#tab-transaction").addClass("active");
                    dTable['tblTransaction'].ajax.reload(null, true);
                } else {
                    dTable['tblTransaction'] = $('#tblTransaction').DataTable({
                        "dom": '<"html5buttons"B>lTfgirtp',
                        "lengthMenu": [[10, 25, 50, 100, 500, -1], [10, 25, 50, 100, 500, "All"]],
                        "processing": true,
                        "serverSide": true,
                        "language": {
                          processing: '<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span> '
                        },
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
                                    e.start_date = $('#start_date').val() ? moment($('#start_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
                                    e.end_date = $('#end_date').val() ? moment($('#end_date').val(), 'DD-MM-YYYY').format('YYYY-MM-DD') : '';
									e.status_id = 1;
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
                              $(row).addClass('text-danger strikethrough');
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
                                return (data)?moment(data,'YYYY-MM-DD HH:mm:ss').format('DD-MMM-YYYY HH:mm:ss'):'';
                                }  
                            },
                            {data: 'account_no'},
                            {   
                                data: 'member_name', render: function(data, type, full, meta) {
                                    if((parseInt(full.client_type) == 2) && full.group_name) {
                                        return full.group_name;
                                    }

                                    return data;
                                }
                            },
                             {data: 'debit', render: function (data, type, full, meta) {
                                   return curr_format(data*1);
                              }
                            },
                            {data: 'credit', render: function (data, type, full, meta) {
                                   return curr_format(data*1);
                              }
                            },
                            {data: 'end_balance', render: function (data, type, full, meta) {
                                   return "<b>"+curr_format(data*1)+"</b>";
                              }
                            },
                            {data: 'type_name'},
                            {data: 'payment_mode'},
                            {data: 'narrative'},
                           
                            
                            {data: 'id', render: function (data, type, full, meta) {
                               var display_btn = "<div>";

                                
                                <?php if(in_array('6', $savings_privilege)){ ?>
                                display_btn += "<a target='_blank' href='<?php echo site_url();?>transaction/print_receipt/"+data+"/"+full.client_type+"' title='Print Receipt'><span class='fa fa-print text-danger'></span></a>";
                                 <?php 
                                

                             } if(in_array('26', $savings_privilege)){ ?>
                                if(full.transaction_type_id <5){ 
                                 display_btn += "<a href='#edit_transaction-modal'  title='Edit transaction date or narrative' data-toggle='modal' class='btn btn-sm edit_me'><i class='text-warning fa fa-pencil'></i></a>";
                                 } else { }
                                 if((full.transaction_type_id <4) && (full.ref_no === null)){ 
                                 display_btn += "<a href='#reverse-modal' title='Reverse this Transaction' data-toggle='modal' class='btn btn-sm edit_me2'><i class='text-danger fa fa-undo'></i></a>";
                                 } else { }
                                <?php } ?>

                                display_btn += "</div>";
                                return display_btn;
                                }
                            }
                         
                        ],
                        buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Transactions'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }

$('table tbody').on('click', 'tr .edit_me2', function (e) {
    e.preventDefault();
    var row = $(this).closest('tr');
    var tbl = row.parent().parent();
    var tbl_id = $(tbl).attr("id");
    var dt = dTable[tbl_id];
    var data = dt.row(row).data();
    if (typeof (data) === 'undefined') {
        data = dt.row($(row).prev()).data();
        if (typeof (data) === 'undefined') {
            data = dt.row($(row).prev().prev()).data();
        }
    }
    var formId = tbl_id.replace("tbl", "formReverse");
    edit_data(data, formId);
});

