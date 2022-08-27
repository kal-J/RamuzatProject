if ($("#tblModules").length && tabClicked === "tab-modules") {
                if (typeof (dTable['tblModules']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-modules").addClass("active");
                    dTable['tblModules'].ajax.reload(null, true);
                } else {
                    dTable['tblModules'] = $('#tblModules').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        ajax:{
                                 "url":  "<?php echo site_url('modulePrivilege/jsonList') ?>",
                                 "dataType": "json",
                                 "type": "POST",
                                 "data": function(d){

                                  d.status_id ='1';
                                  }
                                  },
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'module_name', render: function (data, type, full, meta) { return "<a href='<?php echo site_url("modulePrivilege/view");?>/"+full.id+"' title='Click to view full details'>"+data+"</a>";}},
                            {data: 'description'},
                            {data: 'status_id', render:function ( data, type, full, meta ) {return (data==1)?"Active ":'Deactivated'; }},
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Module and Privileges'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }