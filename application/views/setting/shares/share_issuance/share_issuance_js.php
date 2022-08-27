if ($("#tblShare_issuance").length && tabClicked === "tab-share_issuance") {
                if (typeof (dTable['tblShare_issuance']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-share_issuance").addClass("active");
                    dTable['tblShare_issuance'].ajax.reload(null, true);
                } else {
                    dTable['tblShare_issuance'] = $('#tblShare_issuance').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
                            "url": "<?php echo site_url('Share_issuance/jsonList'); ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function(d){
                             d.status_id ='1';
                            }
                         },
                        "columnDefs": [{
                                "targets": [6],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'id', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('Share_issuance/view'); ?>/" + full.id + "' title='View Share issuance details'> "+full.issuance_name+" </a>";
                                }
                            },
                            {data:'issuance_code', render:function( data,type,full,meta){
                                if(data =='null'){
                                    var ic = 'No Code';
                                    return ic;
                                }
                                else{
                                     
                                    return full.issuance_code;
                                }
                            }},
                            {data: 'share_to_issue'},
                            { "data": "price_per_share", render:function ( data, type, full, meta ){
                             return "UGX "+curr_format(data*1);} },

                            {data: 'date_of_issue', render: function (data, type, full, meta) {
                            return data ? moment(data, 'YYYY-MM-DD').format('DD-MMM-YYYY') : '';
                            }},
                            {data: 'min_shares'},
                            {data:'share_account'},
                           
                            {data: 'status_id', render:function ( data, type, full, meta ) {
                            if(data==="1"){
                                var stat="Active";
                                return '<a href="#" role="button" class="btn btn-flat btn-success btn-xs" >'+ stat +'</a>';
                            } else{
                                var stat ="Deactivated";
                                return '<a href="#" role="button" class="btn btn-flat btn-danger btn-xs" >'+ stat +'</a>';
                            }
                           
                        }},
                            {data: 'id', render: function (data, type, full, meta) {
                               var display_btn = "<div class='btn-grp'>";
                            <?php if(in_array('3', $share_issuance_privilege)){ ?>
                                display_btn += "<a href='#add_share_issuance-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Edit Share Details'><i class='fa fa-edit'></i></a>";
                            <?php } if(in_array('7', $share_issuance_privilege)){ ?>
                               

                             <?php }  if(in_array('7', $share_issuance_privilege)){ ?>
                                display_btn += '<a href="#" style="padding-left:10px;" title="Delete  record"><span class="fa fa-trash text-danger delete_me"></span></a>';
                             <?php } ?>
                                display_btn += "</div>";
                                return display_btn; 
                                }
                            }
                        ],
                        "buttons": <?php if(in_array('6', $share_issuance_privilege)){ ?> getBtnConfig('Share Issuance'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
