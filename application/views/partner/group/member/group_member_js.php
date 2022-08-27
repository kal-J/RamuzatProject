if ($("#tblGroup_member").length && tabClicked === "tab-membership") {
        if (typeof (dTable['tblGroup_member']) !== 'undefined') {
                    $("#tab-savings").removeClass("active");
                    $("#tab-loans").removeClass("active");
            $("#tab-membership").addClass("active");
            dTable['tblGroup_member'].ajax.reload(null, true);
        } else {
                 dTable['tblGroup_member'] = $('#tblGroup_member').DataTable({
                    dom: '<"html5buttons"B>lTfgitp',
                    "deferRender": true,
                    "order": [[0, 'desc']],
                    "ajax": {
                        "url": "<?php echo site_url("group_member/jsonList"); ?>",
                        "dataType": "JSON",
                        "type": "POST",
                        "data": function (d) {
                            d.group_id = <?php echo (int) $group['id']; ?>;
                            d.status = 1;
                        }
                    },
                    "columnDefs": [{
                            "targets": [0, 3],
                            "orderable": false,
                            "searchable": false
                        }, {
                            "targets": [0],
                            "orderable": false
                        }],
                    columns: [
                        {data: 'id', render: function (data, type, full, meta) {
                                return "<a href='<?php echo site_url("group_member/view"); ?>/" + data + "' title='Click to view full details'>" + data + "</a>";
                            }},
                        {data: 'member_name', render: function (data, type, full, meta) {
                                return "<a href='<?php echo site_url("member/member_personal_info"); ?>/" + full.member_id + "' title='Click to view full details'>" + data +"</a>";
                            }},
                        {data: 'real_bal'},
                        {data: 'group_leader', render: function (data, type, full, meta) {
                                if(data && parseInt(data)===1) {
                                    return "Group Leader";
                                }
                                return "Ordinary member";
                            }},
                        {data: 'id', render: function (data, type, full, meta) {
                                var anchor_class = (full.member_count ? 'change_status' : 'delete_me');
                                var itag_class = (full.member_count ? 'power-off' : 'trash');
                                var anchor_title = (full.member_count ? 'Deactivate' : 'Delete');
                                var ret_txt ="";
                                <?php if((in_array('5', $group_privilege))||(in_array('7', $group_privilege))){ ?>
                                 ret_txt += "<a href='#' class='btn btn-sm btn-default " + anchor_class + "' title='" + anchor_title + "'><i class='fa fa-" + itag_class + " text-danger'></i></a>";
                                <?php } ?>
                                return ' <div class="btn-group">' + ret_txt + '</div>';
                            }
                        }
                    ],
                    buttons: <?php if(in_array('6', $group_privilege)){ ?> getBtnConfig('<?php echo $title; ?>'), <?php } else { echo "[],"; } ?>
                });
            }
        }