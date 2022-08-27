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
                    buttons: getBtnConfig('<?php echo $title; ?>-Next of Kin'),
                    "ajax": {
                        "url": "<?php echo site_url('NextOfKin/jsonList'); ?>",
                        "dataType": "json",
                        "type": "POST",
                        "data": function(d){d.user_id = <?php echo $user['user_id'] ?>;}
                    },
                    "columns": [
                        {"data": "firstname", render: function (data, type, full, meta) {
                                return data + " " + full.lastname;
                            }},

                        {"data": "gender"},
                        {"data": "relationship_type"},
                        {"data": "address"},
                        {"data": "telphone"}
                    ]

                });
                }
            }
