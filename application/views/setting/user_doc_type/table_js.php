if ($("#tblUser_doc_type").length && tabClicked === "tab-user_doc_type") {
                if (typeof (dTable['tblUser_doc_type']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-user_doc_type").addClass("active");
                    dTable['tblUser_doc_type'].ajax.reload(null, true);
                } else {
                    dTable['tblUser_doc_type'] = $('#tblUser_doc_type').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        "ajax":{
                            "url": "<?php echo base_url('user_doc_type/jsonList/'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function (d) {
                            d.status_id = 1
                            }
                        },
            "columns": [
                      { "data": "user_doc_type" },
                      { "data": "description" },
                      { "data": "id", render:function ( data, type, full, meta ) {
                        var ret_txt="";
                        <?php if(in_array('3', $privileges)){ ?>
                        ret_txt +="<a href='#add_user_doc_type-modal' data-toggle='modal' title='Edit record' class='btn text-primary btn-sm  edit_me'><i class='fa fa-edit'></i></a>";
                      <?php } if(in_array('7', $privileges)){ ?>
                        ret_txt += "<a href='#' data-toggle='modal' class='btn btn-sm text-danger  change_status' data-toggle='tooltip' title='Deactivate record'><i class='fa fa-ban'></i></a>";
                      <?php } ?>
                        return ret_txt;
                      }}
                   ],
                   buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('User Document types'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
