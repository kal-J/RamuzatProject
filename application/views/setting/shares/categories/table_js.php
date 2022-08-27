if ($("#tblShare_category").length && tabClicked === "tab-share_category") {
                if (typeof (dTable['tblShare_category']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-share_category").addClass("active");
                    dTable['tblShare_category'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_category'] = $('#tblShare_category').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax:{
                                 "url": "<?php echo site_url('Share_category/jsonList') ?>",
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
                            {data: 'category', render: function (data, type, full, meta) { return "<a href='<?php echo site_url("share_category/view");?>/"+full.id+"' title='Click to assign Privileges'>"+data+"</a>";}},
                            {data: 'description'},
                            {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
                            {"data": 'status_id', render: function (data, type, full, meta) {
                                var ret_txt ="";
                                if(parseInt(full.id)===4){ } else{
                                <?php if(in_array('3', $share_issuance_privilege)){ ?>
                                ret_txt += "<a href='#add_share_category-modal' data-toggle='modal' class='btn btn-sm btn-default edit_me' title='Update category details'><i class='fa fa-edit '></i></a>";
                              <?php } if(in_array('7', $share_issuance_privilege)){ ?>
                                    var title_text = parseInt(data)===1?"De":"A";
                                    var fa_class = parseInt(data)===1?"ban":"undo";
                                    var icon_color = parseInt(data)===1?"warning":"default";
                                    ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default change_status" title="'+title_text+'ctivate category"><i class="fa fa-'+fa_class+' text-'+icon_color+'"></i></a>';
                               <?php } if(in_array('4', $share_issuance_privilege)){ ?>
                                   ret_txt += '<a href="#" data-toggle="modal" class="btn btn-sm btn-default delete_me"><i class="fa fa-trash text-danger"></i></a>';
                                <?php } ?>
                                }
                                   return ret_txt;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $share_issuance_privilege)){ ?> getBtnConfig('Share Categories'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }