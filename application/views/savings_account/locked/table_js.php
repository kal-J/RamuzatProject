if ($("#tblLock_savings").length && tabClicked === "tab-locked" ) {
                if (typeof (dTable['tblLock_savings']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-locked").addClass("active");
                    dTable['tblLock_savings'].ajax.reload(null, true);
                } else {
                    dTable['tblLock_savings'] = $('#tblLock_savings').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
							"url":"<?php echo site_url('Lock_savings/jsonList') ?>",
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
                            {data: 'account_no', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='#'>" + data + "</a>";
                                }
                            },
                            {data: 'amountcalculatedas'},
                            {data: 'locked_savings_amount', render: function (data, type, full, meta) {
                                    return curr_format(data*1);
                              
                                }},
                            {data: 'locked_date',render:function( data, type, full, meta ){
                                if (type === "sort" || type === "filter") {
                                    return data;
                                }
                                return (data)?moment(data,'YYYY-MM-DD').format('D-MMM-YYYY'):'';
                                }  
                            },
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn = "<div>";
                                <?php if(in_array('18', $savings_privilege)){ ?>
                                 display_btn += '<a href="#add_locked_amount" data-toggle="modal" title="Edit Locked Amount"><span class="fa fa-pencil text-primary edit_me"></span></a>';
                                 <?php } if(in_array('25', $savings_privilege)){ ?>
                                display_btn += '<a href="#" data-toggle="modal"   title="Unlock Amount" class="lock_savings"> &nbsp;&nbsp;<i class="fa fa-unlock"  style="color:#bf0b05"></i></a>';
                                <?php } ?>
                                display_btn += "</div>";
                                return display_btn;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $savings_privilege)){ ?> getBtnConfig('Charges'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }


$('table tbody').on('click', 'tr .lock_savings', function (e) {
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
            var controller = tbl_id.replace("tbl", "");
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/unlock";

            unlock_savings("Are you sure, you want to Unlock this Amount?", {id: data.id,acc_id: data.saving_account_id, status_id: (parseInt(data.status_id) === 3)}, url, tbl_id);
        });
