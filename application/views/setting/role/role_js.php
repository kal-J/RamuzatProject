if ($("#tblRole").length && tabClicked === "tab-role") {
                if (typeof (dTable['tblRole']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-role").addClass("active");
                    dTable['tblRole'].ajax.reload(null, true);
                } else {
                    dTable['tblRole'] = $('#tblRole').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax:{
                                 "url":  "<?php echo site_url('role/jsonList') ?>",
                                 "dataType": "json",
                                 "type": "POST",
                                 "data": function(d){
                                  d.status_id ='0';
                                  }
                                  },
                        "columnDefs": [{
                                "targets": [1],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'role', render: function (data, type, full, meta) { return "<a href='<?php echo site_url("rolePrivilege/view");?>/"+full.id+"' title='Click to assign Privileges'>"+data+"</a>";}},
                            {data: 'description'},
                            {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
                            {"data": 'status_id', render: function (data, type, full, meta) {
                                var ret_txt ="";
                                if(parseInt(full.id)===4){ } else{
                                <?php if(in_array('3', $role_privilege)){ ?>
                                ret_txt += "<a href='#add_role-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me' title='Update role details'><i class='fa fa-edit '></i></a>";
                              <?php } if(in_array('7', $role_privilege)){ ?>
                                    var title_text = parseInt(data)===1?"De":"A";
                                    var fa_class = parseInt(data)===1?"ban":"undo";
                                    var icon_color = parseInt(data)===1?"warning":"default";
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status" title="'+title_text+'ctivate role"><i class="fa fa-'+fa_class+' text-'+icon_color+'"></i></a>';
                               <?php } if(in_array('4', $role_privilege)){ ?>
                                   ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                                <?php } ?>
                                }
                                   return ret_txt;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $role_privilege)){ ?> getBtnConfig('System Roles'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }