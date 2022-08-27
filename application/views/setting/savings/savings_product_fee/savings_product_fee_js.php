if ($("#tblSavings_product_fee").length && tabClicked === "tab-savings_product_fee") {
                if (typeof (dTable['tblSavings_product_fee']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#savings_product_fee").addClass("active");
                    dTable['tblSavings_product_fee'].ajax.reload(null, true);
                } else {
                    dTable['tblSavings_product_fee'] = $('#tblSavings_product_fee').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
                            "url": "<?php echo site_url('Savings_product_fee/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function(d){
                            d.status_id ='0',
                            d.id =<?php echo $product['id'];?>;
                            }
                         },
                        "columnDefs": [{
                                "targets": [1],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                                  {data: 'feename'},
                                  {data: 'account_name_ac'},
                                  {data: 'account_name_rec'},
                                  {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn ="";
                                <?php if(in_array('3', $deposit_product_privilege)){ ?>
                                    display_btn +="<div><a href='#add_savings_product_fee-modal' data-toggle='modal' class='btn btn-sm edit_me'><i class='fa fa-edit'></i></a>";
                                <?php }  if(in_array('7', $deposit_product_privilege)){ ?>
                                    display_btn += "<a href='#' class='btn btn-sm btn-warning change_status' title='Deactivate Deposit fee'><i class='fa fa-refresh'></i></a></div>";
                                    display_btn += "<a href='#' class='btn btn-sm btn-danger delete_me' title='Delete Deposit fee'><i class='fa fa-trash '></i></a></div>";
                            <?php } ?>
                                    return display_btn;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $deposit_product_privilege)){ ?> getBtnConfig('Savings Product Fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }