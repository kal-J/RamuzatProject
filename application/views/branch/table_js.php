if ($("#tblBranch").length && tabClicked === "tab-branch") {
                if (typeof (dTable['tblBranch']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-branch").addClass("active");
                    dTable['tblBranch'].ajax.reload(null, true);
                } else {
                    dTable['tblBranch'] = $('#tblBranch').DataTable({
                        "dom": '<"html5buttons"B>lTfgitp',
                        order: [[1, 'asc']],
                        deferRender: true,
                        ajax:{
                            "url":  "<?php echo site_url('branch/jsonList') ?>",
                            "dataType": "json",
                            "type": "POST",
                            "data": function(d){
                            d.organisation_id =<?php echo $_SESSION['organisation_id']; ?>;
                            }
                        },
                        "columnDefs": [{
                                "targets": [6],
                                "orderable": false,
                                "searchable": false
                            }],
                        columns: [
                            {data: 'branch_number', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('branch/view'); ?>/" + full.id + "' title='View branch details'>" + data + "</a>";
                                }
                            },
                            {data: 'branch_name', render: function (data, type, full, meta) {
                                    if (type === "sort" || type === "filter") {
                                        return data;
                                    }
                                    return "<a href='<?php echo site_url('branch/view'); ?>/" + full.id + "' title='View branch details'>" + data + "</a>";
                                }
                            },
                            {"data": "departments", render: function (data, type, full, meta) {
                                var ret_txt = "";
                        if (data === undefined || data.length == 0) {
                                ret_txt +="<div> <a href='<?php echo site_url('branch/view'); ?>/"+full.id+"' title='Add Departments'> <span class='badge badge-warning'> <i class='fa fa-plus'></i> Add  </span></a></div>";
                             }else{
                                        $.each (data , function(key , value) {
                                            ret_txt +="<div> <a href='<?php echo site_url('branch/view'); ?>/"+full.id+"' title='View departments'> (" + value.department_number +") " + value.department_name + "</a></div>";
                                        });
                             }
                                    return  ret_txt ;
                                }},

                            {data: 'office_phone', render: function (data, type, full, meta) {
                                    return "<a href='tel:" + data + "'>" + data + "</a>";
                                }},
                            {data: 'email_address', render: function (data, type, full, meta) {
                                    return "<a href='mailto:" + data + "'>" + data + "</a>";
                                }},
                            {data: 'physical_address'},
                            {data: 'postal_address'},
                            {data: 'id', render: function (data, type, full, meta) {
                                 var display_btn = "<div class='btn-grp'>";
                                 <?php if(in_array('3', $privileges)){ ?>
                                    display_btn += "<a href='#add_branch-modal' data-toggle='modal' class='btn btn-sm edit_me' title='Update branch details'><i class='fa fa-edit'></i></a>";
                                  <?php } if(in_array('4', $privileges)){ ?>
                                    display_btn += '<a href="#" title="Delete branch record"><span class="fa fa-trash text-danger delete_me"></span></a>';
                                    <?php } ?>
                                    display_btn += "</div>";
                                    return display_btn;
                                }
                            }
                        ],
                        buttons: <?php if(in_array('6', $privileges)){ ?> getBtnConfig('Branches'), <?php } else { echo "[],"; } ?>
                        responsive: true
                    });
                }
            }