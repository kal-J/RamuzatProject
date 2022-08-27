         if ($('#tblNextOfKin').length && tabClicked === "tab-kin") {
                if(typeof(dTable['tblNextOfKin'])!=='undefined'){
                    $(".biodata").removeClass("active");
                    $("#tab-kin").addClass("active");
                    $("#tab-biodata").addClass("active");
                    //dTable['tblNextOfKin'].ajax.reload(null,true);
                }else{
                    dTable['tblNextOfKin'] =
                        $('#tblNextOfKin').DataTable({
                    "pageLength": 25,
                    "searching": false,
                    "paging": false,
                    "responsive": true,
                    "dom": '<"html5buttons"B>lTfgitp',
                    buttons: <?php if(in_array('6', $member_staff_privilege)){ ?> getBtnConfig('<?php echo $title; ?>-Next of Kin'), <?php } else { echo "[],"; } ?>
                    "ajax": {
                        "url": "<?php echo site_url('NextOfKin/jsonList'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){d.user_id = <?php echo $user['user_id'] ?>;}
                    },
                    "footerCallback": function (tfoot, data, start, end, display) {
                        var api = this.api();
                            display_footer_sum(api,[3]);
                    },
                    "columns": [
                        {"data": "firstname", render: function (data, type, full, meta) {
                                return data + " " + full.lastname;
                            }},

                        {"data": "gender"},
                        {"data": "relationship_type"},
                        {"data": "share_portion", render: function (data, type, full, meta) {
                                return (data*1)+'%';
                        }},
                        {"data": "address"},
                        {"data": "telphone"},
                        {"data": "id", render: function (data, type, full, meta) {
                            var ret_txt ="";
                              <?php if(in_array('3', $member_staff_privilege)){ ?>
                                ret_txt += "<div class='btn-grp'><a href='#add_nextofkin-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update next of kin details'><i class='fa fa-edit'></i></a>";
                                <?php } if(in_array('4', $member_staff_privilege)){ ?>
                                ret_txt += '<a href="#" data-toggle="modal"   title="Update next of kin details" class="delete_me"> &nbsp;&nbsp;<i class="fa fa-trash"  style="color:#bf0b05"></i></a>';
                                <?php } ?>
                                return ret_txt;
                            }}
                    ]

                });
                }
            }
