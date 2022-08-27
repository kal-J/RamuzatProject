if ($("#tblGroup_member").length && tabClicked === "tab-membership") {
        if (typeof (dTable['tblGroup_member']) !== 'undefined') {
                    $(".tab-pane").removeClass("active");
                    $("#tab-membership").addClass("active");
                    // console.log('+++++=====++++');
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
                                    group_loanModel.group_leader_present(true);
                                    return "Group Leader";
                                }
                                return "Ordinary member";
                            }},
                        {data: 'id', render: function (data, type, full, meta) {
                                var anchor_class = (full.member_count ? 'change_status' : 'delete_me');
                                var itag_class = (full.member_count ? 'power-off' : 'trash');
                                var anchor_title = (full.member_count ? 'Deactivate' : 'Delete');
                                var ret_txt ="";
                                 <?php if(in_array('3', $group_privilege)){ ?>
                       if(parseInt(full.group_leader) ===0){
                                ret_txt += '<a href="#" class="btn btn-sm btn-primary"  style="margin-right:15px;" title="Mark as Group Leader"><span class="fa fa-check-circle  mark_group"></span></a>';
                            } else {
                                ret_txt += '<a href="#" class="btn btn-sm btn-warning" style="margin-right:15px;" title="Remove Group Leader Status"><span class="fa fa-ban  unmark_group"></span></a>';
                                }
                        <?php }  if((in_array('5', $group_privilege))||(in_array('7', $group_privilege))){ ?>
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


      $('table tbody').on('click', 'tr .mark_group', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var controller = "Group_member";
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/mark_group_leader";

            change_status({id: data.id, status: 1,group_id:data.group_id}, url, tbl_id);
        });

        $('table tbody').on('click', 'tr .unmark_group', function (e) {
            e.preventDefault();
            var row = $(this).closest('tr');
            var tbl = row.parent().parent();
            var tbl_id = $(tbl).attr("id");
            var dt = dTable[tbl_id];
            var data = dt.row(row).data();
            if (typeof (data) === 'undefined') {
                data = dt.row($(row).prev()).data();
                if (typeof (data) === 'undefined') {
                    data = dt.row($(row).prev().prev()).data();
                }
            }
            var controller = "Group_member";
            var url = "<?php echo site_url(); ?>" + controller.toLowerCase() + "/unmark_group_leader";

            change_status({id: data.id, status_id: 0}, url, tbl_id);
        });