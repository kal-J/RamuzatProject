if ($("#tblCollateral_docs_setup").length && tabClicked === "tab-collateral_docs_setup") {
                if (typeof (dTable['tblCollateral_docs_setup']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-collateral_docs_setup").addClass("active");
                    dTable['tblCollateral_docs_setup'].ajax.reload(null, true);
                } else {
                    dTable['tblCollateral_docs_setup'] = $('#tblCollateral_docs_setup').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('Collateral_docs_setup/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.status_id = 1
                            }
                        },
            "columns": [
                      { "data": "collateral_type_name" },
                      { "data": "description" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt ="";
                        <?php if(in_array('3', $loan_product_privilege)){ ?>
                        ret_txt +="<a href='#add_collateral_docs_setup-modal' data-toggle='modal' title='Edit record' class='btn text-primary btn-sm  edit_me'><i class='fa fa-edit'></i></a>";
                      <?php } if(in_array('7', $loan_product_privilege)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm text-danger  change_status' data-toggle='tooltip' title='Deactivate record'><i class='fa fa-ban'></i></a>";
                      <?php } ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $loan_product_privilege)){ ?> getBtnConfig('Loan Fees'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
