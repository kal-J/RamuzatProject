if ($("#tblShare_transaction").length && tabClicked === "tab-transaction" ) {
                if (typeof (dTable['tblShare_transaction']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-transaction").addClass("active");
                    dTable['tblShare_transaction'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_transaction'] = $('#tblShare_transaction').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[2, 'dsc']],
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
                              {data: 'firstname', render: function (data, type, full, meta) {
                                  if(full.group_name) {
                                      return full.group_name;
                                  }
                                    return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.member_id + "'>" + data + "  " + full.lastname + "  " + full.othernames + "</a>";
                                }},
                             {data: 'transaction_date',render:function( data, type, full, meta ){
                            if (type === "sort" || type === "filter") {
                            return data;
                            }
                                return (data)?moment(data,'YYYY-MM-DD HH:mm:ss').format('DD-MMM-YYYY HH:mm:ss'):'';
                            }
                            },
                            {data: 'payment_mode'},
                           
                            {data: 'type_name'},
                            {data: 'share_account_no'},
                           
                            {data: 'debit', render: function (data, type, full, meta) {
                                 return curr_format(data*1);
                                 }
                            },
                            {data: 'credit', render: function (data, type, full, meta) {
                                 return curr_format(data*1);
                                 }
                            },
                             {data: 'narrative'},

                            {data: 'id', render: function (data, type, full, meta) {
                               var display_btn = "<span>";
                                 if(full.ref_no === null){ 
                                display_btn += "<a href='#reverse-modal' title='Reverse this Transaction' data-toggle='modal' class='btn btn-sm edit_me2'><i class='text-danger fa fa-undo'></i></a>";
                               }
                                display_btn += "</span>";
                                
                                let print_receipt_url = "<?php echo site_url('shares/print_receipt'); ?>" + `/${full.share_account_no}/${full.id}`;
                                let btn_receipt = `
                                    <span style="width: fit-content;" title="Print receipt">
                                        <form class="ml-1 d-inline" action=${print_receipt_url} method="post">
                                        <input name="state_id" value="0" type="hidden">
                                        <input name="status_id" value="1" type="hidden">

                                        <?php if(isset($get_share_by_id)): ?>

                                        <input name="acc_id" value="<?php echo $get_share_by_id['id']; ?>" type="hidden">
                                        
                                        <?php endif; ?>

                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <i class="fa fa-print"></i>
                                        </button>
                                        </form>
                                        
                                    </span>
                                `;

                                return display_btn + btn_receipt;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $share_privilege)){ ?> getBtnConfig('Shares Transactions'), <?php } else { echo "[],"; } ?>
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