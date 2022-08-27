if ($("#tblDepositProduct").length && tabClicked === "tab-deposit_product") {
                if (typeof (dTable['tblDepositProduct']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-deposit_product").addClass("active");
                    dTable['tblDepositProduct'].ajax.reload(null, true);
                } else {
                    dTable['tblDepositProduct'] = $('#tblDepositProduct').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
                            "url": "<?php echo site_url('DepositProduct/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function(d){
                            d.status_id ='0';
                            }
                         },
                        "columnDefs": [{
                                "targets": [5],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'productname', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('DepositProduct/view'); ?>/" + full.id + "' title='View Savings product details'>" + data + "</a>";
                                }
                            },
                            {data: 'typeName'},
                            {data: 'name_av'},
                            {data: 'interestpaid', render:function ( data, type, full, meta ) {return (data==1)?"Yes ":'No'; }},
                            {data: 'status_id', render:function ( data, type, full, meta ) {
                                return (data==1)?"<div class='btn-grp'><a href='#' class='btn btn-sm change_status' title='Click to deactivate Savings Product'>Activated<i class='fa fa-check-square'></i></a></div> ":"<div class='btn-grp'><a href='#' class='btn btn-sm change_status' title='Click to activate Savings Product'>Deactivated<i class='fa fa-ban'></i></a></div>";
                            }},
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn ="";
                                     display_btn += "<div class='btn-grp'>";
                                     <?php if(in_array('3', $deposit_product_privilege)){ ?>
                                    display_btn +="<a href='<?php echo base_url(); ?>DepositProduct/view/" + full.id + "'><i style='margin-right: 15px;' class='fa fa-edit'></i></a>";
                                     <?php } if(in_array('4', $deposit_product_privilege)){ ?>
                                    display_btn += '<a href="#" title="Delete Savings Product record"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                     <?php } ?>
                                    display_btn += "</div>";
                                    return display_btn;
                                }
                            }
                        ],
                        "buttons": <?php if(in_array('6', $deposit_product_privilege)){ ?> getBtnConfig('Savings Product'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }