if ($("#tblDepartment").length && tabClicked === "tab-department") {
                if (typeof (dTable['tblDepartment']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-department").addClass("active");
                    dTable['tblDepartment'].ajax.reload(null, true);
                } else {
                    dTable['tblDepartment'] = $('#tblDepartment').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax: {
                            "url": "<?php echo site_url('department/jsonList') ?>",
                            "dataType": "JSON",
                            "type": "POST"
                        },
                        "columnDefs": [{
                                "targets": [2],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'department_number', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('department/view'); ?>/" + full.id + "' title='View department details'>" + data + "</a>";
                                }
                            },
                            {data: 'department_name', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('department/view'); ?>/" + full.id + "' title='View department details'>" + data + "</a>";
                                }
                            },
                            {data: 'id', render: function (data, type, full, meta) {
                                var display_btn ="";
                                display_btn += "<div class='btn-grp'>";
                                <?php if(in_array('3', $privileges)){ ?>
                                    display_btn += "<a href='#add_department-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update department details'><i class='fa fa-edit'></i></a>";
                                   <?php } if(in_array('4', $privileges)){ ?>
                                    display_btn += '<a href="#" title="Delete department record"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                    <?php } ?>
                                    display_btn += "</div>";
                                    return display_btn;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Departments'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }