if ($("#tblOrganisation").length && tabClicked === "tab-organisation") {
                if (typeof (dTable['tblOrganisation']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-organisation").addClass("active");
                    dTable['tblOrganisation'].ajax.reload(null, true);
                } else {
                    dTable['tblOrganisation'] = $('#tblOrganisation').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
                            "url":"<?php echo site_url('Organisation/jsonList') ?>",
                            "dataType":"json",
                            "type":"POST",
                            "data":function(e){
                                e.status_id = '1';
                            }
                        },
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                        {data: 'name', render: function (data, type, full, meta) {
                            return "<a href='<?php echo site_url('organisation/settings'); ?>/" + full.id + "' title='View organisation details'>" + data + "</a>";
                        }
                        },
                        { data: 'org_initial'},
                        { data: 'description'},
                        {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
                        { data: 'id', render: function(data, type, full, meta) {
                            var display_btn = "<div class='btn-grp'>";
                            <?php if(in_array('3', $privileges)){ ?>
                                display_btn += "<a href='#organisation-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Organisation details'><i class='fa fa-edit'></i></a>";
                            <?php } if(in_array('7', $privileges)){ ?>
                                display_btn += '<a href="#" title="change status of the record"><span class="fa fa-refresh text-warning change_status"></span></a> &nbsp;&nbsp;';
                            <?php } if(in_array('4', $privileges)){ ?>
                              display_btn += '<a href="#" title="Delete organisation and all records"><span class="fa fa-trash text-danger delete_me"></span></a>'; 
                             <?php } ?>
                                 display_btn += "</div>";
                                return display_btn; 
                            }
                        }
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Organisations'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }
